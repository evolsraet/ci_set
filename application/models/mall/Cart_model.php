<?php defined('BASEPATH') OR exit('No direct script access allowed');

// ** 추가할것
// 30일 이전 비회원 장바구니 삭제

class Cart_model extends MY_Model {

	public $prefix = 'cart_';
	public $carts_item = array();
	public $carts = array();

	public function __construct()
	{
		parent::__construct();

		$this->protected[] = $this->primary_key;

		$this->created = true;
		$this->updated = true;

		// 연결
		$this->before_get[] = 'login_check_where';
		$this->after_get[] = 'after_get';
		$this->before_delete[] = 'login_check_where';
		$this->before_create[] = 'login_check_data';
		$this->before_update[] = 'login_check_data';

		// before_create
		// after_create
		// before_update
		// after_update
		// before_get
		// after_get
		// before_delete
		// after_delete
	}

	private function get_session_id() {
		if( $this->session->userdata('cart_session_id') ) :
			$session_id = $this->session->userdata('cart_session_id');
		else:
			$session_id = session_id();
			$this->session->set_userdata('cart_session_id', $session_id);
		endif;

		return $session_id;
	}

	// 항상 최신 데이터로 가져온다 ->product ->options
	public function after_get($data) {
		$this->load->model('mall/product_model');
		$this->load->model('mall/option_model');

		// kmh_print($data);

		if( !$data->cart_id )
			throw new Exception("장바구니 항목이 없습니다.", 1);

		$data->_cart_checkbox = true;
		$data->cart_item = json_decode($data->cart_item);
		$data->product = $this->product_model->get( $data->cart_item->op_pd_id );

		$this->load->library('files');
		$data->product->front_image = $this->files->front_image('product', $data->product->pd_id, 'pd_img', 500, 400);

		$data->cart_item->op_price_one = $data->product->pd_price;
		$data->cart_item->op_price = 0;

		$data->product->op_options = array();
		foreach( (array) json_decode( $data->cart_item->op_options ) as $key => $row ) :
			$option_db = $this->option_model
								->where('ot_pd_id', $data->product->pd_id)
								->where('ot_type', $row->ot_type)
								->where('ot_name', $row->ot_name)
								->get();
			if( $option_db ) :
				$data->product->op_options[] = $option_db;
				$data->cart_item->op_price_one += $option_db->ot_price;
			endif;
		endforeach;

		$data->cart_item->op_price = $data->cart_item->op_price_one * $data->cart_item->op_count;

		return $data;
	}

	public function login_check_where() {
		$session_id = $this->get_session_id();

		if( $this->members->is_admin() ) :
			// 관리자는 모두 조회
		elseif( $this->members->is_login() ) :
			$this->where( 'cart_mb_id', $this->logined->mb_id );
			$this->or_where( 'cart_session_id', $session_id );
		else:
			$this->where( 'cart_session_id', $session_id );
		endif;

		return $this;
	}

	public function login_check_data($data) {
		$data->cart_item = json_encode($data->cart_item);

		if( $this->members->is_login() ) :
			$data->cart_mb_id = $this->logined->mb_id;
		else:
			$data->cart_session_id = $this->get_session_id();
			$this->session->set_userdata('cart_session_id', $data->cart_session_id);
		endif;

		if( !$data->cart_mb_id && !$data->cart_session_id )
			throw new Exception("login_check_data 에러발생", 1);
			// die('login_check_data');

		return $data;
	}

	// 아이템 설정 후 가격 계산 = 카트에 담긴 after_get 기준으로 계산
		public function set_carts($array = array()) {
			$this->carts_item = $array;
			$this->carts = $this->where_in('cart_id', $array)->get_all();
		}

		// 배송비
		public function deli_price() {
			$this->load->model('config_model');

			if( count($this->carts) )
				return $this->config_model->get_value('deli_price');
			else
				return 0;
		}

		// 제품 총 가격
		public function pd_price() {
			$pd_price = 0;
			foreach( (array) $this->carts as $key => $cart ) :
				$price_one = 0;
				$price = 0;

				// 기본가
				$price_one += $cart->product->pd_price;
				foreach( (array) $cart->product->op_options as $option ) :
					// 옵션가
					$price_one += $option->ot_price;
				endforeach;

				$price = $price_one * $cart->cart_item->op_count;
				$pd_price += $price;
			endforeach;

			return $pd_price;
		}

		public function total_price() {
			$deli_price = $this->deli_price();
			$pd_price = $this->pd_price();
			return $deli_price + $pd_price;
		}
}
