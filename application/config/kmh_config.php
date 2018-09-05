<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *	index.php 에
 * 	define('ENVIRONMENT' ----- 설정할것 production or development
 *
 */

// 사이트 정보
	$config['site_title']		= '와토시스';
	$config['site_description']	= '와토시스';
	$config['site_author']		= 'WATOSYS';
	$config['site_keywords']	= '와토시스';

// 기본 템플릿
	// $config['base_template'] = "vue_boiler";
	$config['base_template'] = "_boiler";
	$config['admin_template'] = "adminLTE";

// 회원가입 사용불가
	$config['deny_mb_id'] = array(
		'admin','administrator','관리자','운영자','어드민','주인장','webmaster','웹마스터','sysop','시삽','시샵','manager','매니저','메니저','root','루트','su','guest','방문객'
	);
	$config['deny_mb_nick'] = array(
		'admin','administrator','관리자','운영자','어드민','주인장','webmaster','웹마스터','sysop','시삽','시샵','manager','매니저','메니저','root','루트','su','guest','방문객'
	);
	$config['deny_mb_id'][] = $config['site_title'];
	$config['deny_mb_nick'][] = $config['site_title'];

// 모바일 전용 컨트롤러 사용
	$config['mobile']     = false;

// 파일 저장위치 (스마트에디터는 해당 파일에서 추가로 수정)
	$config['file_path'] = '/uploads/';
	$config['file_path_php'] = "./uploads/";

// 컴포저 오토로드 사용
	// CI 3.1~ 어딘가에서 추가됨.
	// $config['composer_autoload'] = TRUE;
	// require_once APPPATH.'vendor/autoload.php';

// reCAPTCHA 2 사용 ( recaptcha_sitekey == false 시 사용 중단 )
	// $config['recaptcha_sitekey'] = false;
	// $config['recaptcha_secretkey'] = false;
	$config['recaptcha_sitekey'] = '6LcHD2sUAAAAAHzFZX66gAxFj3AUgB0Ztm1JQNk2';
	$config['recaptcha_secretkey'] = '6LcHD2sUAAAAADEW119E1XvpFeCIRncGE61YlEHE';

// 기본 네비게이션
	$config['nav'] = array();
	$config['nav_sub'] = array();

	$config['nav']['introduce'] = '전국유료바다낚시터협회';
	$config['nav']['partner']   = '회원사안내';
	$config['nav']['notice']    = '공지사항';
	$config['nav']['whatis']    = '바다낚시터란';
	$config['nav']['spot']      = '바다낚시터정보';
	$config['nav']['location']  = '오시는길';

	$config['nav_sub']['introduce']['welcome'] = '인사말';
	$config['nav_sub']['introduce']['vision'] = '비전';
	$config['nav_sub']['introduce']['history'] = '연혁';
	$config['nav_sub']['introduce']['organization'] = '조직도';
	$config['nav_sub']['introduce']['activity'] = '주요활동';

	$config['nav_sub']['partner']['partner']   = '회원사안내';
	$config['nav_sub']['notice']['notice']    = '공지사항';
	$config['nav_sub']['notice']['news']    = '낚시뉴스';
	$config['nav_sub']['notice']['challenge']    = '낚시대회 소식';
	$config['nav_sub']['whatis']['whatis']    = '바다낚시터란';
	$config['nav_sub']['spot']['spot']      = '바다낚시터정보';
	$config['nav_sub']['location']['location']  = '오시는길';

	$config['nav']['member']  = '마이페이지';
	$config['nav_sub']['member']['join']   = '회원가입';
	$config['nav_sub']['member']['update']   = '회원정보 수정';
	$config['nav_sub']['member']['login']   = '로그인';

// 관리자 메뉴
	$config['admin_nav'] = array();
	$config['admin_nav_sub'] = array();

	$config['admin_nav']['dashboard'] = array(
		'text'=>'대시보드',
		'icon'=>'fa fa-dashboard',
	);
	$config['admin_nav']['setting'] = array(
		'text'=>'설정',
		'icon'=>'fa fa-gears',
	);
	$config['admin_nav']['member'] = array(
		'text'=>'회원관리',
		'icon'=>'fa fa-users',
	);
	$config['admin_nav']['etc'] = array(
		'text'=>'기타 설정',
		'icon'=>'fa fa-toggle-off',
	);

	$config['admin_nav_sub']['dashboard']['dashboard'] = '대시보드';
	$config['admin_nav_sub']['setting']['popup'] = '팝업';
	$config['admin_nav_sub']['setting']['board'] = '게시판';
	$config['admin_nav_sub']['member']['member'] = '회원';
	$config['admin_nav_sub']['etc']['etc'] = '기타';


// 페이지네이션
	// $config['pagination']['full_tag_open'] = "<div class=\"pagination\"><ul>";
	// $config['pagination']['full_tag_close'] = "</ul></div>";
	// $config['pagination']['first_link'] = 'First';
	// $config['pagination']['first_tag_open'] = '<li>';
	// $config['pagination']['first_tag_close'] = '</li>';
	// $config['pagination']['last_link'] = 'Last';
	// $config['pagination']['last_tag_open'] = '<li>';
	// $config['pagination']['last_tag_close'] = '</li>';
	// $config['pagination']['next_link'] = '»';
	// $config['pagination']['next_tag_open'] = '<li>';
	// $config['pagination']['next_tag_close'] = '</li>';
	// $config['pagination']['prev_link'] = '«';
	// $config['pagination']['prev_tag_open'] = '<li>';
	// $config['pagination']['prev_tag_close'] = '</li>';
	// $config['pagination']['num_tag_open'] = '<li>';
	// $config['pagination']['num_tag_close'] = '</li>';
	// $config['pagination']['cur_tag_open'] = "<li class=\"active\"><a>";
	// $config['pagination']['cur_tag_close'] = '</a></li>';