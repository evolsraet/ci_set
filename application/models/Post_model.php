<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Post_model extends MY_Model {

	public $prefix = 'post_';

	public function __construct()
	{
		parent::__construct();

		$this->protected[] = $this->primary_key;

		$this->created = true;
		$this->updated = true;
		// $this->soft_delete = true;

		// 연결
		$this->has_one['member'] = array('Member_model','mb_id','post_mb_id');

		$this->after_delete[] = 'after_delete';
		$this->before_create[] = 'before_create';
		$this->after_create[] = 'after_create';
		$this->after_get[] = 'after_get';

		// before_create
		// after_create
		// before_update
		// after_update
		// before_get
		// after_get
		// before_delete
		// after_delete
	}

	// 트리거
	protected function before_create($data) {
		return $data;
	}

	protected function after_get($data) {
        // HTML 확인
        if( !$data->post_use_editor )
        	$data->post_content = nl2br($this->view->post_content);

       	return $data;
	}

	protected function after_create($data) {
		return $data;
	}

	protected function after_delete($data) {
		$delete_post_ids = array();
		foreach( fe( $data['data'] ) as $key => $row ) :
			$delete_post_ids[] = $row->post_id;
		endforeach;

		$this->file_model
			->where('file_rel_type', 'board')
			->where_in( 'file_rel_id',  $delete_post_ids)
			->delete();
	}

	// 환경설정
	public function get_board_info($id) {
		$this->_database->where('board_id', $id);
		return $this->_database->get('board')->row();
	}

	public function get_board_count() {
		return $this->_database->count_all_results('board');
	}

	// 목록 검색 조건
	public function board_list_condition( &$board_info ) {
		// $this->console->log( $this );
		// 게시판
		$this->join('member', "mb_id = post_mb_id", 'left');
		$this->where('post_board_id', $board_info->board_id);

		// 카테고리
		if( $this->input->get('category') )
			$this->where('post_category', $this->input->get('category') );

		// 검색어
        if( $this->input->get('skey') ) :
        	switch ( trim($this->input->get('stx')) ) {
        		case '_writer':
        			// 작성자
        			$this
        				->group_start()
	        				->like('post_writer', $this->input->get('skey'))
	        				->or_like('mb_display', $this->input->get('skey'))
        				->group_end();
        			break;
        		case '':
		        	// 검색어만 있을때 동시검색
		        	$this
        				->group_start()
	        				->like('post_title', $this->input->get('skey'))
	        				->or_like('post_content', $this->input->get('skey'))
        				->group_end();
        			break;
        		default:
		        	// 해당 필드 like 검색
		        	$this->like( $this->input->get('stx'), $this->input->get('skey') );
        			break;
        	}
        endif;

		return $this;
	}

}
