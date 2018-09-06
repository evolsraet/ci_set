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
	}

	// observer
	public function before_delete($data) {

	}

}