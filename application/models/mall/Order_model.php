<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Order_model extends MY_Model {

	public $prefix = 'order_';
	public $point_use_data = null;

	public function __construct()
	{
		parent::__construct();

		$this->protected[] = $this->primary_key;

		$this->has_many['order_log'] = array('order_log','ol_order_id','order_id');
		$this->has_many['order_product'] = array('order_product_model','op_order_id','order_id');

		$this->created = true;
		$this->updated = true;
		$this->soft_delete = false;

		// 연결

		$this->before_create[] = 'tel_encode';
		$this->before_create[] = 'point_use';
		$this->before_create[] = 'data_confirm';

		$this->before_update[] = 'tel_encode';
		$this->before_update[] = 'check_auth_common';
		$this->before_update[] = 'check_auth_update';
		$this->before_update[] = 'point_update';

		$this->before_get[]    = 'before_get';
		$this->before_get[]    = 'check_auth_common';

		$this->after_create[]  = 'point_use_after';


		$this->before_delete[] = 'check_auth_common';

		$this->after_get[]     = 'tel_decode';

		// before_create
		// after_create
		// before_update
		// after_update
		// before_get
		// after_get
		// before_delete
		// after_delete
	}

	public function point_use($data) {
		if( $data->order_point_use && $data->order_mb_id ) :

			// 최대금액 (최조가격 < 포인트사용액)
			if( $data->order_total_price < $data->order_point_use )
				$data->order_point_use = $data->order_total_price;

			// 데이터 구성
			$point = new stdClass;
			$point->pt_mb_id  = $data->order_mb_id;
			$point->pt_amount = $data->order_point_use * -1;
			$point->pt_desc   = "주문 포인트 사용";

			$this->point_use_data = $point;
		endif;

		return $data;
	}

	public function point_use_after($key) {
		if( $this->point_use_data->pt_amount ) :
			$this->load->model('point_model');
			$this->point_use_data->pt_desc .= " #{$key}";
			$this->point_use_data->pt_rel_id = "order_use_{$key}";
			$this->point_model->insert( $this->point_use_data );

			$this->point_use_data = null;
		endif;

		return $key;
	}

	// before_update 포인트 추가
	public function point_update($data) {
		$this->load->model('config_model');

		// 기존 주문서 조회 : qb 유지한채
		$order_db = $this->qb_cache()->get();
		// 적립율
		$order_point_per = $this->config_model->get_value('order_point_per');

		if( $order_db->order_mb_id && $order_point_per ) :
			$this->load->model('point_model');

			// 상태가 다른것에서 현재상태로 바뀔때
			if( $order_db->order_status != $data->order_status ) :

				switch ($data->order_status) {
					case '500_complete':
						// 완료되는 시점에 포인트 지급
						$point = new stdClass;
						$point->pt_rel_id = "order_add_{$order_db->order_id}";
						$point->pt_mb_id  = $order_db->order_mb_id;
						$point->pt_amount = $order_db->order_total_price * ( $order_point_per / 100 );
						$point->pt_desc   = "주문 포인트 적립 #" . $order_db->order_id;

						if( $point->pt_amount > 1 && $this->point_model->check_pt_rel_id($point->pt_rel_id) ) :
							if( !$this->point_model->insert($point) )
								throw new Exception("포인트 적립 중 에러가 발생했습니다.", 1);

							add_flashdata('page_notice', "회원에게 ".number_format($point->pt_amount)."포인트가 적립되었습니다.<br>주문취소 시 회수되지만, 잔여포인트가 0보다 작을 수 있습니다.");
						endif;

						break;
					case '900_cancel':
						// 사용된 포인트 복구
						if( $order_db->order_point_use ) :
							$point = new stdClass;
							$point->pt_rel_id = "order_use_cancel_{$order_db->order_id}";
							$point->pt_mb_id  = $order_db->order_mb_id;
							$point->pt_amount = $order_db->order_point_use;
							$point->pt_desc   = "주문 취소 포인트 복구 #" . $order_db->order_id;

							if( $point->pt_amount > 1 && $this->point_model->check_pt_rel_id($point->pt_rel_id) ) :
								if( !$this->point_model->insert($point) )
									throw new Exception("포인트 복구 중 에러가 발생했습니다.", 1);
								add_flashdata('page_notice', "주문 시 사용된 ".number_format($point->pt_amount)." 포인트가 복구되었습니다.");
							endif;
						endif;

						// 적립된 포인트 차감
						$added_db = $this->point_model->where('pt_rel_id', "order_add_{$order_db->order_id}")->get();
						if( $added_db->pt_amount ) :
							$point = new stdClass;
							$point->pt_rel_id = "order_add_cancel_{$order_db->order_id}";
							$point->pt_mb_id  = $order_db->order_mb_id;
							$point->pt_amount = ($added_db->pt_amount * -1);
							$point->pt_desc   = "포인트 적립 취소 #" . $order_db->order_id;

							if( $this->point_model->check_pt_rel_id($point->pt_rel_id) ) :
								if( !$this->point_model->allow_minus()->insert($point) )
									throw new Exception("포인트 복구 중 에러가 발생했습니다.", 1);
								add_flashdata('page_notice', "주문 시 적립된 ".number_format(abs($point->pt_amount))." 포인트가 차감되었습니다.");
							endif;
						endif;

						break;
					default:
						break;
				} // switch
			endif; // 상태변경
		endif; // 포인트 사용유무

		return $data;
	}

	public function tel_decode($data) {
		if( $data->order_tel ) :
			$data->order_tel = add_hyphen($data->order_tel);
		endif;
		return $data;
	}

	public function tel_encode($data) {
		if( $data->order_tel ) :
			$data->order_tel = only_number( $data->order_tel );
		endif;

		return $data;
	}

	public function data_confirm($data) {
		if( !$data->order_name || !$data->order_tel ) :
			throw new Exception("주문자 정보가 없습니다.", 1);
		endif;

		return $data;
	}

	public function before_get($data) {
		$this->order_by('order_status', 'ASC');
		$this->order_by('order_created_at', 'DESC');
		$this->join('member','mb_id=order_mb_id','left outer');
		return $data;
	}

	public function check_auth_common( $data ) {
		if( $this->members->is_admin() ) :
		elseif( $this->members->is_login() ) :
			// $this->where('order_status', '100_ask');
			$this->where('order_mb_id', $this->logined->mb_id);
		else:
			$this->where('order_mb_id', null);
			$this->where('order_name', $_SESSION['guest_order_search_1'] );
			$this->where('order_tel', $_SESSION['guest_order_search_2'] );
		endif;

		return $data;
	}

	public function check_auth_update( $data ) {
		if( $this->members->is_admin() ) :
		elseif( $this->members->is_login() ) :
			$this->where('order_status', '100_ask');
			// $this->where('order_mb_id', $this->logined->mb_id);
		else:
			$this->where('order_status', '100_ask');
			$this->where('order_mb_id', null);
			$this->where('order_name', $_SESSION['guest_order_search_1'] );
			$this->where('order_tel', $_SESSION['guest_order_search_2'] );
		endif;

		if( !$this->qb_cache()->count_by() )
			throw new Exception("권한이 있는지 새로고침 후 확인해주세요.", 1);


		return $data;
	}

}
