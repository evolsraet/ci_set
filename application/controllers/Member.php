<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Hybridauth\Hybridauth;

class Member extends MY_Controller {

	public $member_skin_path = "member/";
	public $auth_field = null;			// 기본 로그인 항목 (Members 라이브러리 설정을 가져옴)

	public function __construct() {
		parent::__construct();

		$this->load->model('member_model');
		$this->load->model('point_model');
		$this->load->library('members');
		$this->load->helper('member_helper');

		// 계정 ID 필드
		$this->auth_field = $this->members->auth_field;

		$load_style = "/views/".$this->member_skin_path."css/member.less";
        $this->css[] = $load_style;

        $load_js = "/views/".$this->member_skin_path."js/member.js";
        $this->javascript[] = $load_js;

        $this->title = '회원 - ' . $this->title;
	}


	/*----------  API  ----------*/
		public function app_update_token() {
			// if( !IS_APP ) die('APP ONLY agent: ' . $_SERVER['HTTP_USER_AGENT']);
			// 안드로이드에서 잘 안된다함 - 충빈

			$this->kmh->log($_POST, "app_update_token {$mb_id}");
			$mb_id = $this->member_model->decode_auth(
						$this->input->post('authkey')
					);

			if( $this->input->post('mb_app_token') != ''
				OR $this->input->post('mb_app_uuid') != ''
			) :
				$data = new stdClass;
				$data->dv_id    = $this->input->post('mb_app_token');
				$data->dv_uuid  = $this->input->post('mb_app_uuid');
				$data->dv_os    = $this->input->post('mb_app_os');
				$data->dv_mb_id = $mb_id;


				// 디바이스에서 uuid
				$this->load->model('device_model');
				$already = $this->device_model
					->where('dv_uuid',$data->dv_uuid)
					// ->where('dv_mb_id',$data->dv_mb_id)
					->count_by();
				if( $already ) :
					$this->device_model
						->where('dv_uuid',$data->dv_uuid)
						// ->where('dv_mb_id',$data->dv_mb_id)
						->update(null,$data);					
				else :
					$this->device_model
						->insert($data);
				endif;
			endif;

		}

		public function app_logout() {
			// if( !IS_APP ) die('APP ONLY agent: ' . $_SERVER['HTTP_USER_AGENT']);
			// 안드로이드에서 잘 안된다함 - 충빈

			$mb_id = $this->member_model->decode_auth(
						$this->input->post('authkey')
					);

			if( $this->input->post('mb_app_uuid') != ''
			) :
				$data = new stdClass;
				$data->mb_app_uuid = $this->input->post('mb_app_uuid');
				$data->mb_app_token = $this->input->post('mb_app_token');
				$data->mb_app_os    = $this->input->post('mb_app_os');

				// 디바이스에서 uuid 제거
				$this->load->model('device_model');
				$this->device_model
					->where('dv_mb_id', $mb_id)
					->where('dv_uuid', $data->mb_app_uuid)
					->delete();
			endif;

			$_SESSION['dv_uuid'] = $this->input->post('mb_app_uuid');

			$this->kmh->log($_POST, "app_logout {$mb_id}");
		}

		public function app_push_toggle() {
			// if( !IS_APP ) die('APP ONLY agent: ' . $_SERVER['HTTP_USER_AGENT']);
			// 안드로이드에서 잘 안된다함 - 충빈

			$mb_id = $this->member_model->decode_auth(
						$this->input->post('authkey')
					);

			if( $this->input->post('mb_app_uuid') != '' ) :
				$data = new stdClass;
				$data->mb_app_uuid = $this->input->post('mb_app_uuid');

				// 디바이스에서 uuid
				$this->load->model('device_model');
				$this->device_model
					// ->where('dv_mb_id', $mb_id)
					->where('dv_uuid', $data->mb_app_uuid)
					->set('dv_push', 'dv_push XOR 1', false)
					->update();
			endif;

			$this->kmh->log($_POST, "app_push_toggle {$mb_id}");
		}		

