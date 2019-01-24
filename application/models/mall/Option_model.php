<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Option_model extends MY_Model {

	public $prefix = 'ot_';

	public function __construct()
	{
		parent::__construct();

		$this->protected[] = $this->primary_key;

		$this->created = true;
		$this->updated = false;
		$this->soft_delete = false;

		// 연결
		// $this->after_delete[] = 'after_delete';
		// $this->before_create[] = 'pd_min';
		// $this->before_update[] = 'pd_min';
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

	public function get_by_type( $pd_id ) {
		$result = array();

		$option_types = $this->option_model
			->select('ot_type')
			->where('ot_pd_id', $pd_id)
			->group_by('ot_type')
			->get_all();

		foreach( (array) $option_types as $key => $row ) :
			$result[ $row->ot_type ] = $this->option_model
										->where('ot_type', $row->ot_type)
										->where('ot_pd_id', $pd_id)
										->get_all();
		endforeach;

		return $result;
	}

}
