<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vue extends MY_Controller {

	public function index()
	{
		$this->_render('vue/index');
	}

}
