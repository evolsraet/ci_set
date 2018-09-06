<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Test_model extends MY_Model {

	/*
	public $prefix = '';
	public $table = 'test'; // you MUST mention the table name
	public $primary_key = 'id'; // you MUST mention the primary key
	public $protected = array('id');
	*/

	public $prefix = 'test_';

	public function __construct()
	{
		parent::__construct();

		$this->protected = array(
			$this->primary_key,
			);

		$this->soft_delete = true;

		$this->before_create[] = 'before_create';
		$this->after_create[]  = 'after_create';
		$this->before_update[] = 'before_update';
		$this->after_update[]  = 'after_update';
		$this->before_get[]    = 'before_get';
		$this->after_get[]     = 'after_get';
		$this->before_delete[] = 'before_delete';
		$this->after_delete[]  = 'after_delete';
		$this->before_soft_delete[] = 'before_soft_delete';
		$this->after_soft_delete[]  = 'after_soft_delete';
	}

	public function test_qb() {
		/*
		$this->where('test_id', 40);
		kmh_print( $this->qb_functions->where );

		$this->_database
			->group_start()
				->like('test_id_copy', 1)
				->or_like('test_varchar', 1)
			->group_end();

		$this->get_query_builder_where();
		*/

		$this
			->group_start()
				->like('test_id_copy', 1)
				->or_like('test_varchar', 1)
			->group_end();

		// kmh_print( __FUNCTION__ );
		// kmh_print( $this->qb_functions );
		// kmh_print( $this->qb_functions->group_start );
		// kmh_print( $this->qb_functions->group_end );
		$result = $this->get_all();
		kmh_print( count($result) );
	}


	public function before_create($data) {
		$this->console->log( array('before_create', $data) );
		return $data;
	}
	public function after_create($data) {
		$this->console->log( array('after_create', $data) );
		return $data;
	}
	public function before_update($data) {
		$this->console->log( array('before_update', $data) );
		return $data;
	}
	public function after_update($data) {
		$this->console->log( array('after_update', $data) );
		return $data;
	}
	public function before_get($data) {
		$this->console->log( array('before_get', data ));
		return $data;
	}
	public function after_get($data) {
		$this->console->log( array('after_get', $data) );
		return $data;
	}
	public function before_delete($data) {
		$this->console->log( array('before_delete', $data) );
		return $data;
	}
	public function after_delete($data) {
		$this->console->log( array('after_delete', $data) );
		return $data;
	}
	public function before_soft_delete($data) {
		$this->console->log( array('before_soft_delete', $data) );
		foreach( fe( $data['data'] ) as $key => $row ) :
			$this->console->log( "DELETE {$row->test_id} in before_soft_delete" );
		endforeach;
		return $data;
	}
	public function after_soft_delete($data) {
		$this->console->log( array('after_soft_delete', $data) );
		return $data;
	}
}