		public function app_toolbar() {
			if( !APP_AUDIT ) :
				echo 0;
			else :
				echo 1;
			endif;
		}

		public function app_auto_login() {
			// if( !IS_APP ) die('APP ONLY agent: ' . $_SERVER['HTTP_USER_AGENT']);
			// 안드로이드에서 잘 안된다함 - 충빈

			// $_POST['mb_id'] = 57;
			// $_POST['mb_password'] = "ef52b949fba7bbc2055292b8347fe2530d7aac7a11968856beff593eb8d1274038001416f30fca4e861065a18dc2ab90aa65f5478e82c0f9aaa62e78b9b6be661ldcYVoY8Gr5JE%2FnN%2FGwOkemhjdmQnIEgrTxcNNL%2FjU%3D";

			// $result['post'] = $this->input->post();	

			$mb_id = $this->member_model->decode_auth(
						$this->input->post('authkey')
					);
			// $mb_password = $this->input->post('mb_password');

			// $this->kmh->log( $mb_password );
			// $this->kmh->log( urldecode($mb_password) );
			// $this->kmh->log( $this->encryption->decrypt(($mb_password)) );
			// $mb_password_enc = ($mb_password);
			
			// $mb_password = $this->encryption->decrypt( $mb_password );

			// $_POST['mb_password'] = $this->encryption->decrypt( $mb_password ) ;	// login_check 에서 활용


			try {
				// $result['post'] = $this->input->post();
				$result['status'] = 'fail';
				$result['msg'] = '에러가 발생했습니다.';
				$result['mb_id'] = $mb_id;

				if( !$mb_id )
					throw new Exception("정상적인 authkey 가 아닙니다.", 1);

				// 디비 확인
				$db = $this->member_model
							->where('mb_id', $mb_id)
							->get();
							// ->where('mb_password', $mb_password)
				$_POST['mb_password'] = $this->encryption->decrypt( $db->mb_password );

				// 로그인 처리
				$this->member_model->login_check($db);
				$this->app_update_token();

				$result['status'] = 'ok';
				$result['msg'] = '정상 처리되었습니다.';
			} catch (Exception $e) {
				$result['status'] = 'fail';
				$result['msg'] = $e->getMessage();
			}

			kmh_json( $result );
		}
	
	/*----------  공용 / 기타  ----------*/

		public function info( $id ) {
			kmh_print(
				"uri1 : ".$this->uri->segment(1).PHP_EOL.
				"uri2 : ".$this->uri->segment(2).PHP_EOL.
				"uri3 : ".$this->uri->segment(3).PHP_EOL
			);

			$result = $this->member_model->get(2);
			kmh_print( $result );
		}

		public function enc( $id ) {
			if( ENVIRONMENT != 'development' ) die('NO PERMITION');
			
			$str = '';
			$str.= "encrypt : ".$this->encryption->encrypt($id)."<br>";
			$str.= "decrypt : ".$this->encryption->decrypt($id)."<br>";

			$str.= "=== FROM MEMBER_model ===<br>";
			$db = $this->member_model->where(  'mb_id' , $id )->get();

			$str.= "encrypt : ".$db->mb_password."<br>";
			$str.= "decrypt : ".$this->encryption->decrypt($db->mb_password)."<br>";

			echo $str;
		}

		public function test() {
			$this->load->view( $this->member_skin_path.'test');
		}

