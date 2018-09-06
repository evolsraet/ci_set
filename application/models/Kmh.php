<?php defined('BASEPATH') OR exit('No direct script access allowed');

// KMH 공용 모델
// (라이브러리)

class Kmh extends CI_Model {

	public $array = array(); // array 함수용

	public function __construct()
	{
		parent::__construct();
	}

	// 개발용 로그	- 디비에 기록
	public function log($msg, $title='') {
		if( ENVIRONMENT != 'development' ) return;

		$trace = debug_backtrace();
		$trace = $trace[0];

		// kmh_print( $trace );

		if( $title != '' )	$this->db->set('title', $title);
		$this->db->set('controller', $this->router->class);
		$this->db->set('method', $this->router->method);
		$this->db->set('file', $trace['file']);
		$this->db->set('line', $trace['line']);
		$this->db->set('msg', print_r($msg, true) );
		$this->db->insert('log');
	}

	public function activity($rel, $msg, $type = null, $detail = null) {
		if( empty($rel) || empty($msg) )
			die("activity 기록 필수 입력 빠짐 {$this->router->class} {$this->router->method}");

		if( empty($type) ) 		$type = $this->router->class;
		if( empty($detail) ) 	$detail = $this->router->method;

		$this->db->set('ac_rel', 	print_r($rel, true) );
		$this->db->set('ac_msg', 	print_r($msg, true) );
		$this->db->set('ac_type', 	print_r($type, true) );
		$this->db->set('ac_detail',	print_r($detail, true) );
		$this->db->set('ac_ip', 	$this->input->ip_address() );
		$this->db->insert('activity');
	}

	// 배열 기능
		public function set_array( $array = array() ) {
			$this->array = (array) $array;
			return $this;
		}

		public function as_select($id, $selected = null, $class='', $default_text = '전체', $required = null) {
		}

		public function as_radio($id, $selected = null, $class='', $default_text = '전체', $required = null) {
		}

		public function as_ul($id, $selected = null, $link=null, $class='nav nav-pills', $default_text = '전체', $required = null) {
	        $active_init = $selected==''?"active":"";

	        if( !count($this->array) ) return false;

	        $result = "";
	        $result .= "<ul class=\"{$class}\">".PHP_EOL;
	        $result .= "    <li class=\"{$active_init}\">".PHP_EOL;
	        $result .= "        <a href=\"{$link}\">{$default_text}</a>".PHP_EOL;
	        $result .= "    </li>".PHP_EOL;

	        foreach( $this->array as $key => $row ) :
	            $active_row = $selected==$key?"active":"";

	            $result .= "        <li class=\"{$active_row}\">".PHP_EOL;
	            $result .= "            <a href=\"{$link}{$key}\">{$row}</a>".PHP_EOL;
	            $result .= "        </li>".PHP_EOL;
	        endforeach;
	        $result .= "</ul>".PHP_EOL;

	        return $result;
		}
}