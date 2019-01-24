<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mall extends MY_Controller {

	public $comment_type = 'product';
	public $board_base = '/board/product/';
	public $comment_auth = true;
	public $post_id = null;
	public $mall_base_url = '/mall';

	public function __construct() {
		parent::__construct();

		$this->load->model('mall/product_model');
		$this->load->model('mall/option_model');
		$this->load->model('mall/cart_model');
		$this->load->model('mall/category_model');
		$this->load->model('mall/order_model');
		$this->load->model('mall/order_product_model');
		$this->load->model('mall/order_log_model');

		// 코멘트 용
		$this->load->helper('board_helper');
		$this->post_id = $this->uri->segment(3);

		$this->load->helper('mall_helper');

		$this->css[] = VIEWDIR.'mall/assets/mall.less';
		$this->javascript[] = VIEWDIR.'mall/assets/mall.js';

		$this->comment_auth = $this->members->is_login();
		$this->mall_base_url = '/' . $this->uri->segment(1);
	}

	public function index() {
		$this->lists();
	}

	public function test() {
		$this->load->model( 'test_model' );
		$this->db->trans_start();
			echo "A";
			echo $id = $this->test_model->insert(array('test_varchar'=>'A'));
			echo "D" . $this->test_model->update($id, array('test_varchar'=>'D'));
		$this->db->trans_complete();

		kmh_print( "trans_status : " . $this->db->trans_status() );
	}

	public function lists() {
		$nav_sub = $this->config->item('nav_sub');
		$this->page_name = $nav_sub['mall']['lists'];

		$this->data = array_merge(
			$this->data,
			$this->product_model->where('pd_use', 1)->get_list()
		);

		$this->_render('mall/list');
	}

	public function view( $pd_id ) {
		$nav_sub = $this->config->item('nav_sub');
		$this->page_name = $nav_sub['mall']['lists'];

		if( !$pd_id )
			show_error('정상적인 접근이 아닙니다.');

		try {
			$this->data['view'] = $this->product_model
										->join('category', 'cate_id = pd_cate_id', 'left outer')
										->get($pd_id);

			if( !$this->data['view'] )
				show_error('해당 상품이 존재하지 않습니다.');
		} catch (Exception $e) {
			show_error( $e->getMessage() );

		}

		$this->_render('mall/view');
	}

	public function begin_order() {
		if( !$this->input->is_ajax_request() ) exit;

		try {
			if( $this->members->is_admin() )
				throw new Exception("관리자는 구매 프로세스 이용이 불가합니다.", 1);

			$product_data = new stdClass;

			$result['status'] = 'fail';
			$result['msg'] = '에러가 발생했습니다.';

			$post_data = $this->input->post();

			if( !$post_data['pd_id'] )
				throw new Exception("제품 아이디가 누락되었습니다.", 1);

			// 제품 가져오기
				$product = $this->product_model->get($post_data['pd_id']);
				if( !$product )
					throw new Exception("정상적인 제품이 아닙니다.", 1);


			// 옵션 검증
				// $post_data['_options'] = json_decode($post_data['_options']);
				// // $post_data['options_price'] = 0;
				// foreach( (array) $post_data['_options'] as $key => $row ) :
				// 	$option_db = $this->option_model
				// 					->where( 'ot_pd_id', $product->pd_id )
				// 					->where( 'ot_type', $row->ot_type )
				// 					->where( 'ot_name', $row->ot_name )
				// 					->count_by();
				// 	if( !$option_db )
				// 		throw new Exception("존재하지 않는 옵션입니다. 확인 후 다시 시도해주세요.", 1);

				// 	// $post_data['options'][] = $option_db;
				// 	// $post_data['options_price'] += $option_db->ot_price;
				// endforeach;

			// 주문 타입 분기
				if( $post_data['_order_type']=='buy' ) :
					$result['type'] = $post_data['_order_type'];
					$result['url'] = $this->mall_base_url . '/order_write';
				else :
					$result['type'] = 'cart';
					$result['url'] = $this->mall_base_url . '/cart';
				endif;


			// 제품 정보
				$product_data->op_pd_id = $product->pd_id;
				$product_data->op_count = $post_data['buy_count'];
				// $product_data->op_price_one = $post_data['options_price'] + $product->pd_price;
				// $product_data->op_price = $product_data->op_price_one * $product_data->op_count;
				$product_data->op_options = $post_data['_options'];

			// 카트에 입력
				$cart = new stdClass;
				$cart->cart_item = $product_data;

				if( !$id = $this->cart_model->insert( $cart ) )
					throw new Exception("장바구니 입력 중 에러가 발생했습니다.", 1);

			$result['status'] = 'ok';
			$result['msg'] = '정상 처리되었습니다.';
		} catch (Exception $e) {
			$result['status'] = 'fail';
			$result['msg'] = $e->getMessage();
		}

		kmh_json( $result );
	}

	public function cart() {
		$this->page_name = '장바구니';
		if( $this->members->is_admin() ) :
			redirect('/');
		endif;
		$this->data['carts'] = $this->cart_model->get_all();
		$this->under_ie9_error = true;
		$this->_render('mall/cart_vue');
	}

	public function cart_data() {
		$this->load->model('config_model');

		$result['carts'] = $this->cart_model->order_by('cart_created_at', 'DESC')->get_all();
		$result['deli_price'] = (int)$this->config_model->get_value('deli_price');

		$this->_render( $result, 'JSON' );
	}

	public function cart_update() {
		$result = array();
		$result['status'] = 'fail';
		$result['msg'] = '에러가 발생했습니다.';

		// 글작성
		try {
			$post = (object) $this->input->post();
			if( !$post->cart_id || !$db = $this->cart_model->get($post->cart_id) )
				throw new Exception("해당 장바구니 항목이 없습니다. 다시 확인해주세요.", 1);

			// 항목수정
			$db->cart_item->op_count = $post->cart_item_op_count;
			$db->cart_item->op_price = $db->cart_item->op_count * $db->cart_item->op_price_one;

			$update = new stdClass;
			$update->cart_item = $db->cart_item;

			$result['update'] = $update;

			if( !$this->cart_model->update($post->cart_id, $update) )
				throw new Exception("업데이트 중 에러가 발생했습니다. 다시 확인해주세요.", 1);

			$result['status'] = 'ok';
			$result['msg'] = '장바구니가 수정되었습니다.';
		} catch (Exception $e) {
			$result['status'] = 'fail';
			$result['msg'] = $e->getMessage();
		}
		// End of 글작성

		kmh_json($result);
	}

	public function cart_delete() {
		$result = array();
		$result['status'] = 'fail';
		$result['msg'] = '에러가 발생했습니다.';

		try {
			$post = (object) $this->input->post();
			if( !$post->cart_id || !$db = $this->cart_model->get($post->cart_id) )
				throw new Exception("해당 장바구니 항목이 없습니다. 다시 확인해주세요.", 1);

			if( !$this->cart_model->delete($post->cart_id) )
				throw new Exception("삭제 중 에러가 발생했습니다. 다시 확인해주세요.", 1);

			$result['status'] = 'ok';
			$result['msg'] = '장바구니에서 항목이 삭제되었습니다.';
		} catch (Exception $e) {
			$result['status'] = 'fail';
			$result['msg'] = $e->getMessage();
		}

		kmh_json($result);
	}

	public function order( $order_id = null ) {
		$this->page_name = "주문서";
		$this->data['order_id'] = $order_id;

		if( $this->uri->segment(1) == 'admin' && !$order_id )
			$render_type = 'PANEL';
		else
			$render_type = 'FULLPAGE';

		if( $order_id ) :
			try {
				// 조회
				$this->data['view'] = $this->order_model->with('order_product')->get($order_id);
				if( !$this->data['view'] )
					throw new Exception("해당 주문서가 존재하지 않습니다.", 1);
			} catch (Exception $e) {
				show_error( $e->getMessage() );
			}

			$this->data['form_view_mode'] = true;
			$this->_render('mall/order_write');
		else:
			if( !$this->members->is_login() ) :
				if($this->input->get('guest_order_search_1'))
					$_SESSION['guest_order_search_1'] = $this->input->get('guest_order_search_1');
				if($this->input->get('guest_order_search_2'))
					$_SESSION['guest_order_search_2'] = $this->input->get('guest_order_search_2');
			endif;

			$this->data['list'] = $this->order_model
									->with('order_product')
									->with('order_log')
									->get_all();
			$this->_render('mall/order_list', $render_type);
		endif;
	}

	public function order_write( $order_id=null ) {
		$this->page_name = '주문서';
		$this->data['order_id'] = $order_id;
		$this->data['order_mode'] = $order_id ? 'update' : 'write';

		if( $this->members->is_admin() && !$order_id ) :
			go_back('관리자는 구매 프로세스 진행이 불가능합니다.');
			die();
		endif;

		// tryCatch
		try {
			// 새글 여부
			if( !$order_id ) :
				$cart_checked = json_decode($_POST['cart_checked']);
				$this->data['carts'] = $this->cart_model->where_in('cart_id', $cart_checked)->get_all();

				if( !count($cart_checked) || !count($this->data['carts']) ) :
					throw new Exception("상품선택에 에러가 발생했습니다.", 1);
				endif;
			else :
				$this->data['view'] = $this->order_model
											->with('order_product')
											->get( $order_id );
				if( !$this->data['view'] )
					throw new Exception("존재하지 않는 주문서 입니다.", 1);

				if( !$this->members->is_admin() && $this->data['view']->order_status != '100_ask' )
					throw new Exception("수정권한이 없습니다.", 1);

			endif;
			// End of 새글 여부

		} catch (Exception $e) {
			show_error( $e->getMessage() );
		}
		// End of tryCatch

		$this->_render('mall/order_write');
	}

	public function order_write_act() {
		// 배송비 정책 확인 	order_deli_price
		// 가격확인	order_pd_price
		// -가격확인	order_admin_price
		// -가격확인	order_total_price

		// tryCatch
		try {
			$result = array();
			$result['status'] = 'fail';
			$result['msg'] = '에러가 발생했습니다.';

			$order_data = new stdClass;

			// 카트 정보
			$cart_checked_array = json_decode($_POST['_cart_checked']);
			$carts = $this->cart_model->where_in('cart_id', $cart_checked_array)->get_all();

			if( !count($cart_checked_array) || !count($carts) )
				throw new Exception("상품선택에 에러가 발생했습니다.", 1);

			// order 테이블 정보
				// 기타정보
					$order_data = (object) db_filter( $this->input->post(), 'order_' );

				// 회원정보
				if( $this->members->is_login() )
					$order_data->order_mb_id = $this->logined->mb_id;

				// 비용
					$this->cart_model->set_carts($cart_checked_array);
					$order_data->order_deli_price = $this->cart_model->deli_price();
					$order_data->order_pd_price = $this->cart_model->pd_price();
					$order_data->order_admin_price = 0;
					$order_data->order_total_price =
							$order_data->order_deli_price
							+ $order_data->order_pd_price
							- $order_data->order_point_use
							+ $order_data->order_admin_price;

					// kmh_print( 'order_data' );
					// kmh_print( $order_data );

			// 삽입
				// 트랜젝션 (모델이 this->db 외 다른 디비를 사용할 경우 해당 디비로 변경 필요)
					// $this->db->trans_start(TRUE);	// 테스트
					$this->db->trans_start();	// 실제
					// 오더 입력
						if( !$order_insert_id = $this->order_model->insert($order_data) )
							throw new Exception("주문 정보 입력에 실패했습니다.", 1);
					// 오더-상품 입력
						foreach( (array) $carts as $cart ) :
							// kmh_print( $cart->product->options );

							$op_insert_id = null;
							$op_data = new stdClass;
							$op_data->op_order_id  = $order_insert_id;
							$op_data->op_pd_id     = $cart->cart_item->op_pd_id;
							$op_data->op_count     = $cart->cart_item->op_count;
							$op_data->op_price_one = $cart->cart_item->op_price_one;
							$op_data->op_price     = $cart->cart_item->op_price;
							$op_data->op_options   = $cart->product->op_options;

							if( !$op_insert_id = $this->order_product_model->insert($op_data) )
								throw new Exception("주문 상품정보 입력에 실패했습니다.", 1);

							// 수량 업데이트
							$this->product_model
								->set("pd_order", "pd_order + {$cart->cart_item->op_count}", false)
								->where('pd_id', $cart->cart_item->op_pd_id)
								->update();

						endforeach;

					// 카트 제거
						// kmh_print( $cart_checked_array );
						foreach( (array) $cart_checked_array as $key => $row ) :
							$this->cart_model->delete( $row );
						endforeach;

					// 로그 추가
						$this->order_log_model->simple_add($order_insert_id, '100_ask');

				// End 트랜젝션
					$this->db->trans_complete();

				if ($this->db->trans_status() === FALSE) {
					$this->kmh->log($order_data, 'order_write_act 트랜젝션 에러');
					throw new Exception("데이터 처리에 에러가 발생했습니다.", 1);
				}

				// 비회원 세션
				if( !$this->members->is_login() ) :
					$_SESSION['guest_order_search_1'] = $order_data->order_name;
					$_SESSION['guest_order_search_2'] = $order_data->order_tel;
				endif;

			// CODE
			$result['status'] = 'ok';
			$result['msg'] = '정상적으로 처리되었습니다.';
			$result['id'] = $order_insert_id;
			$result['url'] = "{$this->mall_base_url}/order/{$order_insert_id}";
		} catch (Exception $e) {
			$result['msg'] = $e->getMessage();
		}
		// End of tryCatch

		kmh_json( $result );
		// $this->_render('mall/order_result');
	}

	public function order_update_act() {
		$result = array();
		$result['status'] = 'fail';
		$result['msg'] = '에러가 발생했습니다.';

		// 글작성
		try {
			// // 전체 권한
			// if( !$this->members->is_admin() )
			// 	throw new Exception("권한이 없습니다.", 1);

			// 권한 & 디비조회
			if( !$order_db = $this->order_model->get( $this->input->post('order_id')) )
				throw new Exception("주문서가 정확한지 확인 후 다시 시도하세요.", 1);

			$prefix = 'order_';
			$data_ex = array(
				'order_id',
				'order_deli_price',
				'order_pd_price',
				'order_total_price',
				); // 예외 컬럼

			if( !$this->members->is_admin() ) :
				$data_ex[] = 'order_status';
				$data_ex[] = 'order_admin_price';
			endif;

			$data = (object)db_filter($this->input->post(), $prefix, $data_ex);

			// 관리자 할인 금액 적용
			if( $data->order_admin_price ) :
				$data->order_total_price =
					$order_db->order_deli_price +
					$order_db->order_pd_price +
					$data->order_admin_price;
			endif;

			// 데이터
			if( !$this->input->post('order_id') )
				throw new Exception("필요 정보가 없습니다.", 1);

			// kmh_print($this->input->post());
			// kmh_print($data);
			// die();

			$this->db->trans_start();	// 실제

				// 실행
				if( !$this->order_model->update( $this->input->post('order_id'), $data ) )
					throw new Exception("업데이트 중 에러가 발생했습니다.", 1);

				if( $data->order_status )
					$this->order_log_model->simple_add($this->input->post('order_id'), $data->order_status);
			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {
				$this->kmh->log($data, 'order_update_act 트랜젝션 에러');
				throw new Exception("데이터 처리에 에러가 발생했습니다.", 1);
			}

			$result['status'] = 'ok';
			$result['msg'] = '정상 처리되었습니다.';
			if( $this->members->is_admin() )
				$result['url'] = "{$this->mall_base_url}/order_write/{$this->input->post('order_id')}";
			else
				$result['url'] = "{$this->mall_base_url}/order/{$this->input->post('order_id')}";
		} catch (Exception $e) {
			$result['status'] = 'fail';
			$result['msg'] = $e->getMessage();
		}
		// End of 글작성

		kmh_json($result);
	}

	public function order_cancel( $order_id ) {
		$result = array();
		$result['status'] = 'fail';
		$result['msg'] = '에러가 발생했습니다.';

		// 글작성
		try {
			$status = '900_cancel';
			$data = new stdClass;
			$data->order_status = $status;

			// 권한확인 && 디비조회
			if( !$order_db = $this->order_model->get($order_id) )
				throw new Exception("주문서가 정확한지 확인 후 다시 시도하세요.", 1);

			// 실행
			if( !$this->order_model->update($order_id, $data) )
				throw new Exception("취소 중 에러가 발생했습니다.", 1);

			// 액티비티
			$this->order_log_model->simple_add($order_id, $status);

			$result['status'] = 'ok';
			$result['msg'] = '정상 처리되었습니다.';
		} catch (Exception $e) {
			$result['status'] = 'fail';
			$result['msg'] = $e->getMessage();
		}
		// End of 글작성

		kmh_json($result);
	}
}

?>