		// Jquery Validator 용 필드 사용가능여부 체크
		public function check( $field, $echo_result = true ) {
			$result = 'false';

			// 본인 제외, 해당 필드명 있는지 확인
			$where = array(
				$field => $this->input->post($field),
				'mb_id !=' => $this->logined->mb_id
			);
			$this->member_model->select( $field )
					->with_deleted()
					->where( $where );

			$db = $this->member_model->get();

			// $this->kmh->log( $this->db->last_query(), 'check '.$field);

			switch ( $field ) {
				case 'mb_display' :
						// 해당필드 일치항목이 디비에 없거나, 컨피그에 블랙리스트 확인
						if (
							empty($db)
							AND
							(
								$this->members->is_admin()
								OR
								!in_array(
									$this->input->post($field),
									$this->config->item('deny_mb_nick')
								)
							)
						) :	// ifelse
							$result = 'true';
						else :	// ifelse
							$result = 'false';
						endif;	// ifelse
					break;
				default:
					if( $db->{ $field }!='' ) 		$result = 'false';
					else 					 		$result = 'true';
					break;
			}

			if( $echo_result ) :
				echo $result;
			else :
				return $result;
			endif;
		}

	/*----------  뷰  ----------*/

		public function login() {
			$this->_render( $this->member_skin_path.'login', 'NO_NAV');
		}

		public function join() {
			$this->_render( $this->member_skin_path.'join', 'NO_NAV');
		}

		public function reset_passsword() {
			if( !$this->input->is_ajax_request() ) exit;

			try {
				$reset_email = $this->session->flashdata('reset_email');
				if( empty($reset_email) )
					throw new Exception("이메일을 먼저 검색해주세요.", 1);

				// 디비 조회
				$member_info = $this->member_model->where('mb_email', $reset_email)->get();

				// 컨텐츠 생성
				$mail_content = "
					<h3>계정찾기</h3>
					<p class=\"lead\">{$member_info->mb_display}님,<br>아래 계정정보로 로그인 하세요<p>
				";
				switch ($member_info->mb_social_type) {
					case 'web':
						$mb_auth = $member_info->{$this->auth_field};
						$mb_password = rand(100000, 999999);

						// 비밀번호 변경
						$update_result = $this->member_model->where('mb_email', $reset_email)
							->set('mb_password', $this->encryption->encrypt($mb_password))
							->update();

						if( !$update_result )
							throw new Exception("임시 비밀번호 생성 중 에러가 발생했습니다.", 1);

						$mail_content .= "
							<p class=\"callout\">
								계정 : {$mb_auth}<br>
								임시 비밀번호 : {$mb_password}
							</p>
						";
						break;
					default:
						$mail_content .= "
							<p class=\"callout\">
								{$member_info->mb_social_type} 소셜계정으로 가입되었습니다.
							</p>
						";
						break;
				}

				$mail_content .= "
					<p class=\"callout\">
						<a href=\"http://{$_SERVER['HTTP_HOST']}/member/login\">로그인</a>
					</p>
				";

				$mail_content  .= email_comment_noreply();

				// 메일 발송
				$email_result = email_send(
									null,
									null,
									$reset_email,
									'계정찾기',
									$mail_content
								);
				if( $email_result !== true )
					throw new Exception($email_result, 1);

				$result['status'] = 'ok';
				$result['msg'] = '정상 처리되었습니다.';
				$result['reset_email'] = $reset_email;
			} catch (Exception $e) {
				$result['status'] = 'fail';
				$result['msg'] = $e->getMessage();
				$result['reset_email'] = $reset_email;
			}

			kmh_json( $result );
		}

		public function find( $type='account' ) {
			if( count($_POST) ) :
				switch ($type) {
					case 'account':
						$where = db_filter( $_POST, 'mb_' );
						$result['data'] =
							$this->member_model
								->select( $this->auth_field )
								->where($where)
								->get();

						$this->session->set_flashdata('reset_email', $result['data']->mb_email);
						break;

					default:
						# code...
						break;
				}
				$result['status'] = 'ok';
				die( kmh_json($result) );
			endif;

			$this->_render( $this->member_skin_path.'find');
		}

		public function update() {
			$this->data['is_update'] = true;

			try {
				$this->data['view'] 		= $this->member_model->get( $this->logined->mb_id );

				unset( $this->data['view']->mb_password );

				if( !$this->data['view'] ) 	throw new Exception("가입되지 않은 회원입니다.");

				$this->_render( $this->member_skin_path.'join');
			} catch (Exception $e) {
				show_error($e->getMessage(), 500, '오류가 발생했습니다.');
			}

		}

