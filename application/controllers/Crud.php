<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
	커스텀 CRUD
 */

class Crud extends MY_Controller {

	public $baseuri = null;
	public $db_prefix = '';
	public $folder_name = '';
	public $model_name = '';

	public function __construct() {
		parent::__construct();

		$this->page_wrap = TRUE;
		$this->baseuri = '/' . $this->uri->segment(1)
			.'/' . $this->uri->segment(2)
			.'/' . $this->uri->segment(3) .'/';

		switch ($this->uri->segment(3)) {
			// case 'ip':
			// 	$this->db_prefix = 'wh_';
			// 	$this->folder_name = 'ip';
			// 	$this->model_name = 'tire/white_ip_model';
			// 	break;
			case 'config':
				$this->db_prefix = 'config_';
				$this->folder_name = 'config';
				$this->model_name = 'config_model';
				break;
			case 'member':
				$this->db_prefix = 'mb_';
				$this->folder_name = 'member';
				$this->model_name = 'member_model';
				break;
			// case 'audit':
			// 	$this->db_prefix = 'audit_';
			// 	$this->folder_name = 'audit';
			// 	$this->model_name = 'tire/audit_model';
			// 	break;
		}

		$this->load->model($this->model_name);
		// $this->model_name = str_replace('tire/', '', $this->model_name);

		// if( $this->uri->segment(1) == 'system' && !$this->members->is_system() )
		// 	show_error('권한이 없습니다.');

		// $function = 'car_excel';
		// kmh_print( $function() ); die();
	}

	public function index() {
		$this->_render("admin/{$this->folder_name}/list");
	}

	public function write_form($id=null) {
		if( $id != null ) :
			$this->data['view'] = $this->{$this->model_name}->with_deleted()->get($id);
		endif;

		$this->_render("admin/{$this->folder_name}/module_write_form",'AJAX');
	}

	public function insert_update_act() {
		$result = array();
		$result['status'] = 'fail';
		$result['msg'] = '에러가 발생했습니다.';

		// 글작성
		try {
			$data = (object) db_filter( $this->input->post(), $this->db_prefix);
			// $this->kmh->log($data);

			// 전처리
			switch ( $this->folder_name ) {
				case 'config' :
					throw new Exception("삭제가 불가능합니다.", 1);
					break;
			}


			$this->db->trans_start();

			// 디비
			if( $data->{$this->db_prefix . 'id'} && !$this->input->post('force_insert') ) :
				// $this->kmh->log($data);
				if( !$result['db_result'] = $this->{$this->model_name}->update($data->{$this->db_prefix . 'id'}, $data) ) : 
					throw new Exception("업데이트 처리 중 에러가 발생했습니다.", 1);				
				endif;

				$data->{$this->db_prefix . 'id'} = $this->input->post($this->db_prefix . 'id');
			else :
				if( !$result['db_result'] = $this->{$this->model_name}->insert($data) ) : 
					throw new Exception("등록 처리 중 에러가 발생했습니다.", 1);
				endif;

				$data->{$this->db_prefix . 'id'} = $result['db_result'];
			endif;

			// 파일
				$this->load->library('files');

				// 기존 파일 삭제시
					$file_delete_result = $this->input->post('file_delete');

					if( count((array) $file_delete_result) ) {
						$this->file_model
								->where_in('file_id',$file_delete_result)
								->where('file_rel_type', $this->folder_name)
								->delete();
					}
					
				// 파일업로드
				switch ( $this->folder_name ) {
					// case 'biz':
					// 	$file_upload = array(
					// 		'_biz_no_file' => 'biz_no'
					// 	);
					// 	break;
				}

				foreach( (array) $file_upload as $key => $row ) :
					$result['upload_result'][] =
						$upload_result = $this->files->upload(
								$key,
								"{$row}/".date('Ym'),
								$this->folder_name,
								$this->input->post("_{$this->folder_name}_files_code"),
								$row
							);
						if( $upload_result['status'] != 'ok' ) :
							throw new Exception($upload_result['msg'], 1);
						endif;
				endforeach;

				if( $data->{$this->db_prefix . 'id'} ) :
				// 임시파일들 업데이트 (에디터도 있으므로 무조건)
					$file_update_result = $this->file_model
						->set('file_rel_id', $data->{$this->db_prefix . 'id'})
						->where('file_rel_id', $this->input->post("_{$this->folder_name}_files_code"))
						->update();
					// $this->kmh->log( $this->db->last_query() );
					// $this->kmh->log( $data );
				endif;

			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) :
				throw new Exception("처리 중 에러가 발생했습니다.", 1);
			endif;

			$result['status'] = 'ok';
			$result['msg'] = '정상 처리되었습니다.';

		} catch (Exception $e) {
			$result['status'] = 'fail';
			$result['msg'] = $e->getMessage();
		}
		// End of 글작성
		
