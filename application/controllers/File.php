<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
	파일 업로드 컨트롤러

	Files Library를 활용한 컨트롤러단
*/

class File extends MY_Controller {

	public function __construct() {
		parent::__construct();

        $this->load->helper('file');
    	$this->load->helper('download');
    	$this->load->library('files');
	}

	public function index($renderData=""){
		// 렌더
		// $this->_render('pages/home',$renderData);
		echo $this->files->get_type();
		echo $this->files->set_type_image('sdfsdfsdfsdfsdfs')->get_type();
		echo $this->files->get_type();
	}

	public function test() {
		$this->files->delete_old_tmp_files();
	}

	// 아작스로 단일 파일 업로드 후 url리턴
	public function editor_ajax_upload() {
		if( !$this->input->is_ajax_request() ) exit;

		$result['status'] = 'fail';
		$result['msg'] = '에러가 발생했습니다.';

		$upload_result =
			$this->files
				->set_type_image()
				->upload(
					'file',
					'editor/'.date('Ym'),
					$this->input->post('file_rel_type') ? $this->input->post('file_rel_type') : 'editor',
					$this->input->post('file_rel_id'),
					'editor'
				);

		$result['log'][] = $this->files->allowed_types;

		$result['status'] 	= $upload_result['status'];
		$result['imgurl'] 	= $upload_result['data'][0]['web_path'];
		$result['msg'] 		= $upload_result['msg'];

		kmh_json($result);
	}

	// 아작스로 단일 파일 삭제
	public function ajax_file_delete($file_id=null) {
		if( !$this->input->is_ajax_request() ) die('ajax only');

		$this->load->library('members');
		$this->load->model('post_model');

		$result = array();
		$result['status'] = 'fail';
		$result['msg'] = '에러가 발생했습니다.';

		// 글작성
		try {
			if( empty($file_id) )
				throw new Exception('정상적인 접근이 아닙니다.', 1);

			$db = $this->file_model->get( $file_id );
			if( !$db )
				throw new Exception("파일이 존재하지 않습니다.", 1);

			// 권한
			switch ($db->file_rel_type) {
				case 'member':
					if(
						$this->logined->mb_id != $db->file_rel_id
						&& !$this->members->is_admin()
						) {
						throw new Exception("권한이 없습니다.", 1);
					}
					break;
				case 'board':
					// 테스트 안됨
					$post_db = $this->post_model
						->join('member','mb_id = post_mb_id', 'left')
						->get( $db->file_rel_id );
					if(
						$this->logined->mb_id != $post_db->mb_id
						&& !$this->members->is_admin()
						) {
						throw new Exception("권한이 없습니다.", 1);
					}
					break;
				default:
					throw new Exception("삭제 가능한 파일이 아닙니다.", 1);
					break;
			}

			// 삭제
			if( !$this->file_model->delete($file_id) )
				throw new Exception("삭제 중 에러가 발생했습니다. 관리자에 문의하세요.", 1);

			$result['status'] = 'ok';
			$result['msg'] = '정상 처리되었습니다.';

		} catch (Exception $e) {
			$result['status'] = 'fail';
			$result['msg'] = $e->getMessage();
		}
		// End of 글작성

		kmh_json($result);
	}

	public function download( $file_id ) {
    	$file = $this->file_model->get($file_id);
        $url = $this->config->item('file_path_php').$file->file_folder.$file->file_save;

        // 히트 수 증가
        $this->file_model
        	->set('file_hit', 'file_hit + 1', false)
        	->update( $file_id );

        // $this->kmh->log( 'download' );
        // $this->kmh->log( $this->db->last_query() );

        // 다운로드
        force_download( $file->file_name, $url);
	}
}
