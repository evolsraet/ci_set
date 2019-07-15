<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 컨트롤러와 중복을 막기위해 꼬릿말 s
class Boards {

    public function __construct() {
    	$this->CI =& get_instance();
    	$this->CI->load->helper('board');
        // $ci_session = $this->CI->session->userdata('member');
    }

    // latest
    public function latest($data) {
		// $latest['where'];
		// $latest['base_link'];	// null
		// $latest['after_link']; // null
		// $latest['count']; // 10
		// $latest['text_cut']; // 50
		// $latest['skin']; // basic
		// $latest['title']; // 최근게시물

    	if( !is_array($data['where']) )
    		$data['where'] = array(
    			'post_board_id' => $data['where']
    		);

    	if( empty($data['title']) )
    		$data['title'] = '최근 게시물';

    	if( empty($data['count']) )
    		$data['count'] = 10;

    	if( empty($data['skin']) )
    		$data['skin'] = 'basic';

    	if( empty($data['text_cut']) )
    		$data['text_cut'] = 50;

    	if( empty($data['base_link']) )
    		$data['base_link'] = base_url().'board/'.$data['where']['post_board_id'].'/';

    	$this->CI->load->model('post_model');
    	$data['list'] = $this->CI->post_model
    						->join('member', "mb_id = post_mb_id", 'left')
    						->where( $data['where'] )
    						->limit($data['count'])
    						->get_all();
    	// kmh_print($data);
    	return $this->CI->load->view("latest/{$data['skin']}/latest", $data, true);
    }
}
