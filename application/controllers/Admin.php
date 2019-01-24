<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Carbon\Carbon;

class Admin extends MY_Controller {

	public $page_wrap = TRUE;

	public function __construct() {
		parent::__construct();

		$this->load->model('mall/order_model');
	}

	public function index() {
		redirect('/admin/order','refresh');
		// redirect('/admin/dashboard/dashboard','refresh');
	}

	public function dashboard() {
		Carbon::setLocale('ko');
		$day_before = 6;
		$limit_date = Carbon::now()->subDays($day_before);

		// 액티비티 통계
			// DB
			$activity_db = $this->db->select( "DATE_FORMAT(ac_created_at,'%Y-%m-%d') day, COUNT(*) as count", false )
						->where( 'ac_created_at >=', $limit_date->toDateString() )
						->group_by('day')
						->get('activity')->result();
			// DB -> 배열화
			for ($i=$day_before; $i >= 0; $i--) {
				$tmp_date = Carbon::now()->subDays($i)->toDateString();
				$activity_data[ $tmp_date ] = 0;
				foreach( (array) $activity_db as $row ) :
					if( $row->day == $tmp_date )
						$activity_data[ $tmp_date ] = $row->count;
				endforeach;
			}
			// JSON 용
			foreach( fe( $activity_data ) as $date => $count ) :
				$this->data['activity_count'][] = $count;
				if( $date===get_date() )
					$this->data['activity_key'][] = '오늘';
				else
					$this->data['activity_key'][] = Carbon::parse($date)->diffForHumans();
			endforeach;
			// to JSON
			$this->data['activity_count'] = json_encode($this->data['activity_count']);
			$this->data['activity_key'] = json_encode($this->data['activity_key']);

		// 렌더
		$this->_render('/admin/dashboard');
	}

	public function member() {
		$this->setting('member');
	}

	public function mall_config($id) {
		$this->setting($id);
	}

	public function product( $sub = null ) {
		if( empty($sub) ) redirect('/admin/product/list','refresh');

		$this->load->model('mall/product_model');
		$this->data['pd_id'] = $this->uri->segment(4);

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
		$this->_render("mall/admin/{$sub}");
	}

	public function option_list() {
		$this->load->model('mall/option_model');
		$this->_render('mall/modules/admin/option_list', 'AJAX');
	}

	public function option_update() {
		try {
			$this->load->model('mall/option_model');
			$data = db_filter( $this->input->post(), 'ot_' );
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

	// CRUD 세팅
	public function setting( $id ) {
		$this->load->library('grocery_CRUD');
		$crud = new grocery_CRUD();

		$crud->unset_jquery();
		// $crud->unset_jquery_ui();
		$crud->unset_bootstrap();

		$table_full = 'kmh_'.$id;
		$crud->set_table($table_full);
		// $crud->set_theme('datatables');

		$crud_file_path = $this->config->item('file_path_php').'crud';

		// crud 공통 (선행)
		$qry = "SHOW FULL COLUMNS FROM {$table_full};";
		$fields_info = $this->db->query($qry)->result();

		$required_fields = array();
		$fields = array();
		foreach ($fields_info as $key => $row) {
			// 코멘트
			$crud->display_as($row->Field,$row->Comment);

			// 필수입력
			if( $row->Null=='NO' && $row->Key!='PRI' ) {
				$required_fields[] = $row->Field;
			}

			// 필드추가
			if(
				1
				// $row->Field!="{$prefix}MOD_DATETIME"
				// && $row->Field!="{$prefix}ID"
				) {
				$columns[] = $row->Field;
			}

		}

		switch ($id) {
			case 'board':
				unset($columns[array_search('board_auth_file', $columns)]);
				unset($columns[array_search('board_use_editor', $columns)]);
				unset($columns[array_search('board_use_secret', $columns)]);
				unset($columns[array_search('board_category', $columns)]);
				// unset($columns[array_search('board_admin', $columns)]);
				unset($columns[array_search('board_extra_info', $columns)]);

				$this->load->model( 'member_model' );

				foreach( fe( $this->member_model->get_all() ) as $key => $row ) :
					$admin_list[$row->mb_id] = $row->{ $this->members->auth_field } . " [{$row->mb_display}]";
				endforeach;

				$crud->columns($columns)
					->set_field_upload('pu_file', $crud_file_path )
					->field_type('board_use_secret','dropdown',
						array(0 => '비활성', 1 => '활성', 2 => '필수')
						)
					->field_type('board_admin','multiselect',$admin_list);
				break;
			case 'category':
				$crud->columns( $columns )
			 		->fields( $columns )
			 		->field_type('cate_fullname', 'hidden')
					->callback_before_insert(array($this,'cate_fullname'))
					->callback_before_update(array($this,'cate_fullname'));
				break;
			case 'config':
				$crud->unset_add();
				$crud->unset_delete();
				$crud->field_type('config_id', 'readonly');
				$crud->field_type('config_desc', 'readonly');
				break;
			case 'member':
				unset($columns[array_search('mb_id', $columns)]);
				unset($columns[array_search('mb_updated_at', $columns)]);

				$fields = $columns;
				$columns = array(
					'mb_id', 'mb_tid', 'mb_email', 'mb_display', 'mb_created_at'
				);
				$crud->columns($columns)
					->fields( $fields )
					->unset_add()
					->unset_delete()
					->callback_edit_field('mb_password',array($this,'set_password_input_to_empty'))
					->callback_add_field('mb_password',array($this,'set_password_input_to_empty'))
					->callback_before_update(array($this,'encrypt_password_callback'));
				break;
			case 'popup':
				$crud->columns('pu_id','pu_type', 'pu_title', 'pu_start', 'pu_end', 'pu_align')
					->set_field_upload('pu_file', $crud_file_path )
					->required_fields('pu_type', 'pu_title', 'pu_start', 'pu_end','pu_file');
				break;
			default:
				break;
		}

		$this->data['output'] = $crud->render();

		$this->_render('admin/crud',$renderData);
	}

	public function test() {
			$post_array['cate_id'] = '100100';
			echo $find_cate = substr($post_array['cate_id'], 0, 3);
			$parent = $this->db->like('cate_id', $find_cate, 'after')
						->where('cate_depth', 0)
						->get('category')->row();
			echo $this->db->last_query();
			kmh_print( $parent );
	}

	// CRUD 함수
		// 카테고리 풀 네임
		public function cate_fullname($post_array, $primary_key) {
			if( $post_array['cate_depth'] > 0 ) :
				$find_cate = substr($post_array['cate_id'], 0, 3);
				$parent = $this->db->like('cate_id', $find_cate, 'after')
							->where('cate_depth', 0)
							->get('category')->row();
				$post_array['cate_fullname'] = $parent->cate_name . ' > ' . $post_array['cate_name'];
			else:
				$post_array['cate_fullname'] = $post_array['cate_name'];
			endif;

			return $post_array;
		}

		// 비밀번호 필드 생성
		public function set_password_input_to_empty() {
		    return "<input type='password' name='mb_password' value='' />";
		}

		// 비밀번호 암호화
		public function encrypt_password_callback($post_array, $primary_key) {
			$this->load->model('member_model');

			$post_array = $this->member_model->hash_password($post_array);

			return $post_array;
		}
}