		public function logout() {
			$this->logout_act();

			$redirect = $this->input->post_get('redirect') ? $this->input->post_get('redirect') : $this->input->server('HTTP_REFERER');
			if( strpos($redirect, 'member')!==FALSE )
				$redirect = '/';

			if( IS_APP ) :
				echo "<script>";
				// in2uapp://mobileapp?control=logout';
				echo "	location.href='{$this->config->item('app_name')}://mobileapp?control=logout';";
				echo "</script>";				
			else :
				redirect( $redirect );
			endif;
		}

	/*----------  실행부  ----------*/

		public function login_act() {
			if( !$this->input->is_ajax_request() ) exit;

			$result['status'] = 'fail';
			$result['msg'] = '에러가 발생했습니다.';

			$result['data'] = $this->input->post();
			$result['redirect'] = $this->input->get_post('redirect') ? $this->input->get_post('redirect') : '/';
			if( strpos($result['redirect'], 'login') !== false )
				$result['redirect'] = '/';

			$db = $this->member_model->where(  $this->auth_field , $this->input->post( $this->auth_field ) )->get();
			$result['qry'] = $this->db->last_query();

			try {
				$this->member_model->login_check($db);
				
				if( IS_APP )
					$enc_mb_id = $this->member_model->encode_auth(
								$db->mb_id
							);

					$result['app_scheme'] = "{$this->config->item('app_name')}://mobileapp?control=member&action=login"
						. "&authkey={$enc_mb_id}"
						. "&return_url={$result['redirect']}";

				$result['status'] = 'ok';
				$result['msg'] = '정상 처리되었습니다.';

				// 액티비티 기록
				$this->kmh->activity($db->mb_id, '로그인');
				// add_flashdata('page_notice', "{$this->logined->mb_display}님 로그인 되었습니다.");

			} catch (Exception $e) {
				$result['msg'] = $e->getMessage();
			}

			kmh_json($result);
		}

