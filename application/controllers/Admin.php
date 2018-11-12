<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Carbon\Carbon;

class Admin extends MY_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		redirect('/admin/dashboard/dashboard','refresh');
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
				if( $date===get_datetime() )
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

	// CRUD 세팅

	function setting( $id ) {
		$this->load->library('grocery_CRUD');
		$crud = new grocery_CRUD();
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
				unset($columns[array_search('board_admin', $columns)]);
				unset($columns[array_search('board_extra_info', $columns)]);

				$crud->columns($columns)
					->set_field_upload('pu_file', $crud_file_path )
					->required_fields('pu_type', 'pu_title', 'pu_start', 'pu_end','pu_file');
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
					->callback_edit_field('mb_password',array($this,'set_password_input_to_empty'))
					->callback_add_field('mb_password',array($this,'set_password_input_to_empty'))
					->callback_before_update(array($this,'encrypt_password_callback'))
					->required_fields('pu_type', 'pu_title', 'pu_start', 'pu_end','pu_file');
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

		$this->_render('_template/crud',$renderData);
	}

	// CRUD 함수

	function set_password_input_to_empty() {
	    return "<input type='password' name='mb_password' value='' />";
	}

	function encrypt_password_callback($post_array, $primary_key) {
		$this->load->model('member_model');

		$post_array = $this->member_model->hash_password($post_array);

		return $post_array;
	}
}
