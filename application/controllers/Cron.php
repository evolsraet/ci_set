<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends MY_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		echo 'this is index' . PHP_EOL;
	}

	public function test() {
		echo 'this is test' . PHP_EOL;
	}
}
