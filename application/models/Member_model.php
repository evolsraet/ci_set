<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Member_model extends MY_Model {

	public $prefix = 'mb_';

	public function __construct() {
		parent::__construct();

		$this->protected[] = $this->primary_key;

		$this->created = true;
		$this->updated = true;
		$this->soft_delete = true;

		$this->before_create[] = 'hash_password';
		$this->before_update[] = 'hash_password';

		// before_create
		// after_create
		// before_update
		// after_update
		// before_get
		// after_get
		// before_delete
		// after_delete

	}


	public function hash_password($data) {
		if( $data ) $data = (object) $data;

		if( isset($data->mb_mobile) )
			$data->mb_mobile = only_number($data->mb_mobile);

		if( empty($data->mb_password) ) :
			unset( $data->mb_password );
		else :
			$data->mb_password = $this->encryption->encrypt($data->mb_password);
		endif;

		return $data;
	}

}