		kmh_json($result);		
	}

	public function delete_act() {
		$result = array();
		$result['status'] = 'fail';
		$result['msg'] = '에러가 발생했습니다.';

		// 글작성
		try {
			$data = (object) db_filter( $this->input->post(), $this->db_prefix );
			$result['post'] = $this->input->post();
			$result['data'] = $data;
			if( $data->{$this->db_prefix . 'id'} ) :
				if( !$result['db_result'] = $this->{$this->model_name}->delete($data->{$this->db_prefix . 'id'}) ) : 
					throw new Exception("삭제 중 에러가 발생했습니다.", 1);				
				endif;
			else :
				throw new Exception("정상적인 접근이 아닙니다.", 1);
			endif;

			$result['status'] = 'ok';
			$result['msg'] = '정상 처리되었습니다.';

		} catch (Exception $e) {
			$result['status'] = 'fail';
			$result['msg'] = $e->getMessage();
		}
		// End of 글작성
		
		kmh_json($result);
	}

	// 엑셀 다운로드
	public function excel_download() {
		ini_set('memory_limit','-1');
 		// 엑셀 선언
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		// 시트 선언
		$sheet = $spreadsheet->getActiveSheet();
		// 파일명
		$filename = $this->folder_name .'_'. get_date();			
		// 시트명
		$sheet->setTitle($this->folder_name);

		// 엑셀 배열
		$function_name = "{$this->folder_name}_excel";
		$field_array = $function_name();

		// 타이틀
		$column_index = 1;
		foreach( (array) $field_array as $key => $row ) :
			$sheet->setCellValueByColumnAndRow($column_index++, 1, $row);
		endforeach;		

		// 디비 리스트
		switch ($this->folder_name) {
			// case 'order':
			// 	$list = $this->order_model
			// 				->get_list(10000);

			// 	$list = $list['list'];
			// 	break;			
			default:
				$list = $this->{$this->model_name}->with_deleted()->get_all();
				break;
		}

		// 시트에 기록
		foreach( (array) $list as $key => $row ) :
			$row_index = $key + 2;

			$column_index = 1;
			foreach( (array) $field_array as $key_cell => $row_cell ) :
				// 모델 분기
				switch ( $this->folder_name ) {
					// 모델 구분
					case 'member':
						switch ($key_cell) {
							case 'mb_status':
								$row->{$key_cell} = mb_status($row->{$key_cell});
								break;
						}
						break;
					// 모델 구분
				}
				// 모델 분기
				
				// $this->kmh->log($key_cell, 'key_cell');
				// $this->kmh->log($row_cell, 'row_cell');
				// $this->kmh->log($row->{$key_cell}, 'row->key_cell');
				
				$sheet->setCellValueByColumnAndRow($column_index++, $row_index, $row->{$key_cell});
			endforeach;
		endforeach;

		// 자동너비
		for( $i = 1; $i < $column_index; $i++ ) :
			$sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
		endfor;

		// // 출력 헤더
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment;filename=".$filename.".xls");
		header('Cache-Control: max-age=0');

		// // 출력
 	// 	$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
		// $writer->save('php://output');

		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
		// $writer->save($filename.".xls");
		$writer->save('php://output');

		// die();
	}

	// public function excel_test() {
	// 	$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
	// 	$sheet = $spreadsheet->getActiveSheet();
	// 	$sheet->setCellValue('A1', 'Hello World !');

	// 	$writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
	// 	$writer->save('hello world.xlsx');		
	// }

 	// 엑셀 업로드
 	public function excel_upload() {
		ini_set('memory_limit','-1');
		debug_flush( "- 파일 업로드 진행중<br>" );

		// debug_flush( print_r($_POST,true) );
		// debug_flush( print_r($_FILES,true) );

		$this->load->library('upload');

		# 엑셀파일이 넘어오면
		if(isset($_FILES) && $_FILES['excel_file']['size']){

			// 업로드
			$upload_path = $this->config->item('file_path_php') . '/excel_upload/';
			$file = $this->logined->mb_id . "_{$this->folder_name}_" . date('YmdHis') .  '.xls';
			$_FILES['excel_file']['name'] = $file;

			// 폴더만들기
			if (is_dir($upload_path) === false) {
				mkdir($upload_path, 0777);
			}

			if ($_FILES['excel_file']['name']) {

				$uploadconfig = array();
				$uploadconfig['upload_path'] = $upload_path;
				$uploadconfig['allowed_types'] = 'xls';
				// $uploadconfig['max_size'] = 2048;
				$uploadconfig['overwrite'] = true;

				$this->upload->initialize($uploadconfig);
				if ($this->upload->do_upload('excel_file')) {
					 $filedata = $this->upload->data();

				} else {
					 $file_error = $this->upload->display_errors();
					 debug_flush( $file_error );
					 die();
				}
			}

			debug_flush( "- 파일 업로드 완료<br>" );
			debug_flush( "- 데이터 처리 중...<br>" );

			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
			$reader->setReadDataOnly(true);
			$spreadsheet = $reader->load( $upload_path . $file );
			$sheet = $spreadsheet->getActiveSheet();
			$array_data = $sheet->toArray();
			$total_count = count($array_data) - 1;
		 	debug_flush( " - 총 {$total_count} 건 업로드 시작<br>" );

			// 엑셀 배열
			$function_name = "{$this->folder_name}_excel";
			$field_array = $function_name();

			$this->db->trans_start();

			// 선처리
			switch ($this->folder_name) {
				case 'car':
				case 'desc':
				 	$this->{$this->model_name}->truncate();
			 		debug_flush( " - 기존 디비 삭제<br>" );
					break;
				case 'order':
			 		// debug_flush( "운송장 처리 개발중" );
			 		// die();
					break;
				default:
					# code...
					break;
			}

			// 삽입
			foreach( (array) $array_data as $row_key => $row ) :
				// 첫행 무시
				if( $row_key == 0 )
					continue;

		 		// debug_flush( trim($row[0]) );
		 		// debug_flush( trim($row[1]) );
				if( trim($row[0])=='' ) :
					debug_flush( "--- {$row_key} 행 무시됨 : A 필드 비어있음 ---<br>" );
					continue;
				endif;

		 		$data = new stdClass;
				$cell_field = array_keys($field_array);

				foreach( (array) $row as $cell_key => $cell ) :
					$cell = trim($cell);

					// 데이터 가공 - 모델 외
					switch ( $this->folder_name ) {
						default:
							break;
					}

					// 데이터 가공 - 공통
					switch ($cell_field[$cell_key]) {
						case $this->db_prefix . 'updated_at':
							$cell = get_datetime();
							break;

						case $this->db_prefix . 'deleted_at':
							if( $cell == '' ) $cell = null;

							if( $cell != '' && is_numeric($cell) ) :
								$cell = date(
											"Y-m-d h:i:s",
											\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($cell)
										);
							endif;

							break;
					}

					if( $cell_field[$cell_key] )
						$data->{$cell_field[$cell_key]} = $cell;
			 	endforeach;

			 	// 테이블 별 수동 데이터
				switch ( $this->folder_name ) {
					default:
						break;
				}

				// kmh_print($data); die();

			 	// 디비처리
				switch ( $this->folder_name ) {
				}			 	

				// 중간 보고
		 		if( $row_key % 1000 == 0 )
			 		debug_flush( "- {$row_key}건 처리중<br>" );

			endforeach;
			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) :
				debug_flush( "- 에러가 발생해 모든 업로드가 취소되었습니다. 데이터 확인 후 재 업로드 바랍니다." );
			else :
				debug_flush( "- 총 {$row_key}건 처리 완료. <strong>새로고침 해 주세요.</strong>" );
			endif;

			// 파일 삭제
	        @chmod($upload_path, DIR_WRITE_MODE);
	        if( !@unlink( $upload_path.$file ) ) {
				debug_flush( "<br>- 업로드한 파일 삭제 중 에러 : {$upload_path}{$file}" );
	        } else {
				debug_flush( "<br>- 업로드한 파일 삭제 완료" );
	        }

			die();
		} else {
			debug_flush( '<br>업로드 파일이 없거나 파일이 정상적이지 않습니다.' );
			debug_flush( '<br>'.print_r($_FILES,true) );
			die('<script>alert("파일이 없습니다.");</script>');
		}	 		
 	}

 	// 개별 펑션
 	
 	// public function audit_tuncate() {
		// $result = array();
		// $result['status'] = 'fail';
		// $result['msg'] = '에러가 발생했습니다.';

		// // 글작성
		// try {
		// 	if( $this->audit_model->truncate() ) {
		// 		$result['status'] = 'ok';
		// 		$result['msg'] = '정상 처리되었습니다.';
		// 	}		

		// } catch (Exception $e) {
		// 	$result['status'] = 'fail';
		// 	$result['msg'] = $e->getMessage();
		// }
		// // End of 글작성
		
		// kmh_json($result); 		
 	// }

}

