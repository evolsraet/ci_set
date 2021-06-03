<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// use lodash\lodash;
// use Carbon\Carbon;

class MY_Controller extends CI_Controller {

	//Page Info
	protected $data = array();
	public $template = FALSE;
	public $page_name = NULL;	// 페이지 명 (메뉴명 노출) - 네비에서 기본으로 가져오고 특정한 경우만 설정
	public $page_wrap = FALSE;	 // PANEL WRAP
	public $has_nav = TRUE;
	public $render_type = FALSE;
	public $under_ie9_error = FALSE;
	//Page contents
	public $javascript = array();
	public $javascript_bundle = array();
	public $css = array();
	public $fonts = array();
	//Page Meta
	protected $title = FALSE;
	protected $description = FALSE;
	protected $keywords = FALSE;
	protected $author = FALSE;

	// 로그인 정보
	public $logined = false;

	// PJAX 메타
	public $pjax_meta = "pjax_meta_";

	// 모바일 전용 모드 사용시 사용 됨
	public $is_mobile = false; // 모바일 인지 여부
	public $display_type = 'desktop'; // 설정된 디스플레이 타입 :: desktop || mobile

	function __construct()
	{
		parent::__construct();

		$this->template = $this->config->item('base_template');

		// 봇 로그
		// $this->load->library('user_agent');
		// if( $this->agent->is_robot() ) :
		// 	$this->db->insert(
		// 		'log_bot',
		// 		array(
		// 			'bot_info' => get_ip() . ' : ' . print_r( $_SERVER['HTTP_USER_AGENT'], true ),
		// 			'bot_name' => $this->agent->robot(),
		// 			'bot_url' => $_SERVER['REQUEST_URI']
		// 		)
		// 	);
		// endif;

		// IS_APP check
		if( strpos($_SERVER['HTTP_USER_AGENT'] , $this->config->item('app_name'))!==false ) :
			define('IS_APP', true);
		else :
			define('IS_APP', false);
		endif;
		// End of IS_APP check

		// 관리자
		if( $this->uri->segment(1)==='admin' ) {
			$this->template = $this->config->item('admin_template');
			if( !$this->members->is_admin() ) {
				redirect( "/member/login?redirect=".urlencode_path("/admin") );
			}
		}

		// 상수
		define( 'VIEWDIR', 	'/'.str_replace(FCPATH, '', VIEWPATH) );	// 루트기준 뷰 폴더
		define( 'MODULEPATH', 	VIEWPATH.'modules/' );						// PHP 모듈 폴더
		define( 'TPATH', 		VIEWPATH."_template/{$this->template}/" );	// PHP 템플릿 폴더
		define( 'TDIR', 		VIEWDIR."_template/{$this->template}/" );		// 루트기준 템플릿 폴더
		define( 'TIMG', 		VIEWDIR."_template/{$this->template}/img/" );	// 루트기준 템플릿 IMG
		define( 'TCSS', 		VIEWDIR."_template/{$this->template}/css/" );	// 루트기준 템플릿 CSS
		define( 'TJS', 			VIEWDIR."_template/{$this->template}/js/" );		// 루트기준 템플릿 JS


		$this->data["uri_segment_1"] = $this->uri->segment(1);
		$this->data["uri_segment_2"] = $this->uri->segment(2);

		$this->title       = $this->config->item('site_title');
		$this->description = $this->config->item('site_description');
		$this->keywords    = $this->config->item('site_keywords');
		$this->author      = $this->config->item('site_author');

		$this->logined = $this->session->userdata('member');

		// 모바일 (kmh_config 에 세팅되있는 경우 사용)
		if( $this->config->item('mobile') ) {
			$this->load->library('user_agent');

			// 모바일 기기인지 확인
			if( !$this->agent->is_mobile() )
				$this->is_mobile = true;

			// 모바일 기기라면
			if( $this->is_mobile ) :
				if( $_COOKIE['display_type']=='desktop' )
					$this->display_type = 'desktop';
				else
					$this->display_type = 'mobile';
			endif;

			// 모바일 상태라면, 모바일 템플릿 사용
			if( $this->display_type=='mobile' ) $this->template = "mobile";

			// 컨트롤러 변경 및 다른 방식으로 활용시 추가
			//
			// if( $this->display_type=='mobile' && $this->uri->segment(1)!='mobile' && $this->uri->segment(1)!='member' )
			// 	redirect('/mobile');
		}
	}


