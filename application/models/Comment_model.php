<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Comment_model extends MY_Model {
	public $prefix = 'cm_';

	public function __construct()
	{
		parent::__construct();

		$this->protected[] = $this->primary_key;

		$this->created = true;
		$this->updated = true;
		$this->soft_delete = true;

		$this->before_delete[] = 'before_delete';
		$this->after_get[] = 'after_get';
	}

	public function after_get($data) {
		if( $data->cm_deleted_at ) :
			if( $this->members->is_admin() ) :
				$data->cm_content = '<span class="text-muted">[삭제된 댓글]<br></span>'.$data->cm_content;
			else :
				$data->cm_content = '<span class="text-muted">[삭제된 댓글]<br></span>';
			endif;
		endif;


		return $data;
	}

	// observer
	public function before_delete($data) {

	}

}