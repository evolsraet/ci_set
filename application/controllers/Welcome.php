<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *		강민호
 *
 * 		composer 사용법
 *   	1. 패키지 등록
 *       	1-1. application/composer.json 수정
 *        		1-1-2. composer update (composer install 이후)
 *     	    1-2. composer require [이름]
 *    	2. 어디서든 use 로 사용 (클래스 내부에서는 쓸수 없음)
 *     		예 : use SimpleExcel\SimpleExcel;
 *
 */

// use Faker\Factory;
// use Sunra\PhpSimple\HtmlDomParser;

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		// echo 'test';
		$this->load->view('welcome_message');
	}

	public function info() {
		phpinfo();
	}

	// 로그인 크롤링 예제
	public function snoopy() {
		kmh_print( 'start' );
		$snoopy = new \Snoopy\Snoopy();

		$login_url = 'http://211.172.225.124:90/login';
		// $data["mem_userid"] = "admin";
		// $data["mem_password"] = "admin1234";
		// $snoopy->submit($login_url,$data);

		$url = "http://211.172.225.124:90/admin/bdsr/owner";

		if( $snoopy->fetch($url) )
		{
			echo "response code: ".$snoopy->response_code."<br>\n";
			while(list($key,$val) = each($snoopy->headers))
				echo $key.": ".$val."<br>\n";
			echo "<p>\n";

			// kmh_print( $snoopy );

			$html = HtmlDomParser::str_get_html( $snoopy->results );

			foreach( (array) $html->find('.nav') as $key => $row ) :
				kmh_print( $row->innertext );
			endforeach;
		}
		else
			echo "error fetching document: ".$snoopy->error."\n";
	}
}
