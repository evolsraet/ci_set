<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
	TEST
*/

// textalk/websocket PHP 웹소켓 클라이언트
use WebSocket\Client;

class Chat extends MY_Controller {

	public function __construct() {
		parent::__construct();

    	// $this->load->helper('file');
    	// $this->load->helper('download');
    	// $this->load->library('files');
		$this->load->config('ratchet_client');
    	$this->load->library('ratchet_client');
	}

	public function run() {
        // Run server
        $this->ratchet_client->set_callback('auth', array($this, '_auth'));
        $this->ratchet_client->run();
	}

	public function user($user_id=null, $renderData=""){
		$this->data['user_id'] = $user_id;
		// 렌더
		$this->_render('pages/chat',$renderData);
	}

	// user_id 검증
	public function _auth($datas = null) {
		kmh_print('auth datas');
		kmh_print($datas);
		if( $this->members->is_login() )
			$datas->user_id = $this->logined->mb_id;
		else
			$datas->user_id = 'anom_' . $datas->user_id;

		return (!empty($datas->user_id)) ? $datas->user_id : false;
	}

	public function push() {
		$client = new Client(
				"ws://".$this->config->item('host', 'ratchet_client')
				.":".$this->config->item('port', 'ratchet_client')
			);

		// if( !$this->members->is_login() ) {
		// 	kmh_print('NOT LOGINNED');

		// } else {
			$msg = array(
		        'user_id'=> $this->logined->mb_id,
		        'recipient_id'=> ['anom_ks1996','anom_ks1995'],
		        'message'=> "ADMIN PUSH at ".get_datetime(null,'full'),
		        // 'broadcast' => true
		    );

			// $msg['message'] = $msg['message'].$msg['message'].$msg['message'].$msg['message'];

			$client->send( json_encode($msg) );
			// kmh_print( json_encode($msg) );

			// 스스로에게는 푸시되지 않도록 - 기본 - broadcast 는 가능
			echo $client->receive(); // Will output 'Hello WebSocket.org!'
		// }
	}
}
