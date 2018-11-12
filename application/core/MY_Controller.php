<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// use lodash\lodash;
// use Carbon\Carbon;

class MY_Controller extends CI_Controller{

	//Page Info
	protected $data = array();
	protected $pageName = FALSE;
	protected $template = FALSE;
	public $hasNav = TRUE;
	//Page contents
	protected $javascript = array();
	protected $css = array();
	protected $fonts = array();
	//Page Meta
	protected $title = FALSE;
	protected $description = FALSE;
	protected $keywords = FALSE;
	protected $author = FALSE;

	// 로그인 정보
	public $logined = false;

	// PJAX 메타
	public $pjax_meta = "pjax_meta_";

	// 모바일
	public $is_mobile = false;
	public $is_mobile_as_pc = false;

	function __construct()
	{
		parent::__construct();

		$this->template = $this->config->item('base_template');

		// 관리자
		if( $this->uri->segment(1)==='admin' ) {
			$this->template = $this->config->item('admin_template');
			if( !$this->members->is_admin() ) {
				redirect( "/member/login?redirect=".urlencode_path("/admin") );
			}
		}

		// 상수
		define( 'VIEWFOLDER', 	'/'.str_replace(FCPATH, '', VIEWPATH) );	// 루트기준 뷰 폴더
		define( 'TVIEW', 		VIEWPATH."_template/{$this->template}/" );
		define( 'TPATH', 		VIEWFOLDER."_template/{$this->template}/" );
		define( 'TIMG', 		VIEWFOLDER."_template/{$this->template}/img/" );
		define( 'TCSS', 		VIEWFOLDER."_template/{$this->template}/css/" );
		define( 'TJS', 			VIEWFOLDER."_template/{$this->template}/js/" );

		$this->data["uri_segment_1"] = $this->uri->segment(1);
		$this->data["uri_segment_2"] = $this->uri->segment(2);

		$this->title = $this->config->item('site_title');
		$this->description = $this->config->item('site_description');
		$this->keywords = $this->config->item('site_keywords');
		$this->author = $this->config->item('site_author');

		$this->pageName = strToLower(get_class($this));

		$this->logined = $this->session->userdata('member');

		// 모바일
		if( $this->config->item('mobile') ) {
			$this->load->library('user_agent');

			$this->is_mobile_mode = false;
			$this->is_mobile_as_pc = false;

			if( $_COOKIE['is_mobile_as_pc']=='yes' ) 		$this->is_mobile_as_pc = true;

			if( !$this->agent->is_mobile() ) 				$_COOKIE['is_mobile_as_pc']=='no';

			if( ($this->agent->is_mobile() && !$this->is_mobile_as_pc) || $this->uri->segment(1)=='mobile' )
				$this->is_mobile_mode = true;

			if( $this->is_mobile_mode && $this->uri->segment(1)!='mobile' && $this->uri->segment(1)!='member' )
				redirect('/mobile');

			if( $this->is_mobile_mode ) $this->template = "mobile";
		}
	}


	protected function _render($view, $renderData="FULLPAGE") {

		// PJAX meta
		// PJAX 구분 질 모든 요소를 넣는다
		// 	예 ) 메인 구분이 필요할 경우
		$this->pjax_meta .= $this->logined->mb_id.'_'.$this->template;

		// 개발 모드
		if( ENVIRONMENT == 'development' && $renderData!='JSON' ) {
			$this->output->enable_profiler(TRUE);
		}

		// PJAX
		if( $this->input->is_pjax_request() )
			$this->hasNav = false;

		switch ($renderData) {
			case "AJAX"     :
				$this->load->view($view,$this->data);
				break;
			case "JSON"     :
				echo json_encode($this->data);
				break;
			case "ONLYPAGE"     :
				$this->hasNav = false;
			case "FULLPAGE" :
			default :
				// nav
		  		$toTpl['nav'] 		= $this->config->item('nav');
				$toTpl['nav_sub'] 	= $this->config->item('nav_sub');
		  		$toTpl['admin_nav'] 		= $this->config->item('admin_nav');
				$toTpl['admin_nav_sub'] 	= $this->config->item('admin_nav_sub');

				//static
				$toTpl["javascript"] = $this->javascript;
				$toTpl["css"] = $this->css;
				$toTpl["fonts"] = $this->fonts;
				//meta
				$toTpl["title"] = $this->title;
				$toTpl["description"] = $this->description;
				$toTpl["keywords"] = $this->keywords;
				$toTpl["author"] = $this->author;
				//data
				$toTpl["content_body"] = $this->load->view($view,array_merge($this->data,$toTpl),true);
				$toTpl["basejs"] = $this->load->view("_template/_common/basejs",$this->data,true);

				//render view
				$this->load->view("_template/{$this->template}/template",$toTpl);

				break;
		}
	}
}