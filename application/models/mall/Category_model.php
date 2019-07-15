<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Category_model extends MY_Model {

	public $prefix = 'cate_';

	public function __construct()
	{
		parent::__construct();

		$this->protected[] = $this->primary_key;

		$this->created = false;
		$this->updated = false;
		// $this->soft_delete = true;

		// before_create
		// after_create
		// before_update
		// after_update
		// before_get
		// after_get
		// before_delete
		// after_delete
	}

	public function as_nav($full_depth=0) {
		$cate = array();

		for ($find_depth=0; $find_depth <= $full_depth; $find_depth++) :
			$cate_db = $this->category_model->where('cate_depth',$find_depth)->get_all();
			$parent_depth = $find_depth-1;
			foreach( (array) $cate_db as $key => $row ) :
				if( $find_depth > 0 ) :
					$cate[$find_depth][ get_cate_id($row->cate_id, $parent_depth) ][$row->cate_id] = $row->cate_name;
				else:
					$cate[$find_depth][$row->cate_id] = $row->cate_name;
				endif;
			endforeach;
		endfor;

		return $cate;
	}
}
