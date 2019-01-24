<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Order_log_model extends MY_Model {

	public $prefix = 'ol_';
	public $carts_item = array();
	public $carts = array();

	public function __construct()
	{
		parent::__construct();

		$this->protected[] = $this->primary_key;

		$this->created = true;
	}

	public function simple_add( $order_id, $order_status ) {
		if( !$order_id || !order_status )
			throw new Exception("에러가 발생했습니다. simple_add", 1);

		$data = new stdClass;

		$data->ol_order_id = $order_id;
		$data->ol_order_status = $order_status;
		if( $this->members->is_login() )
			$data->ol_mb_id = $this->logined->mb_id;

		$this->insert($data);
	}
}