	protected function _render($view, $render_type="FULLPAGE") {
		if( $_GET['print'] == 'true' ) $render_type = 'PRINT';

		$this->render_type = $render_type ? $render_type : "FULLPAGE";

		// PJAX meta
		// PJAX 구분 질 모든 요소를 넣는다 (다를 경우 페이지 갱신됨)
		// 	예 ) 메인 구분이 필요할 경우,
		// 	( $this->segment->uri_segment(1) ) ? '.type_main' : '.type_sub'
		$this->pjax_meta .= "mb{$this->logined->mb_id}__tpl{$this->template}__rd{$render_type}";

		// 개발 모드
		if( ENVIRONMENT == 'development'
			&& !( $render_type=='JSON' || $render_type=='AJAX' )
		) {
			$this->output->enable_profiler(TRUE);
		}

		// PJAX
		if( $this->input->is_pjax_request() )
			$this->has_nav = false;

		// 렌더 타입별 분기
		switch ($render_type) {
			case "AJAX"     :	// 템플릿없이 뷰 파일내용만 (컨트롤러 요소는 포함)
				$this->load->view($view,$this->data);
				break;
			case "JSON"     :	// $view 를 JSON 으로
				echo json_encode($view);
				break;
			case "NO_NAV"   : 	// 네비만 제거
			case "PRINT"   : 	// 프린트 모드
				$this->has_nav = false;
			case "FULLPAGE" :	// 일반
			default :
				// nav
		  		$to_template['nav'] 			= $this->config->item('nav');
				$to_template['nav_sub'] 		= $this->config->item('nav_sub');
		  		$to_template['admin_nav'] 		= $this->config->item('admin_nav');
				$to_template['admin_nav_sub'] 	= $this->config->item('admin_nav_sub');
				//static
				$to_template["javascript"] 		= $this->javascript;
				$to_template["css"]        		= $this->css;
				$to_template["fonts"]      		= $this->fonts;
				//meta
				$to_template["title"]       	= $this->title;
				$to_template["description"] 	= $this->description;
				$to_template["keywords"]    	= $this->keywords;
				$to_template["author"]      	= $this->author;
				//data
				$to_template["content_body"] 	= $this->load->view($view,array_merge($this->data,$to_template),true);

				// IE9 미만 에러
				if( $this->under_ie9_error ) :
					$to_template["content_body"] =
						"
							<!--[if lt IE 9]>
								<div class=\"alert alert-danger text-center\">
									이 페이지는 인터넷 익스플로러 9 미만 브라우져에서는 작동되지 않습니다.
								</div>
								<div class=\"hidden\">
							<![endif]-->
						"
						. $to_template["content_body"]
						. "
							<!--[if lt IE 9]>
								</div>
							<![endif]-->
						";
				endif;
				// End of IE9 미만 에러

				// PANEL WRAP
				if( $render_type == 'PANEL' ) :
					$to_template["content_body"] =
						'<div class="panel">'
						.'<div class="panel-body">'
						. $to_template["content_body"]
						.'</div></div>';
				endif;
				// End of PANEL WRAP

				// 프린트모드 
				if( $render_type == 'PRINT' ) :
				endif;
				// End of 프린트모드 

				// 기본 JS by PHP
				$to_template["basejs"]       	= $this->load->view("_template/_common/basejs",$this->data,true);
				// 렌더링
				$this->load->view("_template/{$this->template}/template",$to_template);

				break;
		}
	}
}