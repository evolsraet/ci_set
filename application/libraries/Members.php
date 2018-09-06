<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 컨트롤러와 중복을 막기위해 꼬릿말 s
class Members {

    public $auth_field = "mb_email";            // 기본 로그인 항목

    public function __construct() {
    	$this->CI =& get_instance();
    	// $this->CI->load->helper('file', 'kmh');
        // $ci_session = $this->CI->session->userdata('member');
    }

    public function is_login() {
        $ci_session = $this->CI->session->userdata('member');
        if( $ci_session->mb_id ) return true;
        else return false;
    }

    public function is_level( $vs_level ) {
        $ci_session = $this->CI->session->userdata('member');
       if( $vs_level == 0 ) return true;

        if( $ci_session->mb_level >= $vs_level ) return true;
        else return false;
    }
    public function is_me( $mb_id ) {
        $ci_session = $this->CI->session->userdata('member');
       if( !$mb_id || !$ci_session->mb_id ) return false;

        if( $ci_session->mb_id == $mb_id ) return true;
        else return false;
    }
    public function is_admin() {
        $ci_session = $this->CI->session->userdata('member');
        if( $ci_session->mb_level >= 100 ) return true;
        else return false;
    }

}
