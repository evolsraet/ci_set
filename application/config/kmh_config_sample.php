<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *	index.php 에
 * 	define('ENVIRONMENT' ----- 설정할것 production or development
 *
 */

// 사이트 정보
	$config['site_title']		= '사이트이름';
	$config['site_description']	= '사이트 설명입니다';
	$config['site_author']		= 'Kang Minho';
	$config['site_keywords']	= '사이트이름,사이트설명';
	$config['site_email']		= 'noreply@email.com';

// 기본 템플릿
	// $config['base_template'] = "_boiler";
	$config['base_template'] = "_remark";
	$config['admin_template'] = "adminRemark";

// 회원가입 사용불가
	$config['deny_mb_id'] = array(
		'admin','administrator','관리자','운영자','어드민','주인장','webmaster','웹마스터','sysop','시삽','시샵','manager','매니저','메니저','root','루트','su','guest','방문객'
	);
	$config['deny_mb_nick'] = array(
		'admin','administrator','관리자','운영자','어드민','주인장','webmaster','웹마스터','sysop','시삽','시샵','manager','매니저','메니저','root','루트','su','guest','방문객'
	);
	$config['deny_mb_id'][] = $config['site_title'];
	$config['deny_mb_nick'][] = $config['site_title'];

// 회원 기본 아이디 필드 (미 적용시 members library 설정 값 사용)
	// $config['auth_field'] = 'mb_tid';


// 소셜 로그인 Hybridauth 2~
// SNS 설정의 리다이렉트 url = /member/social_endpoint?hauth.done=[PROVIDER]

	$protocol = 'http://';
	if ((isset($_SERVER['HTTPS']) && ( $_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1 ))
			|| (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'))
	{
		$protocol = 'https://';
	}

	$config['hybridauth'] = array(
		'base_url' => "{$protocol}{$_SERVER['HTTP_HOST']}/member/social_endpoint",

		// 'providers' => [
		// 	'Kakao' => [
		// 		'enabled' => true,
		// 		'keys' => [
		// 			'id'  => '',
		// 			'secret' => ''
		// 		]
		// 	],
		// 	'Google'   => [
		// 		'enabled' => true,
		// 		'keys' => [
		// 			'id'  => '',
		// 			'secret' => ''
		// 		]
		// 	]			
		// ],
		// 'providers' => [
		// 	'Twitter' => [
		// 		'enabled' => true,
		// 		'keys' => [
		// 			'key'    => 'gYkw6AZtkabcetdQENwk35cGF',
		// 			'secret' => 'dmmKBdAuonCe7wOM5Wrn3VCXNI3xuu0UyTnQaVSLpIkkcnSdPH'
		// 		]
		// 	],
		// 	'Facebook' => [
		// 		'enabled' => false,
		// 		'keys' => [
		// 			'id'  => '273286656096845',
		// 			'secret' => '273286656096845|N0BYJldf0wXrXLYRNsZJgiQ1tWg'
		// 		]
		// 	],
		// 	'Kakao' => [
		// 		'enabled' => true,
		// 		'keys' => [
		// 			'id'  => '1042e8835c8218348b36930857dae298',
		// 			'secret' => 'fg5OHIDTlqoDxyonGAB8iFsfxNTslwas'
		// 		]
		// 	]
		// ]
	);

// 모바일 전용 모드 사용 (컨트롤러 혹은 레이아웃)
	$config['mobile']     = false;
	$config['app_name']	  = 'agentname';
	$config['app_key_path'] = APPPATH . 'config/';

// 파일 저장위치 (스마트에디터는 해당 파일에서 추가로 수정)
	$config['file_path'] = '/uploads/';
	$config['file_path_php'] = "./uploads/";

// 컴포저 오토로드 사용
	// CI 3.1~ 어딘가에서 추가됨.
	// $config['composer_autoload'] = TRUE;
	// require_once APPPATH.'vendor/autoload.php';

// reCAPTCHA 2 사용 ( recaptcha_sitekey == false 시 사용 중단 )
	// 미사용
		$config['recaptcha_sitekey'] = false;
		$config['recaptcha_secretkey'] = false;
	// 개발
		// $config['recaptcha_sitekey'] = 'testtest';
		// $config['recaptcha_secretkey'] = 'testtest';
	// 실제
		// $config['recaptcha_sitekey'] = '';
		// $config['recaptcha_secretkey'] = '';

// 구글 애널리틱스
	$config['google_analytics'] = false;

// 기본 네비게이션
	$config['nav'] = array();
	$config['nav_sub'] = array();

	$config['nav']['home']                  = '이용안내';
	$config['nav']['board']                 = '게시판';

	$config['nav_sub']['board']['notice']   = '공지사항';
	$config['nav_sub']['board']['customer'] = '고객게시판';
	$config['nav_sub']['board']['free']     = '자유게시판';
	
	$config['nav_sub']['home']['info']      = '이용안내';

	// 회원
	$config['nav']['member']                = '마이페이지';
	$config['nav_sub']['member']['join']    = '회원가입';
	$config['nav_sub']['member']['update']  = '회원정보 수정';
	$config['nav_sub']['member']['login']   = '로그인';
	$config['nav_sub']['member']['find']    = '계정찾기';

// 관리자 메뉴
	$config['admin_nav'] = array();
	$config['admin_nav_sub'] = array();

	// $config['admin_nav']['dashboard'] = array(
	// 	'text'=>'대시보드',
	// 	'icon'=>'fa fa-fw fa-dashboard',
	// );
	$config['admin_nav']['setting'] = array(
		'text'=>'설정',
		'icon'=>'fa fa-fw fa-gears',
	);
	$config['admin_nav']['member'] = array(
		'text'=>'회원관리',
		'icon'=>'fa fa-fw fa-users',
	);
	// $config['admin_nav']['board'] = array(
	// 	'text'=>'게시글 관리',
	// 	'icon'=>'fa fa-fw fa-users',
	// );
	// $config['admin_nav']['etc'] = array(
	// 	'text'=>'기타 설정',
	// 	'icon'=>'fa fa-fw fa-toggle-off',
	// );

	$config['admin_nav_sub']['dashboard']['dashboard'] = '대시보드';
	$config['admin_nav_sub']['setting']['config']      = '환경설정';
	$config['admin_nav_sub']['setting']['popup']       = '팝업';
	$config['admin_nav_sub']['setting']['board']       = '게시판';
	$config['admin_nav_sub']['setting']['tab_chord']   = '코드';
	$config['admin_nav_sub']['member']['member']       = '회원';
	$config['admin_nav_sub']['member']['point']        = '포인트 관리';
	$config['admin_nav_sub']['board']['notice']        = '공지사항';
	$config['admin_nav_sub']['etc']['etc']             = '기타';
/*
// 관리자 메뉴 (몰)
	$config['admin_mall_nav'] = array();
	$config['admin_mall_nav_sub'] = array();

	$config['admin_mall_nav']['mall_config'] = array(
		'text'=>'쇼핑몰 설정',
		'icon'=>'fa fa-fw fa-folder',
	);
	$config['admin_mall_nav']['product'] = array(
		'text'=>'제품관리',
		'icon'=>'fa fa-fw fa-list-alt',
	);
	$config['admin_mall_nav']['order'] = array(
		'text'=>'주문서 관리',
		'icon'=>'fa fa-fw fa-shopping-cart',
	);
	$config['admin_mall_nav']['order_stat'] = array(
		'text'=>'매출 통계',
		'icon'=>'fa fa-fw fa-krw',
	);	
	
	$config['admin_mall_nav_sub']['mall_config']['category'] = '카테고리';
	$config['admin_mall_nav_sub']['product']['list'] = '제품관리';
*/

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