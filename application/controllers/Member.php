<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member extends MY_Controller {

	public $member_skin_path = "member/";
	public $auth_field = null;			// 기본 로그인 항목 (Members 라이브러리 설정을 가져옴)

	public function __construct() {
		parent::__construct();

		$this->load->model('member_model');
		$this->load->library('members');
		$this->load->library('encrypt');

		$this->auth_field = $this->members->auth_field;

		$load_style = "/views/".$this->member_skin_path."css/member.less";
        $this->css[] = $load_style;

        $load_js = "/views/".$this->member_skin_path."js/member.js";
        $this->javascript[] = $load_js;

        $this->title = '회원 ' . $this->title;
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
			$str = '';
			$str.= "encrypt : ".$this->encrypt->encode($id)."<br>";
			$str.= "decrypt : ".$this->encrypt->decode($id)."<br>";

			$str.= "=== FROM MEMBER_model ===<br>";
			$db = $this->member_model->where(  'mb_id' , $id )->get();

			$str.= "encrypt : ".$db->mb_password."<br>";
			$str.= "encrypt : ".$this->encrypt->decode($db->mb_password)."<br>";

			echo $str;
		}

		public function test() {
			$this->load->view( $this->member_skin_path.'test');
		}

		// Jquery Validator 용 필드 사용가능여부 체크
		public function check( $field ) {
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

			$this->kmh->log( $this->db->last_query(), 'check '.$field);

			switch ( $field ) {
				case 'mb_display' :
					if( !$this->members->is_admin() ) : 	// 관리자 분기
						// 해당필드 일치항목이 디비에 없거나, 컨피그에 블랙리스트 확인
						if ( empty($db) &&
							 !in_array( $this->input->post($field), $this->config->item('deny_mb_nick') )
							) :	// ifelse
							$result = 'true';
						else :	// ifelse
							$result = 'false';
						endif;	// ifelse
					else :				// 관리자 분기
						$result = 'true';
					endif;				// 관리자 분기

					break;
				default:
					if( $db->{ $field }!='' ) 		$result = 'false';
					else 					 		$result = 'true';
					break;
			}

			echo $result;
		}

	/*----------  뷰  ----------*/

		public function login() {
			$this->_render( $this->member_skin_path.'login', 'ONLYPAGE');
		}

		public function join() {
			$this->_render( $this->member_skin_path.'join');
		}

		public function update() {
			$this->data['is_update'] = true;

			try {
				$this->data['view'] 		= $this->member_model->get( $this->logined->mb_id );

				unset( $this->data['view']->mb_password );

				if( !$this->data['view'] ) 	throw new Exception("가입되지 않은 회원입니다.");

				$this->_render( $this->member_skin_path.'join');
			} catch (Exception $e) {
				echo $e->getMessage();
			}

		}

		public function logout() {
			$this->logout_act();

			$redirect = $this->input->post_get('redirect') ? $this->input->post_get('redirect') : $this->input->server('HTTP_REFERER');
			if( strpos($redirect, 'member')!==FALSE )
				$redirect = '/';

			redirect( $redirect );
		}

	/*----------  실행부  ----------*/

		public function login_act() {
			if( !$this->input->is_ajax_request() ) exit;

			$result['status'] = 'fail';
			$result['msg'] = '에러가 발생했습니다.';

			$result['data'] = $this->input->post();
			$result['redirect'] = $this->input->get_post('redirect') ? $this->input->get_post('redirect') : '/';

			$db = $this->member_model->where(  $this->auth_field , $this->input->post( $this->auth_field ) )->get();
			$result['qry'] = $this->db->last_query();

			try {
				// 에러 처리
				if( $db->mb_id=='' )
					throw new Exception("가입되지 않은 회원입니다.");
				else if( $db->mb_status == 'ask' )
					throw new Exception("심사중인 회원입니다.");
				else if( $db->mb_status != 'ok' )
					throw new Exception("정상회원이 아닙니다. 관리자에게 문의하세요.");
				else if( $this->input->post('mb_password') != $this->encrypt->decode( $db->mb_password ) )
					throw new Exception("비밀번호가 일치하지 않습니다.");

				// 인증성공 : 비번확인
				$this->session->set_userdata('member', $db);
				$result['status'] = 'ok';
				$result['msg'] = '정상 처리되었습니다.';

				// 액티비티 기록
				$this->kmh->activity($db->mb_id, '로그인');

			} catch (Exception $e) {
				$result['msg'] = $e->getMessage();
			}

			kmh_json($result);
		}

		public function logout_act() {
			$this->session->unset_userdata('member');
		}

		public function join_act() {
			if( !$this->input->is_ajax_request() ) exit;

			$result = array();
			$result['status'] = 'fail';
			$result['msg'] = '에러가 발생했습니다.';

			// 글작성
			try {
				if( empty($this->input->post($this->auth_field)) )
					throw new Exception("필수 변수가 없습니다. {$this->auth_field}", 1);

				$data = db_filter( $this->input->post(), 'mb_' );

				if( !$id = $this->member_model->insert($data) )
					throw new Exception("등록에 실패했습니다.", 1);

				$result['status'] = 'ok';
				$result['msg'] = '정상 처리되었습니다.';

				$this->kmh->activity($id, '회원가입');

			} catch (Exception $e) {
				$result['status'] = 'fail';
				$result['msg'] = $e->getMessage();
			}
			// End of 글작성

			kmh_json($result);
		}

		// 로그인한 회원 본인의 수정만 가능
		public function update_act() {
			if( !$this->input->is_ajax_request() ) exit;

			$result = array();
			$result['status'] = 'fail';
			$result['msg'] = '에러가 발생했습니다.';

			// 글작성
			try {
				if( empty($this->input->post($this->auth_field)) )
					throw new Exception("필수 변수가 없습니다. {$this->auth_field}", 1);

				$data = db_filter( $this->input->post(), 'mb_' );
				$result['data'] &= $data;

				if( $this->input->post('mb_password') == '' )
					unset( $data['mb_password'] );

				if( !$this->member_model->update($this->logined->mb_id, $data) )
					throw new Exception("수정에 실패했습니다.", 1);

				// 파일업로드
				$this->load->library('files');
				$result['upload_result'] =
					$this->files->upload(
							'_mb_photo',
							"member",
							strtolower( get_class($this) ),
							$this->logined->mb_id,
							'photo'
						);

				$result['qry'] = $this->db->last_query();
				$result['status'] = 'ok';
				$result['msg'] = '정상 처리되었습니다.';

				$this->kmh->activity($this->logined->mb_id, '업데이트');

				// 세션 갱신
				$db = $this->member_model->get( $this->logined->mb_id );
				$this->session->unset_userdata('member');
				$this->session->set_userdata('member', $db);

			} catch (Exception $e) {
				$result['status'] = 'fail';
				$result['msg'] = $e->getMessage();
			}
			// End of 글작성

			kmh_json($result);
		}
}