		public function social_login($provider = null) {
			// SNS 설정의 리다이렉트 url = /member/social_endpoint?hauth.done=[PROVIDER]

			// codeigniter 에서는 REQUEST 보안이유로 비워짐
			// 특정 provider 에서 request 사용하는 경우가 있음
			$_REQUEST = array_merge($_GET,$_POST);

			if( $provider != '' ) :
				$this->session->set_flashdata('social_provider', $provider);
			else :
				$provider = $this->session->flashdata('social_provider');
			endif;

			// kmh_print("provider : " . $provider);

			try{
				$hybridauth = new Hybrid_Auth( $this->config->item('hybridauth') );

				$adapter = $hybridauth->authenticate( $provider );

				if( $adapter->isUserConnected() ) :
					$user_profile = $adapter->getUserProfile();

					// kmh_print($user_profile);
					// die();

				    // 기존 등록 여부 확인
					$already = $this->member_model
									->where( 'mb_social_type' , $provider )
									->where( 'mb_social_id' , $user_profile->identifier )
									->get();

					// 기존 등록 여부
					if( $already->mb_id ) :
						// 정보 업데이트
						$this->member_model->set('mb_social_image', $user_profile->photoURL)
											->update( $already->mb_id );
						// 새로 받아오기
						$already = $this->member_model->get($already->mb_id);

						// 프로필 사진 및 기타 정보 업데이트?
						$this->member_model->login_process($already);

						// 액티비티 기록
						$this->kmh->activity($already->mb_id, '소셜 로그인');
					else :
						// 데이터 가공
						// https://hybridauth.github.io/hybridauth/apidoc.html
						$data = array();
						$data['mb_social_type'] = $provider;
						$data['mb_social_id'] = $user_profile->identifier;
						$data['mb_social_image'] = $user_profile->photoURL;
						if( $user_profile->email!='' ) $data['mb_email'] = $user_profile->email;
						$data['mb_display'] = $user_profile->displayName;
						$data['mb_mobile'] = $user_profile->phone;

						if( $data['mb_social_id'] == '' )
							throw new Exception("계정정보를 가져오는데 오류가 발생했습니다.", 100);

						// 이메일이 빈값이 아닌데, 이미 있는경우 비움
						// 개선 방향 : 다른 계정으로 이메일이 있는 경우, 기존 계정 패스워드 확인 후 통합
						if( $data['mb_email'] != '' ) :
							$_POST['mb_email'] = $data['mb_email'];
							if( $this->check( 'mb_email', false )!='true' ) :
								$data['mb_email'] = null;
							endif;
						endif;

						// 닉네임
						$_POST['mb_display'] = $data['mb_display'];
						if( $this->check( 'mb_display', false )!='true' || trim($data['mb_display'])=='' ) :
							$data['mb_display'] = $data['mb_display'] . "_{$data['mb_social_type']}{$data['mb_social_id']}";
						endif;

						// 디비 삽입
						// 필수정보가 필요한 경우,
						// 삽입하지 않고 - 회원가입 창으로 유도
						$join_result = $this->join_act($data);
						if( $join_result->status != 'ok' )
							throw new Exception($join_result->msg, 200);

						// 로그인
						$db = $this->member_model->get( $join_result->id );
						$this->member_model->login_process($db);
					endif;
					// End of 기존 등록 여부

					add_flashdata('page_notice', "{$this->logined->mb_display}님 로그인 되었습니다.");
				else :
					throw new Exception("연결에 실패했습니다.", 100);
				endif;

				$adapter->logout();
				if( IS_APP ) :
					$result['app_scheme'] = "{$this->config->item('app_name')}://mobileapp?control=member&action=login"
						. "&authkey=" . $this->member_model->encode_auth($db->mb_id);
					echo "<script>";
					echo "location.href='";
					echo $result['app_scheme'];
					echo "';</script>";
				else :
					redirect('/');
				endif;

				/*

				// exp of using the twitter social api: Returns settings for the authenticating user.
				$account_settings = $provider->api()->get( 'account/settings.json' );

				// print recived settings
				echo "Your account settings on Twitter: " . print_r( $account_settings, true );

				// disconnect the user ONLY form twitter
				// this will not disconnect the user from others providers if any used nor from your application
				echo "Logging out..";
				*/
			}
			catch( Exception $e ){
				// Display the recived error,
				// to know more please refer to Exceptions handling section on the userguide
				switch( $e->getCode() ){
					case 0 : $err_title = "Unspecified error."; break;
					case 1 : $err_title = "Hybriauth configuration error."; break;
					case 2 : $err_title = "Provider not properly configured."; break;
					case 3 : $err_title = "Unknown or disabled provider."; break;
					case 4 : $err_title = "Missing provider application credentials."; break;
					case 5 : $err_title = "Authentification failed. "
											. "The user has canceled the authentication or the provider refused the connection.";
									 break;
					case 6 : $err_title = "User profile request failed. Most likely the user is not connected "
											. "to the provider and he should authenticate again.";
									 // $adapter->logout();
									 break;
					case 7 : $err_title = "User not connected to the provider.";
									 // $adapter->logout();
									 break;
					case 8 : $err_title = "Provider does not support this feature."; break;
					case 200 :	// 추가 정보 필요
						$this->data['social_join'] = (object)$data;
						$this->join();
						break;
					default :
						$erro_title = '소셜로그인에 에러가 발생했습니다.';
						break;
				}

				if( $e->getCode() < 100 )
					show_error($e->getMessage(), 500, $err_title);
			}
		}

		public function social_endpoint() {
			$_REQUEST = $_GET;

			kmh_print( $_REQUEST );
			kmh_print( $_POST );
			kmh_print( $_GET );

			require_once( "vendor/hybridauth/hybridauth/hybridauth/Hybrid/Auth.php" );
			require_once( "vendor/hybridauth/hybridauth/hybridauth/Hybrid/Endpoint.php" );

			Hybrid_Endpoint::process();
		}

