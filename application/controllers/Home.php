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

use Faker\Factory;
// $parser = new Less_Parser();

class Home extends MY_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index($renderData=""){
		$this->load->model('test_model');
		$this->load->library('boards');

		// 렌더
		$this->_render('pages/home',$renderData);
	}

	public function info() {
		phpinfo();
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

	public function test($id) {
		$this->load->model('file_model');
		$file = $this->file_model->get($id);
		$this->file_model->thumb_delete($file);
	}

	public function check_header() {
		kmh_print( apache_request_headers(), true );
		// $response['status'] = 'ok';
		// $response['header'] = apache_request_headers();
		// kmh_json($response);
	}
}