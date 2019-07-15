<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Point_model extends MY_Model {
	public $prefix = 'pt_';
	public $allow_minus = false;	// 마이너스 적립 가능 (삽입 후 리셋됨)


	public function __construct()
	{
		parent::__construct();

		$this->protected[] = $this->primary_key;
		$this->created = true;

		$this->before_get[] = 'before_get';
		$this->before_create[] = 'before_create';
		$this->after_create[] = 'after_create';

		// before_create
		// after_create
		// before_update
		// after_update
		// before_get
		// after_get
		// before_delete
		// after_delete
	}

	public function allow_minus() {
		$this->allow_minus = true;
		return $this;
	}

	// 간편 추가
	public function add( $pt_mb_id, $pt_amount, $pt_desc=null, $pt_rel_id=null, $check_pt_rel_id = true ) {
		// 중복 체크 여부 확인 check_pt_rel_id
		if( $check_pt_rel_id && !$this->check_pt_rel_id($pt_rel_id) ) :
			return false;
		endif;

		$point = new stdClass;
		$point->pt_mb_id  = $pt_mb_id;
		$point->pt_amount = abs($pt_amount);
		$point->pt_desc   = $pt_desc;
		$point->pt_rel_id = $pt_rel_id;

		return $this->insert($point);
	}

	// 간편 삭제
	public function minus( $pt_mb_id, $pt_amount, $pt_desc=null, $pt_rel_id=null ) {
		$point = new stdClass;
		$point->pt_mb_id  = $pt_mb_id;
		$point->pt_amount = abs($pt_amount) * -1;
		$point->pt_desc   = $pt_desc;
		$point->pt_rel_id = $pt_rel_id;

		return $this->insert($point);
	}

	// pt_rel_id 중복 검사
	public function check_pt_rel_id($pt_rel_id) {
		if( $pt_rel_id ) :
			$count = $this->where('pt_rel_id', $pt_rel_id)->count_by();

			if( $count )
				return false;
			else
				return true;
		endif;
	}

	public function before_create($data) {
		$this->load->model('member_model');

		if( !$data->pt_amount )
			throw new Exception("포인트 액수가 누락되었습니다.", 1);

		// 회원 디비
		if( !$mb_db = $this->member_model->get($data->pt_mb_id) )
			throw new Exception("일치하는 회원정보가 없습니다.", 1);

		// 강제가 아닐때 && 사용 (마이너스) && 잔여포인트보다 큰 액수를 쓰려고 할때
		// 잔여포인트 확인 (포인트 사용시)
		if(	!$this->allow_minus
			&& $data->pt_amount < 0
			&& $mb_db->mb_point < abs($data->pt_amount)
		)
			throw new Exception("포인트가 부족합니다.", 1);

		// 잔여포인트
		$data->pt_left_point = $mb_db->mb_point + $data->pt_amount;

		// 회원 업데이트
		if( !$this->member_model->set('mb_point',$data->pt_left_point)->update($data->pt_mb_id) )
			throw new Exception("회원 정보갱신에 실패했습니다.", 1);

		return $data;
	}

	public function after_create($key) {
		// 초기화
		$this->allow_minus = false;
		// return $key;
	}

	public function before_get() {
		// 권한
		if( $this->members->is_admin() ) :
		else:
			$this->where('pt_mb_id', $this->logined->mb_id);
		endif;

		// 검색
		if( $this->input->get('search') ) :
			$this->group_start();
				$this->or_like( $this->members->auth_field, $this->input->get('search') );
				$this->or_like( 'pt_desc', $this->input->get('search') );
			$this->group_end();
		endif;

		// 정렬
		$this->join('member', 'mb_id = pt_mb_id');
		$this->order_by('pt_id', 'DESC');
	}

	public function get_list() {
		$this->before_get();
		$result['total'] = $this->count_by();

		$paging_query = $this->paging(
							10,
							$result['total'],
							$this->input->get('page')
						);

		$result['pagination'] = $paging_query->pagenation();
		$result['list']       = $paging_query->get_all();

		return $result;
	}

}
