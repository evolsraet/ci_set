<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
	Admin 기본 기능 및 Grocery Crud 베이스 세팅
 */

use Carbon\Carbon;

class Admin extends MY_Controller {

	public $page_wrap = TRUE;

	public function __construct() {
		parent::__construct();

		$this->load->model('mall/order_model');
		$this->load->model('member_model');
	}

	public function index() {
		redirect('/admin/order','refresh');
		// redirect('/admin/dashboard/dashboard','refresh');
	}

	// 통계
	public function order_stat() {
		switch ( $_GET['sort'] ) {
			case 'year':
				$format = '%Y';
				break;
			case 'month':
				$format = '%Y-%m';
				break;
			case 'day':
				$format = '%Y-%m-%d';
				break;
			case 'week':
			default:
				$_GET['sort'] = 'week';
				$format = '%x년 %v주';
				break;
		}

		if( !$this->input->get('start') ) $_GET['start'] = date('Y-m') . '-01';
		if( !$this->input->get('end') ) $_GET['end'] = date('Y-m-d');

		$this->data['stat'] = $this->order_model->stat(
									$this->input->get('start'),
									$this->input->get('end'),
									$format
								);

		$this->_render('mall/admin/stat');
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
				$this->load->helper('member_helper');

				unset($columns[array_search('mb_id', $columns)]);
				unset($columns[array_search('mb_updated_at', $columns)]);

				$fields = $columns;
				$columns = array(
					'mb_id', 'mb_tid', 'mb_email', 'mb_status', 'mb_display', 'mb_created_at'
				);
				$crud->columns($columns)
					->fields( $fields )
					->unset_add()
					->unset_delete()
					->field_type( 'mb_status','dropdown',mb_status() )
					->callback_edit_field('mb_password',array($this,'set_password_input_to_empty'))
					->callback_add_field('mb_password',array($this,'set_password_input_to_empty'))
					->callback_before_update(array($this,'encrypt_password_callback'));
				break;
			case 'popup':
				$crud->columns('pu_id','pu_type', 'pu_title', 'pu_start', 'pu_end', 'pu_align')
					->set_field_upload('pu_file', $crud_file_path )
					->required_fields('pu_type', 'pu_title', 'pu_start', 'pu_end','pu_file');
					// ->required_fields('pu_type', 'pu_title', 'pu_start', 'pu_end');
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
				$post_array['cate_fullname'] = '';
				$parent = array();
				for ($depth=0; $depth < $post_array['cate_depth']; $depth++) {
					$find_cate = get_cate_id($post_array['cate_id'], $depth);
					$parent[] = $this->db->where('cate_id', $find_cate)
								->where('cate_depth', $depth)
								->get('category')->row();
				}
				foreach( (array) $parent as $key => $row ) :
					$post_array['cate_fullname'] .= $row->cate_name . ' > ';
				endforeach;
				$post_array['cate_fullname'] .= $post_array['cate_name'];
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

			$post_array = (array) $this->member_model->hash_password($post_array);

			return $post_array;
		}
}
