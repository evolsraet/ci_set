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

	public function depth0_code($data) {
		return substr($data, 0, 3);
	}

	public function as_nav($type='main') {
		$cate = array();
		$cate_sub = array();
		$cate_db = $this->db->where('cate_depth',0)->get('category')->result();
		foreach( (array) $cate_db as $key => $row ) :
			$cate[ $row->cate_id ] = $row->cate_name;
			$sub_db = $this->db->where('cate_depth', 1)->like('cate_id', $row->cate_id, 'after')->get('category')->result();
			foreach( (array) $sub_db as $sub_row ) :
				$cate_sub[ $row->cate_id ][ $sub_row->cate_id ] = $sub_row->cate_name;
			endforeach;
			// $cate_sub[ $row->cate_id ]= (object)$cate_sub[ $row->cate_id ];
		endforeach;

		switch ($type) {
			case 'sub':
				// return (object)$cate_sub;
				return $cate_sub;
				break;

			default:
				// return (object)$cate;
				return $cate;
				break;
		}
	}
}
