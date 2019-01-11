<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Board extends MY_Controller {

	protected $board_id_segment = false;	// 게시판 아이디 - 세그먼트 위치

	public $board_id   = false;	// 게시판 아이디
	public $board_base = false;	// 보드 베이스
	public $method     = false;	// 메소드
	public $post_id    = false;	// 글 아이디 or 코멘트 아이디
	public $is_update  = false;	// 새글 작성인지 업데이트인지 여부

	protected $auth       = false;	// 권한 변수
	public $board_info = false;	// 게시판 정보

	public function __construct() {
		parent::__construct();

		// $this->benchmark->mark('board_construct_start');

		// 게시판 아이디의 기준을 변경할 경우
		$this->board_id_segment = 2;

		if( $this->uri->segment(1)=='admin' )
			$this->board_id_segment = 3;

		// 변수 설정
		$this->board_id = $this->uri->segment($this->board_id_segment);
		$this->method   = $this->uri->segment($this->board_id_segment+1);
		$this->post_id  = $this->uri->segment($this->board_id_segment+2);

		// board_id 없을 경우, 메인으로 돌아감
		if(!$this->board_id) {
		    redirect('/');
		    exit;
		}

		$this->load->model('post_model');
		$this->load->model('comment_model');
		$this->load->helper('file');
		$this->load->helper('board');
		$this->load->library('files');


		// 설정
		$this->board_info         = $this->post_model->get_board_info( $this->board_id );
		$this->data['board_info'] =& $this->board_info;

		$this->data['board_base'] = base_url();
		for ($i=1; $i <= $this->board_id_segment; $i++) {
			$this->data['board_base'] .= $this->uri->segment( $i ) . '/';
		}
		$this->board_base =& $this->data['board_base'];

		// 에러반환
		if( empty($this->board_info->board_id) ) show_error('존재하지 않는 게시판입니다.');

        // 기본권한 설정

		// 댓글은 회원 이상
		$this->board_info->board_auth_comment = $this->board_info->board_auth_comment ? $this->board_info->board_auth_comment : 1;

        $this->auth = new stdClass();
		$this->auth->list          = $this->members->is_level($this->board_info->board_auth_list);
		$this->auth->write         = $this->members->is_level($this->board_info->board_auth_write);
		$this->auth->reply         = $this->members->is_level($this->board_info->board_auth_reply);
		$this->auth->view          = $this->members->is_level($this->board_info->board_auth_view);
		$this->auth->comment       = $this->members->is_level($this->board_info->board_auth_comment);
		$this->auth->update        = false;
		$this->auth->need_password = false;

		$this->data['auth'] =& $this->auth;

		define('BOARDDIR', VIEWDIR."board/");
		define('SKINDIR', BOARDDIR.$this->board_info->board_skin.'/');

        // 스타일 / 스크립트 로드
        $this->css[] = BOARDDIR."board.less";
        $this->css[] = SKINDIR."/assets/skin.less";

        $this->javascript[] = BOARDDIR."board.js";
        $this->javascript[] = SKINDIR."/assets/skin.js";

		// 에디터
		if( $this->board_info->board_use_editor ) :
			$this->javascript[] = LIB.'summernote/dist/summernote.min.js';
			$this->javascript[] = LIB.'summernote/dist/lang/summernote-ko-KR.js';
			$this->javascript[] = BOARDDIR."board_editor.js";;
			$this->css[] = LIB.'summernote/dist/summernote.css';

			// $this->javascript[] = LIB.'summernote/dist/summernote-lite.js';
			// $this->javascript[] = LIB.'summernote/dist/lang/summernote-ko-KR.js';
			// $this->css[] = LIB.'summernote/dist/summernote-lite.css';
		endif;
		// End of 에디터

		// $this->benchmark->mark('board_construct_end');
	}

	public function index() {
        switch ( $this->method ) {
            case 'test':
                $this->test($this->post_id);
                break;
            case 'view':
                $this->view($this->post_id);
                break;
            case 'write':
                $this->write();
                break;
            case 'reply':
                $this->reply($this->post_id);
                break;
            case 'update':
            	$this->is_update = true;
                $this->update($this->post_id);
                break;
            case 'write_act':
                $this->write_act();
                break;
            case 'update_act':
                $this->update_act();
                break;
            case 'delete':
                $this->delete($this->post_id);
                break;
            case 'check_password':
                $this->check_password( $this->post_id );
                break;
            case 'comment_insert':
                $this->comment_insert($this->post_id);
                break;
            case 'comment_list':
                $this->comment_list($this->post_id);
                break;
            case 'comment_delete':
                $this->comment_delete($this->post_id);
                break;
            case 'comment_update':
            	$this->is_update = true;
                $this->comment_update($this->post_id);
                break;
            default:
            	/*============================
            	=            권한확인            =
            	============================*/

	            	// 읽기권한 없고, 강제가 아니고, 쓰기권한이 있을때 --- 쓰기전용
	            	if( !$this->auth->list
	            			&& $_GET['force']!='yes'
	            			&& $this->auth->write
	            		) :

	            		$this->write();

	            	// 읽기권한 없고, 쓰기권한도 없을때 --- 뒤로가기
	            	elseif( !$this->auth->list
	            			&& !$this->auth->write
	            		) :

	            		go_back();

	            	// 그 외 --- 목록화면
	            	else :

	            		$this->lists();

	            	endif;

            	/*=====  End of 권한확인  ======*/

                break;
        }
	}

	public function lists() {
		// 페이징 :: GET 방식
		$page  = $this->input->get('page') ? $this->input->get('page') : 1;

		// 페이지네이션 변수
		$limit = $this->input->get('limit') ? $this->input->get('limit') : $this->board_info->board_per_page;
		$this->data['start_no']    = ( ($page-1) * $limit ) + 1;

		// 총 게시물 수
		$this->data['total_count'] = $this->post_model
										->board_list_condition( $this->board_info )
										->count_by();

		// 정렬
        $this->post_model->order_by("post_is_notice", "DESC"); // 공지사항
        $this->post_model->order_by("post_family", "DESC");
        $this->post_model->order_by("post_family_seq", "ASC");

		// 리스트
		$this->data['list'] = $this->post_model
								->board_list_condition( $this->board_info )
								->paging($limit, $this->data['total_count'], $page)
								->get_all();

		// 페이지네이션
		$this->load->library('pagination');

		$pagination_config['base_url']   = $this->data['board_base']  . querystring('page');
		$pagination_config['total_rows'] = $this->data['total_count'];
		$pagination_config['per_page']   = $limit;

		$this->pagination->initialize( $pagination_config );
		$this->data['pagination'] = $this->pagination->create_links();

		// 리스트로 돌아올 경우를 위해, get 쿼리를 쿠기로 설정
		$list_query = querystring('force');

		$this->input->set_cookie('list_query', urlencode( $this->data['board_base'] . $list_query ), 60 * 60 * 24);

		// 렌더
		$this->_render('board/list');
	}

	// 권한 확인
	public function _check_basic($json_return = false) {
		$auth_to_view = false;

		$id = $this->post_id;

		// 기본 가능상태 조회
		try {

			$basic_check_method = null; // 체크할 기초 권한
			switch ( $this->method ) {
				case 'write' :
				case 'write_act' :
				case 'update' :
				case 'update_act' :
				case 'delete' :
					$basic_check_method = 'write';
					break;
				default :
					$basic_check_method = $this->method;
					break;
			}

			// 삽입 || 업데이트 시 카파차
			if( $this->config->item('recaptcha_sitekey') ) {
				if( $this->method=='write_act' || $this->method=='update_act' ) {
					$url = 'https://www.google.com/recaptcha/api/siteverify';
					$capacha_data = array(
						'secret' => $this->config->item('recaptcha_secretkey'),
						'response' => $this->input->post('g-recaptcha-response')
					);
					$captcha_success = get_api_json($url, $capacha_data);
					if( $captcha_success->success !== true )
						throw new Exception("'로봇이 아닙니다'를 체크해 주세요.", 1);

				}
			}

			// 기본권한 확인
			if( !$this->auth->{ $basic_check_method } )
				throw new Exception("권한이 없습니다.", 1);


			// 새글 메소드 여부
			if( strpos($this->method, 'write')!==false ) {
				// 새글 (액트 포함)
			} else {
				// 새글 아닌 모든 메소드

				// id 가 없을경우
				if( !is_numeric($id) )
					throw new Exception("게시물이 번호가 없습니다.", 1);

				// db 조회
				$this->view = $this->post_model
									->get($id);

				// 게시물 예외
				if( !$this->view->post_id )
					throw new Exception("게시물이 없습니다.", 1);
			}
			// End of 새글 메소드 여부

			// 메소드 별 권한 확인 분기
			switch ( $this->method ) {
				case 'view':
					// 조회권한 확인
					if( !$this->view->post_is_secret || is_board_admin() ) :
						// 비밀글아니거나 또는 관리자
						$auth_to_view = true;
					else :
						// 비밀글
						if( $this->view->post_mb_id
							 && $this->members->is_me($this->view->post_mb_id)
							) :
							// 회원글이고, 작성자일때
							$auth_to_view = true;
						else :
							// 손님용 암호 확인
							$this->load->library('encrypt');
							$hash_post_id = $this->input->cookie( "post_password" );
							if( $this->encrypt->decode( $hash_post_id ) == $id )
								$auth_to_view = true;
						endif;
					endif;
					// -- 조회권한 확인 (비밀글)

					// 첨부파일 로드 (용도에 따라 다르므로 컨트롤러에서 로딩)
					$this->view->file = $this->file_model
							->where('file_rel_type', 'board')
							->where('file_rel_id', $this->view->post_id)
							->get_all();

					break;
				case 'update':
					// 첨부파일 로드 (용도에 따라 다르므로 컨트롤러에서 로딩)
					$this->view->file = $this->file_model
							->where('file_rel_type', 'board')
							->where('file_rel_id', $this->view->post_id)
							->get_all();
				case 'update_act':
				case 'delete':
					// 본인확인
					if( is_board_admin()
						||
						( $this->view->post_mb_id && $this->members->is_me($this->view->post_mb_id) )
						) :
						// 작성회원이거나, 관리자일때
						$auth_to_view = true;
					elseif( $this->view->post_mb_id=='' ) :
						// 손님용 암호 확인
						$this->load->library('encrypt');
						$hash_post_id = $this->input->cookie( "post_password" );
						if( $this->encrypt->decode( $hash_post_id ) == $id )
							$auth_to_view = true;
					endif;

					break;
				default :
					$auth_to_view = true;
					break;
			}

			// 최종 조회권한 확인
			if( !$auth_to_view )
				throw new Exception("처리할 권한이 없습니다.", 1);

		} catch (Exception $e) {
			$result = array(
					'status' => 'fail',
					'msg' => $e->getMessage()
				);

			if( $json_return )
				kmh_json($result);
			else
				go_back( $result['msg'] );

			die();
		}
		// End of 기본 가능상태 조회
	}

	public function update($id) {
		$this->_check_basic();
		$this->data['update'] =& $this->view;
		$this->_render('board/write');
	}

	public function view($id) {
		$this->_check_basic();

		try {
	        // 조회 추가 -- 쿠키 값 없을경우만
	        if( !preg_match('/,' . $id . '/', $this->input->cookie('post_seen')) ) {
	            //업뎃
	            $this->post_model
	            	->set('post_hit', $this->view->post_hit + 1)
	            	->update($id);

	            // 쿠키 설정 (하루)
	            $this->input->set_cookie('post_seen', $this->input->cookie('post_seen') . ',' . $id, 60 * 60 * 24);
	        }

			// 업뎃 권한 확인
				// 비회원 글 일 경우
				if( !$this->view->post_mb_id && !is_board_admin() )
					$this->auth->need_password = true;
				// 내가 쓴 경우나 관리자
		        elseif( $this->members->is_me($this->view->post_mb_id) || is_board_admin() )
		            $this->auth->update  = true;

	        // 첨부파일 로드

	        // 이전, 다음글 링크
		} catch (Exception $e) {
			go_back( $e->getMessage() );
		}

        // 렌더
		$this->data['view'] =& $this->view;
		$this->_render('board/view');
	}

	public function reply($id) {
		$this->_check_basic();
		$this->data['reply'] =& $this->view;
		$this->_render('board/write');
	}

	public function write() {
		$this->_check_basic();
		$this->_render('board/write');
	}

	/*----------  실행  ----------*/

	public function write_act() {
		// 글 아이디가 있을 경우, 업데이트로 전달
		if( is_numeric( $this->input->post('post_id') ) )
			$this->update_act();

		$result = array();
		$result['status'] = 'fail';
		$result['msg'] = '에러가 발생했습니다.';

		$prefix = 'post_';
		$data_ex = array('post_id'); // 예외 컬럼
		$data = (object)db_filter($this->input->post(), $prefix, $data_ex);

		// 글작성
		try {
			$this->_check_basic(true);

			// 삽입 후 $data->post_family 업데이트 분기 (답글이 아닐경우 사용)
			$update_for_family = false;

			// 수동 데이터
				$data->post_board_id = $this->board_id;
				$data->post_is_secret = oz( $this->input->post('post_is_secret') );
				$data->post_is_notice = oz( $this->input->post('post_is_notice') );
				$data->post_ip = get_ip();
				if( $this->members->is_login() )
					$data->post_mb_id = $this->logined->mb_id;

			// 비밀글 필수 게시판은 모든 글 시크릿 설정
				if( $this->board_info->board_use_secret === 2 )
					$data->post_is_secret = 1;

			// 에디터 사용여부
				if( $this->board_info->board_use_editor )
					$data->post_use_editor = 1;

			// 답변글 아닐경우
				if( ! $data->post_parent ) :
					// $data->post_family     = $next_post_id;
					$update_for_family = true;
					$data->post_family_seq = 0;
					$data->post_depth      = 0;
			// 답변글일 경우
				else :
					// post_parent 는 뷰에서 넘어온다
					// post_parent / post_family / post_family_seq

					// 부모글 조회
					$parent_post = $this->post_model->get( $data->post_parent );

					// 부모글의 패밀리 상속 - 카테고리 포함
					$data->post_family     = $parent_post->post_family;
					$data->post_family_seq = $parent_post->post_family_seq + 1;
					$data->post_depth      = $parent_post->post_depth + 1;
					$data->post_category   = $parent_post->post_category;

					// 부모의 family_seq 보다 큰 패밀리들은 전부 시퀀스 + 1
					$this->post_model
						->where('post_family_seq >', $parent_post->post_family_seq)
						->set('post_family_seq', 'post_family_seq + 1', false)
						->update($where);
				endif;

			// 파일업로드 (임시코드로 업로드 후 추후에 게시글 번호로 업데이트)
			$this->load->library('files');
			$result['upload_result'] =
				$this->files->upload(
						'_post_files',
						"{$this->board_id}/".date('Ym'),
						strtolower( get_class($this) ),
						$this->input->post('_post_files_code'),
						'attach'
					);

			// $this->kmh->log( $result['upload_result'], 'upload_result' );

			// 인서트
			if( !$insert_id = $this->post_model->insert($data) )
				throw new Exception("등록에 실패했습니다.", 1);

			// 임시파일들 업데이트 (에디터도 있으므로 무조건)
			$file_update_result = $this->file_model
				->set('file_rel_id', $insert_id)
				->where('file_rel_id', $this->input->post('_post_files_code'))
				->update();
			// $this->kmh->log( $this->db->last_query(), '업로드 파일 임시코드 변경' );

			// 새글의 post_family 엡데이트
			if( $update_for_family )
				$this->post_model
					->set('post_family', $insert_id)
					->update($insert_id);


			$result['status'] = 'ok';
			$result['msg'] = '정상 처리되었습니다.';
			$result['id'] = $insert_id;

			// 액티비티
			$this->kmh->activity($insert_id, "새글작성 by {$this->logined->mb_display}({$this->logined->mb_id})", 'board', $this->method);

		} catch (Exception $e) {
			$result['status'] = 'fail';
			$result['msg'] = $e->getMessage();
		}
		// End of 글작성

		kmh_json($result);
	}

	public function update_act() {
		$result = array();
		$result['status'] = 'fail';
		$result['msg'] = '에러가 발생했습니다.';

		$prefix = 'post_';
		$data_ex = array(
			'post_id', 'post_board_id', 'post_mb_id', 'post_family', 'post_family_seq', 'post_parent', 'post_depth'
			); // 예외 컬럼
		$data = (object)db_filter($this->input->post(), $prefix, $data_ex);


		// 업데이트
		try {
			$this->_check_basic(true);

			// 수동 데이터
				$data->post_is_secret = oz( $this->input->post('post_is_secret') );
				$data->post_is_notice = oz( $this->input->post('post_is_notice') );

			// 비밀글 필수 게시판은 모든 글 시크릿 설정
				if( $this->board_info->board_use_secret === 2 )
					$data->post_is_secret = 1;

			// 에디터 사용여부
				if( $this->board_info->board_use_editor )
					$data->post_use_editor = 1;

			// 기존 파일 삭제시
				$this->load->library('files');
				$file_delete_result = $this->input->post('file_delete');

				if( count($file_delete_result) ) {
					$this->file_model
							->where_in('file_id',$file_delete_result)
							->delete();
				}



			// 파일업로드 (다이렉트로 게시물 아이디 등록)
				$this->load->library('files');
				$upload_result =
					$this->files->upload(
							'_post_files',
							"{$this->board_id}/".date('Ym'),
							strtolower( get_class($this) ),
							$this->post_id,
							'attach'
						);

			// 업데이트
			if( !$this->post_model->update($this->post_id, $data) )
				throw new Exception("수정에 실패했습니다.", 1);

			// 임시파일들 업데이트 (에디터도 있으므로 무조건)
			$file_update_result = $this->file_model
				->set('file_rel_id', $this->post_id)
				->where('file_rel_id', $this->input->post('_post_files_code'))
				->update();

			$result['status'] = 'ok';
			$result['msg'] = '정상 처리되었습니다.';
			$result['id'] = $this->post_id;

			// 액티비티
			$this->kmh->activity($this->post_id,
				"업데이트 by {$this->logined->mb_display}({$this->logined->mb_id})",
				'board',
				$this->method);
		} catch (Exception $e) {
			$result['status'] = 'fail';
			$result['msg'] = $e->getMessage();
		}
		// End of 글작성

		kmh_json($result);

	}

	public function delete() {

		// 삭제
		try {
			$this->_check_basic();

			// 답글 여부
				$is_parent = (bool)$this->post_model->where('post_parent', $this->post_id)->count_by();
				if( $is_parent )
					throw new Exception('답글이 있는 글은 삭제할 수 없습니다.', 1);

			if( !$this->post_model->delete($this->post_id) )
				throw new Exception('삭제에 실패했습니다.', 1);

			// 액티비티
			$this->kmh->activity($this->post_id,
				"삭제 by {$this->logined->mb_display}({$this->logined->mb_id})",
				'board',
				$this->method);

			redirect( $this->data['board_base'] );
		} catch (Exception $e) {
			go_back( $e->getMessage() );
		}
		// End of 삭제
	}

	// 비번 확인 (게스트 글)
	public function check_password( $id ) {
		if( !$this->input->is_ajax_request() ) exit;

		$this->load->library('encrypt');

		$result['status'] = 'fail';
		$result['msg'] = '에러가 발생했습니다.';

		$db = $this->post_model->get($id);

		try {
			// 예외처리
			if( !$db->post_id )
				throw new Exception("필요 정보가 없습니다.");
			if( !empty($db->post_mb_id) )
				throw new Exception("회원이 작성한 게시물 입니다.");
			if( $this->input->post('password') != $db->post_password )
				throw new Exception("비밀번호가 일치하지 않습니다.");

			// 성공
			$hash_post_id = $this->encrypt->encode($id);

            $cookie = array(
                'name'   => "post_password",
                'value'  => $hash_post_id,
                'expire' => '86500'
            );
            $this->input->set_cookie($cookie);

			$result['status'] = 'ok';
			$result['msg'] = '정상 처리되었습니다.';

			kmh_json($result);
		} catch (Exception $e) {
			$result['msg'] = $e->getMessage();
			kmh_json($result);
		}
	}


	/*----------  댓글  ----------*/

	public function _comment_check($json_return = false) {
		$auth_to_view = false;

		$id = $this->post_id; // 게시글 아이디 또는 코멘트 아이디

		// 기본 가능상태 조회
		try {
			// 기본권한 확인
			if( !$this->auth->comment )
				throw new Exception("권한이 없습니다.", 1);

			// 새글 메소드 여부
			if( strpos($this->method, 'comment_insert')!==false ) {
				// 새글 (액트 포함)
			} else {
				// 새글 아닌 모든 메소드

				// id 가 없을경우
				if( !is_numeric($id) )
					throw new Exception("게시물이 번호가 없습니다.", 1);
			}
			// End of 새글 메소드 여부

			// 메소드 별 권한 확인 분기
			switch ( $this->method ) {
				case 'comment_update':
				case 'comment_delete':
					$this->view = $this->comment_model->get( $id );
					if( !$this->view->cm_id )
						throw new Exception("해당 댓글이 존재하지 않습니다.", 1);

					// 본인확인
					if( is_board_admin()
						||
						( $this->view->cm_mb_id && $this->members->is_me($this->view->cm_mb_id) )
						) :
						// 작성회원이거나, 관리자일때
						$auth_to_view = true;
					elseif( $this->view->cm_mb_id=='' ) :
						// 손님용 암호 확인 (비회원 댓글은 현재 사용안함)
						$this->load->library('encrypt');
						$hash_cm_id = $this->input->cookie( "cm_password" );
						if( $this->encrypt->decode( $hash_cm_id ) == $id )
							$auth_to_view = true;
					endif;

					break;
				default :
					$auth_to_view = true;
					break;
			}

			// 최종 조회권한 확인
			if( !$auth_to_view )
				throw new Exception("처리할 권한이 없습니다.", 1);

		} catch (Exception $e) {
			$result = array(
					'status' => 'fail',
					'msg' => $e->getMessage()
				);

			if( $json_return )
				kmh_json($result);
			else
				go_back( $result['msg'] );

			die();
		}
		// End of 기본 가능상태 조회
	}

	public function comment_insert( $post_id ) {
		if( !$this->input->is_ajax_request() ) exit;

		$prefix = 'cm_';
		$data_ex = array('cm_id'); // 예외 컬럼
		$data = (object)db_filter($this->input->post(), $prefix, $data_ex);

		// 수동 데이터
		$data->cm_post_id = $post_id;
		$data->cm_mb_id = $this->logined->mb_id;
		$data->cm_ip = get_ip();

		$update_for_family = false;

			// 답변글 아닐경우
				if( ! $data->cm_parent ) :
					$update_for_family = true;
					$data->cm_family_seq = 0;
					$data->cm_depth      = 0;
			// 답변글일 경우
				else :
					// 부모글 조회
					$parent_comment = $this->comment_model->with_deleted()->get( $data->cm_parent );

					// 부모글의 패밀리 상속 - 카테고리 포함
					$data->cm_family     = $parent_comment->cm_family;
					$data->cm_family_seq = $parent_comment->cm_family_seq + 1;
					$data->cm_depth      = $parent_comment->cm_depth + 1;

					// 부모의 family_seq 보다 큰 패밀리들은 전부 시퀀스 + 1
					$this->comment_model
						->set('cm_family_seq', 'cm_family_seq + 1', false)
						->where('cm_family_seq >', $parent_comment->cm_family_seq)
						->update();
				endif;


		$result['status'] = 'fail';
		$result['msg'] = '에러가 발생했습니다.';

		if( $insert_id = $this->comment_model->insert($data) ) {
			// 새글의 comment_family 엡데이트
			if( $update_for_family )
				$this->comment_model
					->set('cm_family', $insert_id)
					->update($insert_id);

			$result['status'] = 'ok';
			$result['msg'] = '정상 처리되었습니다.';

			// 액티비티
			$this->kmh->activity($insert_id, "댓글작성 by {$this->logined->mb_display}({$this->logined->mb_id})", 'board', $this->method);
		} else {
			$result['msg'] = '등록에 실패했습니다.';
		}

		kmh_json($result);
	}

	public function comment_list( $post_id, $type = null ) {
		$data = array();
		$data['auth'] = $this->auth;
		$data['comments'] = $this->comment_model
					->with_deleted()
					->join('member', 'mb_id = cm_mb_id', 'left')
					->where('cm_type', $type)
					->where('cm_post_id', $post_id)
					->order_by('cm_family DESC, cm_family_seq ASC')
					->get_all();
		$this->load->view('board/module_comment_list', $data);
	}

	public function comment_delete( $comment_id ) {

		try {
			$result['status'] = 'fail';
			$result['msg'] = '에러가 발생했습니다.';

			$this->_comment_check(true);

			if( $this->comment_model->where('cm_parent', $comment_id)->count_by() )
				throw new Exception("댓글이 있는 댓글은 삭제가 불가능 합니다.", 1);

			if( !$this->comment_model->delete( $comment_id ) )
				throw new Exception("댓글 삭제에 실패했습니다.", 1);

			$result['status'] = 'ok';
			$result['msg'] = '정상 처리되었습니다.';
			// $result['id'] = $insert_id;

			// 액티비티
			$this->kmh->activity($comment_id, "댓글삭제 by {$this->logined->mb_display}({$this->logined->mb_id})", 'board', $this->method);

		} catch (Exception $e) {
			$result['status'] = 'fail';
			$result['msg'] = $e->getMessage();
		}

		kmh_json( $result );
	}

	public function comment_update( $comment_id ) {
		if( !$this->input->is_ajax_request() ) exit;

		try {
			$result['status'] = 'fail';
			$result['msg'] = '에러가 발생했습니다.';

			$this->_comment_check(true);

			if( $this->comment_model->where('cm_parent', $comment_id)->count_by() )
				throw new Exception("댓글이 있는 댓글은 수정이 불가능 합니다.", 1);

			$this->comment_model
				->set('cm_content', $this->input->post('cm_content') )
				->where('cm_id', $comment_id)
				->update();

			$result['status'] = 'ok';
			$result['msg'] = '정상 처리되었습니다.';

			// 액티비티
			$this->kmh->activity($comment_id, "댓글수정 by {$this->logined->mb_display}({$this->logined->mb_id})", 'board', $this->method);
		} catch (Exception $e) {
			$result['status'] = 'fail';
			$result['msg'] = $e->getMessage();
		}

		kmh_json( $result );
	}

}
