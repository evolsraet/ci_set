<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page extends MY_Controller {

	public function __construct() {
    	parent::__construct();
    }

	public function index() {
		$render = $_GET['render'] ? $_GET['render'] : '';

		$this->load->helper('file');

		// 테스트 페이지 IE9 미만 에러
		if( $this->uri->segment(1)=='vue' )
			$this->under_ie9_error = true;

		// 직접 로드 할 수 없음
		$this->data["uri_segment_1"] = $this->uri->segment(1);
		$this->data["uri_segment_2"] = $this->uri->segment(2);
		$this->data["uri_segment_3"] = $this->uri->segment(3);

		// [pages/1뎁스/2뎁스.php]
		// 없을 경우 [pages/basic.php]
		$fileType = '.php';
		switch( $this->uri->segment(1) ) {
			case 'admin':
				if( empty($this->data["uri_segment_3"]) ) $file = 'admin/'.$this->data["uri_segment_2"]."/default";
				else $file = 'admin/'.$this->data["uri_segment_2"].'/'.$this->data["uri_segment_3"];
				break;
			default:
				if( empty($this->data["uri_segment_2"]) ) $file = 'pages/'.$this->data["uri_segment_1"]."/default";
				else if( $this->uri->segment(1)=='page' ) $file = 'pages/'.$this->data["uri_segment_2"];
				else $file = 'pages/'.$this->data["uri_segment_1"].'/'.$this->data["uri_segment_2"];
				break;
		}

		$full_path = VIEWPATH.$file.$fileType;

		if( !file_exists($full_path) ) $file = "pages/default";
		// if( !file_exists($full_path) ) show_404();

		// echo $file;

		$this->_render($file,$render);
	}

}

?>
