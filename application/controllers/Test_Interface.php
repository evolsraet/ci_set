<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Test\Jobs;

class Test_Interface implements Jobs
{

	public function index() {
		echo "test";
	}

    public function do_a() {
    }

    public function do_b() {
    }
}
