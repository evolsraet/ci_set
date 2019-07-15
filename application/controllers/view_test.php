<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$nav = array();
		$nav_sub = array();

		$nav['setting'] = '설정';
		$nav['member'] = '회원관리';

		$nav_sub['setting']['popup'] = '팝업';
		$nav_sub['setting']['board_config'] = '게시판';
		$nav_sub['member']['member'] = '회원';

		$this->config->set_item('nav', $nav);
		$this->config->set_item('nav_sub', $nav_sub);
	}

	public function index() {
		redirect('/admin/v');
	}

	public function v() {
		$this->_render('/admin_vue/main');
	}

}