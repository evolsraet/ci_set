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

	public function uniqtest($num=0) {
		kmh_print( doublehex($num, 4) );

		$array = array();
		for ($i=0; $i < 100; $i++) { 
			// $d = new DateTime();
			// echo $d->format('H:i:s.u') . ' : ';

			list($u, $timestamp) = explode(" ", microtime());
			$u = substr($u,2,6);
			echo date('Y-m-d H:i:s', $timestamp) . " .{$u} : ";
			$hour = date('His', $timestamp);
			echo $hour . " : ";
			$code =	date('Ymd', $timestamp)
					. '-' . doublehex($hour, 4)
					. '-' . doublehex($u, 4)
					. doublehex( rand(0,35) );

			// 2019-03-14 13:36:28 .406685 : 133628406685 : 20190314-2JH23VHF (17)
			// 2019-03-14 13:42:35 .513611 : 14134235513611 : 201903-7X3J-1JS3V (16)
			// 2019-03-14 13:59:15 .086470 : 135915 : 20190314-3FKH-26T8M (19)					

			// $id = strtoupper( uniqid(rand(10,99)) );
			// $array[] = $id;
			// $count = strlen($id);
			// echo  " : ({$count}) $id <br>";

			// $now = date("ymdHis"); //오늘의 날짜 년월일시분초 
			// $rand = strtoupper(substr(md5(uniqid(time())),0,6)); //임의의난수발생 앞6자리 
			// echo $orderNum = microtime() . ' : ' . $now . '-' .$rand . "<br>";

			// $code = microtime();
			// $code = dechex(microtime());
			// $code = uniqid( get_current_user().'-' );
			// $code = uniqid() . '-'  . dechex( rand(16,2554) );
			// $code = substr_replace($code,'-',9,0);
			// $code = $d->format('Ymd')
			// 		. '-' . str_pad( doublehex($d->format('His')), 4, '0', STR_PAD_LEFT )
			// 		. '-' . str_pad( doublehex(rand(0,1155)), 2, '0', STR_PAD_LEFT );
			
			echo ( $code );
			// echo strtoupper( $code );
			$string_conunt = strlen($code);
			echo " ({$string_conunt})<br>";
		}
	}

	public function test() {
		$this->load->model( 'test_model' );
		kmh_print( $this->db->trans_enabled );

			// kmh_print('inside TRANS');
			// kmh_print( $this->db->trans_enabled );
		// $this->db->trans_start();
			// kmh_print( '$this->db->_trans_depth : ' . $this->db->_trans_depth );
			// echo $insert_id = $this->test_model->insert(array('test_varchar'=>'controller insert'));
			// $this->db
			// 	->set('ac_mb_id2', 	$this->logined->mb_id )
			// 	->insert('activity');
		// $this->db->trans_complete();
		kmh_print('end TRANS');
		kmh_print( $this->db->trans_enabled );

		if( $this->db->trans_status()===FALSE )
			kmh_print( '$this->db->trans_status() false' );

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

	public function cart_delete_test() {
		kmh_print('cart_delete_test START');
		$this->cart_model->delete_old_cart();
		kmh_print('cart_delete_test END');
	}

	// 장바구니에 담기
	public function begin_order() {
		if( !$this->input->is_ajax_request() ) die('정상적인 접속이 아닙니다.');

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

				// 기존 카트의 제품+옵션 인지 확인
					$options_json = json_encode($cart->cart_item->op_options);
					$db = $this->cart_model
						->like('cart_item','"op_pd_id":"'.$product_data->op_pd_id.'"')
						->like('cart_item', $options_json)
						->get();
					// if( !$db ) :
					// 	if( $db === null ) throw new Exception("null", 1);
					// 	if( $db === false ) throw new Exception("false", 1);
					// 	throw new Exception("리드 실패", 1);
					// endif;
						

				// 실행
					if( $db ) :
						$update_data = new stdClass;
						$db->cart_item->op_count += $product_data->op_count;
						$db->cart_item->op_price = $db->cart_item->op_count * $db->cart_item->op_price_one;
						$update_data->cart_item = $db->cart_item;
						// kmh_print($update_data);
						// die('test');

						if( !$this->cart_model->update($db->cart_id, $update_data) ) :
							throw new Exception("장바구니 업데이트 중 오류가 발생했습니다.", 1);
						endif;
						
						$result['method'] = 'update';
					else :
						if( !$id = $this->cart_model->insert( $cart ) ) :
							throw new Exception("장바구니 입력 중 에러가 발생했습니다.", 1);
						endif;

						$result['method'] = 'insert';
					endif;

			$result['status'] = 'ok';
			$result['msg'] = '정상 처리되었습니다.';
		} catch (Exception $e) {
			$result['status'] = 'fail';
			$result['msg'] = $e->getMessage();
		}

		kmh_json( $result );
	}

	public function cart() {
		$this->cart_model->delete_old_cart();

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
					// 삽입시 포인트 최대 금액 조정 - 포인트 사용 && 회원
					if( ($order_data->order_deli_price+$order_data->order_pd_price) < $order_data->order_point_use ) :
						$order_data->order_point_use = ($order_data->order_deli_price+$order_data->order_pd_price);
					endif;
					$order_data->order_admin_price = 0;
					$order_data->order_total_price =
						$this->order_model->total_price(
							$order_data->order_pd_price,
							$order_data->order_deli_price,
							$order_data->order_point_use,
							$order_data->order_admin_price
						);

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

					// 알림
						if( $new_order_email = $this->config_model->get_value('new_order_email') ) :
							$mail_content  = "
								<p class=\"callout\">
									<a href=\"http://{$_SERVER['HTTP_HOST']}/admin/order_write/{$order_insert_id}\">새 주문이 접수되었습니다.</a>
								</p>						
							";
							$mail_content  .= email_comment_noreply();

							// 메일 발송
							$email_result = email_send(
												null,
												null,
												$new_order_email,
												'새 주문 알림',
												$mail_content
											);
							if( $email_result !== true )
								$this->kmh->log($email_result, '메일 발송 에러');
						endif;

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
			if( isset($data->order_admin_price) ) :
				$data->order_total_price =
						$this->order_model->total_price(
							$order_db->order_pd_price,
							$order_db->order_deli_price,
							$order_db->order_point_use,
							$data->order_admin_price
						);
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

	public function order_delete( $order_id ) {
		$result = array();
		$result['status'] = 'fail';
		$result['msg'] = '에러가 발생했습니다.';

		// 글작성
		try {
			$status = '900_cancel';

			// 권한확인 && 디비조회
			if( !$this->members->is_admin() || !$order_db = $this->order_model->get($order_id) )
				throw new Exception("주문서가 정확한지 확인 후 다시 시도하세요.", 1);

			// 권한확인 && 디비조회
			if( $order_db->order_status != $status )
				throw new Exception("취소된 주문서만 삭제가능합니다.", 1);

			// 실행
			if( !$this->order_model->force_delete()->delete($order_id) )
				throw new Exception("삭제 중 에러가 발생했습니다.", 1);

			$result['status'] = 'ok';
			$result['msg'] = '정상 처리되었습니다.';
		} catch (Exception $e) {
			$result['status'] = 'fail';
			$result['msg'] = $e->getMessage();
		}
		// End of 글작성

		kmh_json($result);
	}


	/*----------  관리자  ----------*/

	public function product( $sub = null, $pd_id = null ) {
		if( empty($sub) ) redirect('/admin/product/list','refresh');

		$this->load->model('mall/product_model');
		$this->data['pd_id'] = $pd_id;

		// DATA
		switch ($sub) {
			case 'get_list':
				// 리스트
				 $db = $this->product_model
				 				->join('category', 'cate_id = pd_cate_id', 'left outer')
				 				->get_all();
				 foreach( fe($db) as &$row ) :
				 	$row->pd_price = number_format($row->pd_price);
				 endforeach;
				kmh_json( $db );
				die();

				break;
			case 'update_act':
				$result['status'] = 'fail';
				$result['msg'] = '에러가 발생했습니다.';

				try {
					if( !$this->input->post('pd_id') )
						throw new Exception("정상적인 접근이 아닙니다.", 1);

					$data = (object)db_filter($this->input->post(), 'pd_');

					if( $this->input->post('is_recovery')=='ok' )
						$data->pd_deleted_at = null;

					if( !$this->product_model->update($this->input->post('pd_id'), $data) )
						throw new Exception("디비 삽입 중 에러가 발생했습니다.", 1);

					// 기존 파일 삭제시
						$this->load->library('files');
						$file_delete_result = $this->input->post('file_delete');

						if( count($file_delete_result) ) {
							$this->file_model
									->where_in('file_id',$file_delete_result)
									->delete();
						}

					// 파일업로드 (다이렉트로 게시물 아이디 등록)
						$result['upload_result'] =
							$this->files->upload(
									'_post_files',
									"product/".$id,
									'product',
									$this->input->post('pd_id'),
									'pd_img'
								);

					// 임시파일들 업데이트 (에디터도 있으므로 무조건)
						$file_update_result = $this->file_model
							->where('file_rel_id', $this->input->post('_post_files_code'))
							->set('file_rel_id', $this->input->post('pd_id'))
							->update();

					$result['id'] = $this->input->post('pd_id');
					$result['status'] = 'ok';
					$result['msg'] = '정상 처리되었습니다.';
				} catch (Exception $e) {
					$result['status'] = 'fail';
					$result['msg'] = $e->getMessage();
				}

				kmh_json( $result );
				die();
				break;
			case 'write_act':
				$result['status'] = 'fail';
				$result['msg'] = '에러가 발생했습니다.';

				try {
					$data = db_filter($this->input->post(), 'pd_');

					// 파일업로드 (임시코드로 업로드 후 추후에 게시글 번호로 업데이트)
					$this->load->library('files');
					$result['upload_result'] =
						$this->files->upload(
								'_post_files',
								"product/".$id,
								'product',
								$this->input->post('_post_files_code'),
								'pd_img'
							);
					// $this->kmh->log( $result['upload_result'], 'upload_result' );

					if( !$id = $this->product_model->insert($data) )
						throw new Exception("디비 삽입 중 에러가 발생했습니다.", 1);

					// 임시파일들 업데이트 (에디터도 있으므로 무조건)
					$file_update_result = $this->file_model
						->set('file_rel_id', $id)
						->where('file_rel_id', $this->input->post('_post_files_code'))
						->update();


					$result['id'] = $id;
					$result['status'] = 'ok';
					$result['msg'] = '정상 처리되었습니다.';
				} catch (Exception $e) {
					$result['status'] = 'fail';
					$result['msg'] = $e->getMessage();
				}

				kmh_json( $result );
				die();
				break;
			case 'delete_act':
				try {
					$result['status'] = 'fail';
					$result['msg'] = '에러가 발생했습니다.';

					if( !$this->data['pd_id'] )
						throw new Exception("정상적인 접근이 아닙니다.", 1);

					if( !$this->product_model->delete($this->data['pd_id']) )
						throw new Exception("삭제 중 에러가 발생했습니다.", 1);

					$result['status'] = 'ok';
					$result['msg'] = '정상 처리되었습니다.';
				} catch (Exception $e) {
					$result['status'] = 'fail';
					$result['msg'] = $e->getMessage();
				}

				kmh_json( $result );
				die();
				break;
			case 'write':
				$sub = 'view';
			case 'view' :
				$this->javascript[] = LIB.'summernote/dist/summernote.min.js';
				$this->javascript[] = LIB.'summernote/dist/lang/summernote-ko-KR.js';
				$this->javascript[] = JS."product_editor.js";;
				$this->css[] = LIB.'summernote/dist/summernote.css';

				if( $this->data['pd_id'] ) :
					$this->data['view'] = $this->product_model->with_deleted()->get( $this->data['pd_id'] );
					if( !$this->data['view']->pd_id )
						show_404('정상적인 접근이 아닙니다.');
					$this->data['is_update'] = true;

					$this->data['view']->file = $this->file_model
						->where('file_rel_type', 'product')
						->where('file_rel_id', $this->data['pd_id'])
						->where('file_rel_desc', 'pd_img')
						->get_all();
				else :
					$this->data['is_update'] = false;
				endif;
				break;
			default:
				break;
		}

		// RENDER
		$this->_render("mall/admin/{$sub}", 'PANEL');
	}

	public function option_list() {
		$this->load->model('mall/option_model');
		$this->_render('mall/modules/admin/option_list', 'AJAX');
	}

	public function option_update() {
		try {
			$this->load->model('mall/option_model');
			$data = db_filter( $this->input->post(), 'ot_' );

			if( $data['ot_use']=='true' )	$data['ot_use'] = true;
			else 							$data['ot_use'] = false;
			// kmh_print($data); die();

			if( !$this->option_model->replace( $data ) )
				throw new Exception("업데이트 중 에러가 발생했습니다.", 1);

			add_flashdata( 'option', '정상적으로 처리되었습니다.' );
		} catch (Exception $e) {
			add_flashdata( 'option', $e->getMessage(), 'error' );
		}
	}

	public function option_delete() {
		try {
			$this->load->model('mall/option_model');
			$where = db_filter( $this->input->post(), 'ot_' );
			if( !$this->option_model->where($where)->delete() )
				throw new Exception("삭제 중 에러가 발생했습니다.", 1);

			add_flashdata( 'option', '정상적으로 처리되었습니다.');
		} catch (Exception $e) {
			add_flashdata( 'option', $e->getMessage(), 'error' );
		}
	}
}

?>