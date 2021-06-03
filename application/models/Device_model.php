<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Device_model extends MY_Model {

	public $prefix = 'dv_';

	public function __construct()
	{
		parent::__construct();

		// $this->protected[] = $this->primary_key;

		$this->created = true;
		// $this->updated = true;
		// $this->soft_delete = true;

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
}
