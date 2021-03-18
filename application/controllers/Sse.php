<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sse extends MY_Controller {

	public function __construct() {
		parent::__construct();
	}

	/*
		'읽지않은 메세지'와 같은 메세지 큐를 사용할경우,
		SSE 는 숏폴링으로 지속하므로,
		디비는
		- 푸시여부 (푸시 보낸건 다시 SSE 하지 않도록)
		- 확인여부 (푸시 보낸건 다시 SSE 하지 않도록)
		두 가지 플래그를 사용해야한다
	*/

	public function test() {
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache'); // recommended to prevent caching of event data.

		// $data = $this->db
		// 	->where('test_updated_at', null)
		// 	->get('test')
		// 	->row();

		// $this->db
		// 	->where('test_updated_at', null)
		// 	->set('test_updated_at', get_datetime())
		// 	->update('test');

		$serverTime = time();
		$msg = '';
		$msg .= "id: $serverTime" . PHP_EOL;
		$msg .= "data: msg";
			// $msg .= json_encode($data);
			$msg .= PHP_EOL;	// EO data
		$msg .= PHP_EOL;	// END OF STREAM

		echo $msg;
	}
}
