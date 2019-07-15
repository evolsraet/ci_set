<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 컨트롤러와 중복을 막기위해 꼬릿말 s
class Members {

    public $auth_field = "mb_email";            // 기본 로그인 항목

    public function __construct() {
    	$this->CI =& get_instance();
        if( $this->CI->config->item('auth_field')!='' )
            $this->auth_field = $this->CI->config->item('auth_field');
    	// $this->CI->load->helper('file', 'kmh');
        // $ci_session = $this->CI->session->userdata('member');
    }

    public function update_login_info() {
        $this->CI->load->model('member_model');
        $ci_session = $this->CI->session->userdata('member');
        if( $ci_session->mb_id ) :
            $db = $this->CI->member_model->get( $ci_session->mb_id );
            $this->CI->session->set_userdata('member', $db);
            $this->CI->logined = $db;
        endif;
    }

    public function ask_member_count() {
        $this->CI->load->model('member_model');
        return $this->CI->member_model
            ->where('mb_status', 'ask')
            ->count_by();
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