		// hybridAuth 3 용 - 라이브러리 개발중이라 보류
		public function _3_social_login($provider = null) {

			if( $provider == null ) :
				$provider = $this->session->flashdata('social_provider');
			else :
				$this->session->set_flashdata('social_provider', $provider);
			endif;

			// $provider = 'Google';

			try{
			    //Feed configuration array to Hybridauth
			    $hybridauth = new Hybridauth( $this->config->item('hybridauth') );

			    //Then we can proceed and sign in with Twitter as an example. If you want to use a diffirent provider,
			    //simply replace 'Twitter' with 'Google' or 'Facebook'.

			    //Attempt to authenticate users with a provider by name
			    $adapter = $hybridauth->authenticate($provider);

			    //Returns a boolean of whether the user is connected with Twitter
			    if( $adapter->isConnected() ) :
				    //Retrieve the user's profile
				    $userProfile = $adapter->getUserProfile();

				    // kmh_print($userProfile);

				    // 기존 등록 여부 확인
					$already = $this->member_model
									->where( 'mb_social_type' , $provider )
									->where( 'mb_social_id' , $userProfile->identifier )
									->get();

					// 기존 등록 여부
					if( $already->mb_id ) :
						$this->member_model->login_process($already);
					else :
						// 데이터 가공
						$i_data = array();
						$i_data['mb_social_type'] = $provider;
						$i_data['mb_social_id'] = $userProfile->identifier;
						$i_data['mb_social_image'] = $userProfile->photoURL;
						$i_data['mb_email'] = $userProfile->email;
						$i_data['mb_display'] = $userProfile->displayName;
						$i_data['mb_mobile'] = $userProfile->phone;

						// 디비 삽입
						$mb_id = $this->member_model->insert($i_data);

						// 로그인
						$db = $this->member_model->get($mb_id);
						$this->member_model->login_process($db);
					endif;
					// End of 기존 등록 여부

			    endif;

			    $adapter->disconnect();
			    redirect('/');

			}
			catch(\Exception $e){
				show_error($e->getMessage(), 500, '소셜 로그인에 에러가 발생했습니다.');
			}
		}

		public function logout_act() {
			$this->session->unset_userdata('member');
		}

		// 회원가입 (플래시데이터, 액티비티 포함)
		public function join_act( $input_data = null ) {
			// if( !$this->input->is_ajax_request() ) exit;

			// $require = array(
			// 	'mb_mobile'
			// );

			if( $input_data === null ) 	$post_data = $this->input->post();
			else 						$post_data = $input_data;

			$result = array();
			$result['status'] = 'fail';
			$result['msg'] = '에러가 발생했습니다.';

			// 글작성
			try {
				if( empty($post_data[$this->auth_field]) )
					throw new Exception("필수 항목이 없습니다. {$this->auth_field}", 1);

				foreach( (array) $require as $field ) :
					if( empty($post_data[$field]) )
						throw new Exception("필수 항목이 없습니다. {$field}", 1);
				endforeach;

				if( !reCAPTCHA_server() )
					throw new Exception("'로봇이 아닙니다'를 체크해 주세요.", 1);

				$data = db_filter( $post_data, 'mb_' );

				if( !$id = $this->member_model->insert($data) )
					throw new Exception("등록에 실패했습니다.", 1);

				$result['id'] = $id;
				$result['status'] = 'ok';
				$result['msg'] = '정상 처리되었습니다.';

				// 알림
					if( $new_order_email = $this->config_model->get_value('new_order_email') ) :
						$mail_content  = "
							<p class=\"callout\">
								<a href=\"http://{$_SERVER['HTTP_HOST']}/admin/member/member/edit/{$id}\">새로운 회원이 가입했습니다.</a>
							</p>						
						";
						$mail_content  .= email_comment_noreply();

						// 메일 발송
						$email_result = email_send(
											null,
											null,
											$new_order_email,
											'새 회원 알림',
											$mail_content
										);
						if( $email_result !== true )
							$this->kmh->log($email_result, '메일 발송 에러');
					endif;

				$this->kmh->activity($id, '회원가입');
				add_flashdata('page_notice', '회원가입이 정상적으로 처리되었습니다.');
			} catch (Exception $e) {
				$result['status'] = 'fail';
				$result['msg'] = $e->getMessage();
			}
			// End of 글작성

			// kmh_print( kmh_json($result) );

			if( $input_data === null )
				kmh_json($result);
			else
				return (object) $result;
		}

