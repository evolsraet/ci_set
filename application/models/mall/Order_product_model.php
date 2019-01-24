<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Order_product_model extends MY_Model {

	public $prefix = 'op_';

	public function __construct()
	{
		parent::__construct();

		$this->protected[] = $this->primary_key;

		$this->created = false;
		$this->updated = false;
		$this->soft_delete = false;

		$this->load->model('mall/product_model');

		// 연결
		// $this->after_delete[] = 'after_delete';
		$this->before_create[] = 'options_encode';
		$this->before_update[] = 'options_encode';
		$this->after_get[] = 'options_decode';
		$this->before_get[] = 'with_product';
		// before_create
		// after_create
		// before_update
		// after_update
		// before_get
		// after_get
		// before_delete
		// after_delete
	}

	public function with_product() {
		$this->join('product', 'pd_id = op_pd_id');
	}

	public function options_decode($data) {
		$data->op_options = json_decode($data->op_options);
		return $data;
	}

	public function options_encode($data) {
		$data->op_options = json_encode($data->op_options);
		return $data;
	}

}
