<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
 *     		예 : use Faker\Factory;
 *     		예 : use Less\Parser;
 *
 */

use Carbon\Carbon;
use Faker\Factory;
// $parser = new Less_Parser();

class Home extends MY_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index($renderData=""){
		$this->_render('pages/home');
	}

	public function info() {
		phpinfo();
	}

	public function goaccess() {
		$this->load->helper('file');
		$this->load->library('table');

		// 데이터 로드
		$json_file = FCPATH . 'goaccess.json';
		$fulldata = json_decode( read_file($json_file) );

		// 테이블 설정
		$template = array(
				'table_open'         => '<table width="100%" border="1" cellpadding="2" cellspacing="1" class="mytable">',
				'heading_cell_start' => '<th style="text-align: center">',
				'cell_start'         => '<td style="text-align: center">',
				'cell_alt_start'     => '<td style="text-align: center">'
		);

		$this->table->set_template($template);

		// 메타데이터
		$table_data = array();
		$table_data[] = array('시작일자','마지막일자','총 방문자','일별 최대 방문자수');
		$table_data[] = array(
							Carbon::createFromFormat(
									'd/F/Y',
									$fulldata->general->start_date
								)->toDateString(),
							Carbon::createFromFormat(
									'd/F/Y',
									$fulldata->general->end_date
								)->toDateString(),
							number_format($fulldata->visitors->metadata->visitors->count),
							number_format($fulldata->visitors->metadata->visitors->max),
						);

		$this->data['tables']['meta']['title']
			= '메타데이터';
		$this->data['tables']['meta']['table']
			= $this->table->generate($table_data);

		// 일별 방문자
		$table_data = array();
		$table_data[] = array('순번','날짜','방문자수','비율');

		foreach( (array) $fulldata->visitors->data as $key => $row ) :
			$table_data[] = array(
								$key + 1,
								Carbon::parse($row->data)->toDateString(),
								number_format($row->visitors->count),
								$row->visitors->percent
							);
		endforeach;
		$this->data['tables']['daily']['title']
			= '일별 방문자';
		$this->data['tables']['daily']['table']
			= $this->table->generate($table_data);

		// 시간대별 방문자수
		$table_data = array();
		$table_data[] = array('시간','방문자수','방문자비율','클릭수');

		foreach( (array) $fulldata->visit_time->data as $key => $row ) :
			$table_data[] = array(
								$key,
								number_format($row->visitors->count),
								$row->visitors->percent,
								number_format($row->hits->count),
							);
		endforeach;
		$this->data['tables']['hour']['title']
			= '시간대별 방문자수';
		$this->data['tables']['hour']['table']
			= $this->table->generate($table_data);

		// IP별 방문자수
		$table_data = array();
		$table_data[] = array('순번', '아이피','방문자수');

		foreach( (array) $fulldata->hosts->data as $key => $row ) :
			$table_data[] = array(
								$key + 1,
								$row->data,
								number_format($row->visitors->count),
							);
		endforeach;
		$this->data['tables']['host']['title']
			= 'IP별 방문자수';
		$this->data['tables']['host']['table']
			= $this->table->generate($table_data);

		// 렌더
		$this->_render('pages/goaccess', 'AJAX');
	}

	public function email_test() {
		$mail_content  = "
			<p class=\"callout\">
				<a href=\"http://{$_SERVER['HTTP_HOST']}/admin/order_write/{$order_insert_id}\">새 주문이 접수되었습니다.</a>
			</p>						
		";
		$mail_content  .= email_comment_noreply();

		// 메일 발송
		$email_result = email_send(
							null,
							null,
							'ks1995@gmail.com',
							'새 주문 알림',
							$mail_content
						);
		if( $email_result !== true )
			$this->kmh->log($email_result, '메일 발송 에러');

		kmh_print( $email_result );
	}

	public function php_info() {
		// phpinfo();
	}

	public function table_row_test() {
		$this->load->view('pages/table_row_test');
	}

	public function make_dummy() {
		$this->output->enable_profiler(TRUE);
		// echo 'f';
		// return false;

		$faker = Faker\Factory::create('ko_KR');

		  // `bd_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '아이디',
		  // `bd_bc_id` varchar(45) NOT NULL DEFAULT '' COMMENT '설정 아이디',
		  // `bd_mb_id` int(11) DEFAULT NULL COMMENT '회원 아이디',
		  // `bd_category` varchar(45) DEFAULT NULL COMMENT '카테고리',
		  // `bd_writer` varchar(100) DEFAULT NULL COMMENT '작성자(비회원)',
		  // `bd_password` varchar(255) DEFAULT NULL COMMENT '비밀번호(비회원)',
		  // `bd_family` int(11) DEFAULT NULL COMMENT '패미리',
		  // `bd_family_seq` int(11) DEFAULT '0' COMMENT '패미리순서',
		  // `bd_rel` int(11) DEFAULT NULL COMMENT '연관글',
		  // `bd_depth` smallint(2) DEFAULT '0' COMMENT '답글뎁스',
		  // `bd_title` varchar(255) DEFAULT NULL COMMENT '제목',
		  // `bd_content` longtext COMMENT '내용',
		  // `bd_file` varchar(255) DEFAULT NULL COMMENT '파일',
		  // `bd_hit` int(11) DEFAULT NULL COMMENT '조회수',
		  // `bd_is_secret` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '비밀글',
		  // `bd_is_notice` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT '공지글',
		  // `bd_created_at` timestamp NULL DEFAULT NULL COMMENT '생성일',
		  // `bd_updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일',
		  // `bd_deleted_at` timestamp NULL DEFAULT NULL COMMENT '삭제일',
		  // `bd_ip` varchar(45) DEFAULT NULL COMMENT '아이피',

		for ($i=0; $i < 30; $i++) {
			$tmp = array();
			// $tmp['bd_bc_id'] = $faker->name;
			// $tmp['address'] = $faker->address;
			// $tmp['img'] = $faker->imageUrl(640,480,'animals');

			$tmp['post_board_id'] = 'notice';
			$tmp['post_mb_id'] = 2;
			$tmp['post_title'] = $faker->name;
			$tmp['post_content'] = $faker->realText($faker->numberBetween(10,20));
			$tmp['post_created_at'] = $faker->DateTime->format(DATETIME);

			kmh_print($tmp);
			$this->db->insert('post', $tmp);
		}

		// echo "nono";
	}

	public function tone_test() {
		$this->load->model('tab/chord_model');
		$this->load->model('tab/tab_model');
		$this->_render('tab/tone_test','AJAX');
		// $this->_render('tab/tone_test');
	}

	public function test($id=null) {
		$fastest = 10;
		$slowest = 500;
		$base = 100;
		$now = 490;

		echo abs($now - $slowest) / 100;
		// $this->load->model('file_model');
		// $file = $this->file_model->get($id);
		// $this->file_model->thumb_delete($file);
	}

	public function check_header() {
		kmh_print( apache_request_headers(), true );
		// $response['status'] = 'ok';
		// $response['header'] = apache_request_headers();
		// kmh_json($response);
	}
}