		// 로그인한 회원 본인의 수정만 가능
		public function update_act() {
			// if( !$this->input->is_ajax_request() ) exit;
			if( $input_data === null ) 	$post_data = $this->input->post();
			else 						$post_data = $input_data;

			$result = array();
			$result['status'] = 'fail';
			$result['msg'] = '에러가 발생했습니다.';

			// 글작성
			try {
				if( empty($post_data[$this->auth_field]) )
					throw new Exception("필수 항목이 없습니다. {$this->auth_field}", 1);

				$data = db_filter( $post_data, 'mb_' );
				$result['data'] &= $data;

				if( $data['mb_password'] == '' )
					unset( $data['mb_password'] );

				if( !$this->member_model->update($this->logined->mb_id, $data) )
					throw new Exception("수정에 실패했습니다.", 1);

				// 파일업로드
				$this->load->library('files');
				$result['upload_result'] =
					$this->files
							->set_duplicate_replace()
							->upload(
								'_mb_photo',
								"member",
								strtolower( get_class($this) ),
								$this->logined->mb_id,
								'photo'
							);

				$result['qry'] = $this->db->last_query();
				$result['status'] = 'ok';
				$result['msg'] = '정상 처리되었습니다.';

				add_flashdata('page_notice', '회원정보가 수정되었습니다.');

				$this->kmh->activity($this->logined->mb_id, '업데이트');

				// 세션 갱신
				$db = $this->member_model->get( $this->logined->mb_id );
				$this->member_model->login_process($db);
			} catch (Exception $e) {
				$result['status'] = 'fail';
				$result['msg'] = $e->getMessage();
			}
			// End of 글작성

			kmh_json($result);
		}

	/*----------  포인트 관련  ----------*/

	public function point() {
		$this->page_name = '포인트 내역';

		/*----------  저장  ----------*/
		if( $this->input->is_ajax_request() ):
			$result = array();
			try {
				$data = (object) db_filter($this->input->post(), 'pt_');
				if( empty($data->pt_mb_id) || empty($data->pt_amount) )
					throw new Exception("필수값이 누락되었습니다.", 1);

				if( !$this->point_model->insert($data) )
					throw new Exception("데이터베이스 에러가 발생했습니다.", 1);

				$result['status'] = 'ok';
				$result['msg'] = '정상처리되었습니다.';
			} catch (Exception $e) {
				$result['status'] = 'fail';
				$result['msg'] = $e->getMessage();
			}

			kmh_json($result);
			die();
		endif;


		/*----------  뷰  ----------*/
		$this->css[]               = LIB."remark/vendor/bootstrap-table/bootstrap-table.css";
		$this->javascript_bundle[] = LIB."remark/vendor/bootstrap-table/extensions/mobile/bootstrap-table-mobile.js";
		$this->javascript_bundle[] = LIB."remark/vendor/bootstrap-table/bootstrap-table-ko-KR.js";

		// 포인트 목록
		$this->data = $this->point_model->get_list();

		// 관리자는 회원 정보
		if( $this->members->is_admin() ) :
			$this->member_model->select( array('mb_id', $this->members->auth_field) );
			$this->data['members'] = $this->member_model->get_all();
			$this->data['members'] = as_simple_array( $this->data['members'], 'mb_id', $this->members->auth_field );
		endif;

		$render_type = $this->uri->segment(1)=='admin' ? 'PANEL' : '';
		$this->_render('member/point', $render_type);
	}


}