<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Config_model extends MY_Model {

	public $prefix = 'config_';

	public function __construct()
	{
		parent::__construct();

		$this->protected[] = $this->primary_key;

		$this->created = true;
		$this->updated = true;

		// 연결
		// $this->after_get[] = 'after_get';

		// before_create
		// after_create
		// before_update
		// after_update
		// before_get
		// after_get
		// before_delete
		// after_delete
	}

	public function get_value($id) {
		$db = $this->get($id);
		if( isset($db->config_value) )
			return $db->config_value;
		else
			return false;
	}
}
