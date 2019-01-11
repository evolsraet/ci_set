<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//
// 파일 업로드 공통
//
// - 업로드 후 file db 에 등록
//

use Carbon\Carbon;


class Files {

	public $folder = false;
	public $file = false;
	public $allowed_types = '*';
	public $max_size = '*'; // KB
	public $duplicate_replace = false; // 중복 비허용 : file_rel_type, file_rel_id, file_rel_desc 같은 지난 파일 삭제

	public $tmpcode_prefix = "tmpcode_";

	public $last_file = false;	// 최종 접근한 파일 (설정 안되어있음)
	public $last_image = false;	// 최종 접근한 이미지 파일 (post_image 에서 사용)

	public function __construct( $config = array() ) {
		$this->CI =& get_instance();
		// $this->CI->load->helper('file', 'kmh');
		$this->CI->load->model('file_model');

		empty($config) OR $this->initialize($config, FALSE);
	}

	// 설정 초기화
	//
	// 수동 - 혹은 업로드 후 초기화 됨
	public function initialize(array $config = array(), $reset = TRUE) {


		// reset :: 클래스 초기 값으로 변환
		if( $reset === TRUE ) :
			$reflection = new ReflectionClass($this);
			foreach ($reflection->getDefaultProperties() as $key => $row) :
				$this->{$key} = $row;
			endforeach;
		endif;
		// End of reset

		// 컨피그파일로 초기화
		foreach( (array) $config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}

		return $this;
	}

	/*----------  타입설정  ----------*/

		public function set_type_image() {
			$this->allowed_types = 'jpg|jpeg|png|gif';
			return $this;
		}

		public function set_type($type='*') {
			$this->allowed_types = $type;
			return $this;
		}

		public function set_duplicate_replace( $val = true ) {
			$this->duplicate_replace = $val;
			return $this;
		}

		public function get_type() {
			return $this->allowed_types;
		}

	/*----------  기타 설정  ----------*/

		public function set_max_size( $size='*' ) {
			$this->max_size = $size;
		}


	/*----------  유틸리티  ----------*/

		// 파일 임시 저장용 코드 생성
		public function make_tmp_code( $name='' ) {
			$tmp_code =
				$this->tmpcode_prefix
				.$name."_"
				.$_SERVER['REMOTE_ADDR'].date('His');

			return $tmp_code;
		}

		public function no_image($width=0, $height=0) {
			if( $width  == 0 )	$width = 500;
			if( $height == 0 )	$height = 500;
			$url = "http://via.placeholder.com/{$width}x{$height}/f1f4f5/76838f?text=NO%20IMAGE";
			return $url;
		}

		// 이미지 리사이즈
		// file_url 은 웹 기반 (NO PHP BASE URL)
		// is_thumb이 false 이면 같은 파일을 계속 리사이징 하므로, 최초 업로드에만 사용할것
		//
		// // 패키지스트 : gumlet/php-image-resize 도 괜찮을듯
		public function image_resize($image_path, $width = 0, $height = 0, $is_thumb = true) {
			ini_set('memory_limit','1600M');
			ini_set('max_execution_time', 3000);

			// 이미지 리사이즈
			try {

				// 경로 설정
					$image_path = str_replace('http://'.$_SERVER['HTTP_HOST'], "", $image_path);
					$image_path = str_replace('http://www.'.$_SERVER['HTTP_HOST'], "", $image_path);
					$image_path = str_replace(
										$this->CI->config->item('file_path'),
										$this->CI->config->item('file_path_php'),
										$image_path
									);

				// 사이즈 없으면 원본대로
					if( $width==0 && $height==0 )
						throw new Exception("리사이징 할 필요가 없습니다.(사이즈 미설정)", 1);	// 경로 그대로 출력

				// 파일 확인 - 정보 구하기
					if ( !is_file( $image_path ) or $image_path == '/' or $image_path == '.' )
						throw new Exception("파일 경로가 잘못됐습니다.", 2);

					$image_info = @getimagesize($image_path);
					$file_info = pathinfo($image_path);


				// 이미지인지 체크
					if( !is_array( $image_info ) )
						throw new Exception("이미지 파일이 아닙니다.", 2);


				// 새 경로 지정

					if( $is_thumb ) {

						// $thumb_folder = 'thumb/'.date('Ym').'/';
						$thumb_folder = 'thumb/';
						$file_path_php = $this->CI->config->item('file_path_php');

						// 폴더 생성
						mkdir_path( $file_path_php . $thumb_folder );

						// $new_image_path =
						// 		$file_info['dirname']
						// 		. '/thumb_' . $file_info['basename']
						// 		. '_' . $width
						// 		. 'x'
						// 		. $height
						// 		. '.'
						// 		. $file_info['extension'];



						$new_image_path =
								$file_path_php . $thumb_folder
								. 'thumb_' . $file_info['basename']
								. '_' . $width
								. 'x'
								. $height
								. '.'
								. $file_info['extension'];
					} else {
						$new_image_path = $image_path;
					}

				// 썸네일이 존재하거나, 이미지가 캐시이미지(뉴_이미지_패스) 보다 오래됐다면 - 에러
					if (		( $is_thumb && file_exists($new_image_path) )
								|| filemtime($image_path) < @filemtime($new_image_path)
					 	) {
						throw new Exception("이미 리사이징 파일이 존재합니다.", 3);
					}

				// 사이즈 및 비율
					$original_width = $image_info[0];
					$original_height = $image_info[1];
					$ratio = $original_width / $original_height;

				// 리사이징 할 사이즈
					$requested_width = $width;
					$requested_height = $height;

				// 초기화
					$new_width = 0;
					$new_height = 0;

				// * 계산
					// 가로세로 비율에 맞춰 리사이징 할 사이즈를 구한 뒤,
					// 반대변 요청 사이즈가 계산된 사이즈보다 크면, 해당변의 사이즈를 비율에 맞게 재조정
					if ($requested_width > $requested_height) {	// 가로이미지 혹은 가로사이즈만 존재
						$new_width = $requested_width;
						$new_height = $new_width / $ratio;
						if ($requested_height == 0)
							$requested_height = $new_height;

						if ($new_height < $requested_height) {
							$new_height = $requested_height;
							$new_width = $new_height * $ratio;
						}

					}
					else {	// 세로이미지 혹은 세로사이즈만 존재
						$new_height = $requested_height;
						$new_width = $new_height * $ratio;
						if ($requested_width == 0)
							$requested_width = $new_width;

						if ($new_width < $requested_width) {
							$new_width = $requested_width;
							$new_height = $new_width / $ratio;
						}
					}

					$new_width = ceil($new_width);
					$new_height = ceil($new_height);

				// 가로세로 모두있으면 크롭 / 아니면 리사이징
					$this->CI->load->library('image_lib');

					// 리사이징
					$config = array();
					$config['image_library'] = 'gd2';
					$config['source_image'] = $image_path;
					$config['new_image'] = $new_image_path;
					$config['maintain_ratio'] = FALSE;
					$config['height'] = $new_height;
					$config['width'] = $new_width;
					$this->CI->image_lib->initialize($config);
					$this->CI->image_lib->resize();
					$this->CI->image_lib->clear();

					if ($width != 0 && $height != 0) {
						$x_axis = floor(($new_width - $width) / 2);
						$y_axis = floor(($new_height - $height) / 2);

						// 크롭
						$config = array();
						$config['image_library'] = 'gd2';
						$config['source_image'] = $new_image_path;
						$config['new_image'] = $new_image_path;
						$config['maintain_ratio'] = FALSE;
						$config['width'] = $width;
						$config['height'] = $height;
						$config['x_axis'] = $x_axis;
						$config['y_axis'] = $y_axis;
						$this->CI->image_lib->initialize($config);
						$this->CI->image_lib->crop();
						$this->CI->image_lib->clear();
					}

				// 새 파일명 리턴

					$web_path = str_replace(
										$this->CI->config->item('file_path_php'),
										$this->CI->config->item('file_path'),
										$new_image_path
									);

					return urlencode_path( $web_path );


			} catch (Exception $e) {
				$this->CI->kmh->log( $e->getMessage(), $image_path );

				switch ($e->getCode()) {
					case 2:
						// 파일 미존재 - 가짜 썸네일
						return $this->no_image( $width, $height );
						break;
					case 3:
						// 리사이징 존재 : 뉴 이미지 패스
						$image_path = $new_image_path;
						break;
					default:
						break;
				}

				$web_path = str_replace(
									$this->CI->config->item('file_path_php'),
									$this->CI->config->item('file_path'),
									$image_path
								);

				return urlencode_path( $web_path );
			}
			// End of 이미지 리사이즈
		}

		// 게시글 대표이미지
		public function post_image($post_id, $width = 0, $height = 0, $no_resize = false) {
			$this->CI->load->model('file_model');

			$result = $this->CI->file_model->post_image($post_id);

			if( $result->web_path ) {
				$this->last_image = $result->web_path;

				if( $no_resize === true )
					return $result->web_path;
				else
					return $this->image_resize( $result->web_path, $width, $height );

			} else {
				return $this->last_image = $this->no_image($width, $height);
			}
		}

		// 회원 프로필사진
		public function member_image( $mb_id, $size=50 ) {
			$this->CI->load->model('file_model');
			$image_file = $this->CI->file_model->member_image($mb_id);

			// 이미지가 있다면
			if( $image_file->file_save ) :
				return
					'<img src="'
					.$this->image_resize( $image_file->web_path, $size, $size ).'"'
					." style=\"max-width: {$size}px; width: {$size}px; height: {$size}px\""
					.' class="member_image img-circle" alt="member photo">';
			else :
				$this->CI->load->helper('kmh');
				$this->CI->load->model('member_model');
				$db = $this->CI->member_model->get($mb_id);

				// 소셜 이미지 있을 경우
				if( $db->mb_social_image )
					return
						'<img src="'
						.$db->mb_social_image.'"'
						." style=\"max-width: {$size}px; width: {$size}px; height: {$size}px\""
						.' class="member_image img-circle" alt="member photo" width="'.$size.'">';

				// 없을경우, 텍스트
				switch ($db->mb_level) {
					case '100':
						$bg_class = " bg-primary";
						break;
					// case '1':
					// 	$bg_class = " bg-success";
					// 	break;
					default:
						$bg_class = " bg-gray";
						break;
				}

				// $result = '<div class="member_image_wrap size_'.$size.$bg_class.' ">';
				$font_size = $size / 2.5;
				$result = '<div class="member_image_wrap '.$bg_class.'" ';
				$result .= "	style=\"width: {$size}px; height: {$size}px; line-height: {$size}px; font-size: {$font_size}px;  \" ";
				$result .= '>';
				$result .= '<span>';
				$result .= text_cut($db->mb_display, 1, '');
				$result .= "</span>";
				$result .= "</div>";
			endif;


			return $result;
		}

		// file 디비 형태를 받아 타입 리턴
		public function get_file_type( &$file_data, $type='fa4' ) {
			$ext = explode('.', $file_data->file_save);
			$ext = array_pop($ext);

			$data['fa4']['file']       = 'fa fa-fw fa-file-o';
			$data['fa4']['archive']    = 'fa fa-fw fa-file-archive-o';
			$data['fa4']['audio']      = 'fa fa-fw fa-file-audio-o';
			$data['fa4']['code']       = 'fa fa-fw fa-file-code-o';
			$data['fa4']['excel']      = 'fa fa-fw fa-file-excel-o';
			$data['fa4']['image']      = 'fa fa-fw fa-file-image-o';
			$data['fa4']['pdf']        = 'fa fa-fw fa-file-pdf-o';
			$data['fa4']['powerpoint'] = 'fa fa-fw fa-file-powerpoint-o';
			$data['fa4']['text']       = 'fa fa-fw fa-file-text-o';
			$data['fa4']['video']      = 'fa fa-fw fa-file-video-o';
			$data['fa4']['word']       = 'fa fa-fw fa-file-word-o';

			$ext_type = "zip|alz|gz|tar|z|rar|ace|bz|bz2";
			if( preg_match("/($ext_type)/i",$ext) )
				return $data[$type]['archive'];

			$ext_type = "php|js|sql|css|less|scss|sass";
			if( preg_match("/($ext_type)/i",$ext) )
				return $data[$type]['code'];

			if( strpos($file_data->file_type, 'image') !== false )
				return $data[$type]['image'];

			if( strpos($file_data->file_type, 'audio') !== false )
				return $data[$type]['audio'];

			if( strpos($file_data->file_type, 'video') !== false )
				return $data[$type]['video'];

			if( strpos($ext, 'pdf') !== false )
				return $data[$type]['pdf'];

			if( strpos($ext, 'xls') !== false || strpos($ext, 'csv') !== false )
				return $data[$type]['excel'];

			if( strpos($ext, 'ppt') !== false )
				return $data[$type]['powerpoint'];

			if( strpos($ext, 'doc') !== false )
				return $data[$type]['word'];

			if( strpos($file_data->file_type, 'text') !== false )
				return $data[$type]['text'];

			return $data[$type]['file'];
		}

	/*----------  run  ----------*/

		// 업로드
		public function upload( $file_post_name, $folder, $rel_type = null, $rel_id=null, $rel_desc=null ) {
			$this->CI->kmh->log( func_get_args(), 'upload 변수' );
			$this->CI->kmh->log( $_FILES, 'upload _FILES' );

			$total_result = array();
			$total_result['count'] = 0;
			$total_result['count_fail'] = 0;
			$file_path_php = $this->CI->config->item('file_path_php');

			// 업로드 전에 임시파일들 삭제
			$this->delete_old_tmp_files();

			if( substr($folder, -1)!='/' )
				$folder .= '/';

			try {
				$config['max_size']      = $this->max_size;
				$config['upload_path']   = $file_path_php . $folder;
				$config['allowed_types'] = $this->allowed_types;
				$config['encrypt_name']  = true;

				// 파일이 없다면 예외
				if( !is_array($_FILES[$file_post_name]) ) :
					throw new Exception("업로드할 파일이 없습니다.", EXIT_SUCCESS);
				endif;

				// 폴더 생성
				mkdir_path( $config['upload_path'] );

				$this->CI->load->library('upload', $config);

				// 파일이 배열아니면 배열로 만들기
				if( !is_array($_FILES[$file_post_name]['name']) ) :
					$_FILES['tmp'] = $_FILES[$file_post_name];
					$vars = array( 'name','type','tmp_name','error','size' );
					foreach( (array) $vars as $key => $row ) :
						$_FILES[$file_post_name][$row] = array();
						$_FILES[$file_post_name][$row][]     = $_FILES['tmp'][$row];
					endforeach;
				endif;
				// End of 파일이 배열이면

				foreach ($_FILES[$file_post_name]['name'] as $key => $row) :
					$_FILES['userfile']['name'] = $_FILES[$file_post_name]['name'][$key];
					$_FILES['userfile']['type'] = $_FILES[$file_post_name]['type'][$key];
					$_FILES['userfile']['tmp_name'] = $_FILES[$file_post_name]['tmp_name'][$key];
					$_FILES['userfile']['error'] = $_FILES[$file_post_name]['error'][$key];
					$_FILES['userfile']['size'] = $_FILES[$file_post_name]['size'][$key];

					if ($_FILES['userfile']['size']) :
						$total_result['count'] ++;
						$this->CI->upload->initialize($config);
						// 실패
						if ( !$this->CI->upload->do_upload('userfile') ) :
							$total_result['count_fail'] ++;
							$total_result['data'][] = array('error' => $this->CI->upload->display_errors('',''));
							$total_result['error'] .=
								PHP_EOL."[ {$_FILES['userfile']['name']} ]"
								.$this->CI->upload->display_errors('','');
							// $this->CI->kmh->log( $file_result );
						// 성공
						else :
							$file_result = array('upload_data' => $this->CI->upload->data());
							$file_result['web_path'] =
								$this->CI->config->item('file_path').$folder.$file_result['upload_data']['file_name'];

							// $this->CI->kmh->log( $file_result );

							// 중복 비허용	(기존 파일 삭제)
							if( $this->duplicate_replace ) :
								$duplicate_replace_result = $this->CI->file_model
									->where('file_rel_type', $rel_type)
									->where('file_rel_id', $rel_id)
									->where('file_rel_desc', $rel_desc)
									->delete();

								// $duplicate_replace_result = "{$rel_type} - {$rel_id} - {$rel_desc} : result ({$duplicate_replace_result})";
								// $this->CI->kmh->log( $duplicate_replace_result, 'duplicate_replace result' );
							endif;

							// 파일 디비에 추가
							if( $rel_type !== null || $rel_id !== null ) {
								$file_data['file_rel_type'] = $rel_type;
								$file_data['file_rel_id']   = $rel_id;
								$file_data['file_rel_desc'] = $rel_desc;

								$file_data['file_folder'] = $folder;
								$file_data['file_save'] = $file_result['upload_data']['file_name'];
								$file_data['file_name'] = $file_result['upload_data']['client_name'];
								$file_data['file_type'] = $file_result['upload_data']['file_type'];
								$file_data['file_size'] = $_FILES['userfile']['size'];
								$file_data['file_is_image'] = $file_result['upload_data']['is_image'];

								$insert_id = $this->CI->file_model->insert($file_data);

								$file_result['file_id'] = $insert_id;
								$total_result['data'][] = $file_result;
							}
						endif;
					endif;
				endforeach;

				// 결과 생성
				if( !$total_result['count_fail'] ) :
					// 모두 성공
					$total_result['status'] = 'ok';
					$total_result['msg'] = '정상적으로 업로드 되었습니다.';
				elseif( $total_result['count_fail'] < $total_result['count'] ) :
					// 일부 실패
					$total_result['status'] = 'ok_but_some_fail';
					$total_result['msg'] = '일부 파일을 업로드하지 못했습니다.'.$total_result['error'];
				else :
					// 모두 실패
					$total_result['status'] = 'fail';
					$total_result['msg'] = '파일을 업로드하지 못했습니다.'.$total_result['error'];
				endif;

			} catch (Exception $e) {
				$total_result['msg'] = $e->getMessage();

				if( $e->getCode() === EXIT_SUCCESS )
					$total_result['status'] = 'ok';
				else
					$total_result['status'] = 'fail';

			} // END TRY

			$this->CI->kmh->log( $total_result, '업로드 결과 : '.$_SERVER['REQUEST_URI'] );

			// 초기화
			$this->initialize(array(), true);
			return $total_result;
		}

		// 기간 지난 임시파일 삭제
		public function delete_old_tmp_files() {
			// $this->CI->output->enable_profiler(TRUE);

			$expire_days = 1; // days
			$expire_date = Carbon::now()->subDays( $expire_days )->toDateString(); // 만료일
			// $expire_date = Carbon::now()->toDateTimeString(); // 만료일 (지금 이전 모두)

			// 만료일 이전 작성 글 조회
			$result =
				$this->CI->file_model
					->like('file_rel_id', $this->tmpcode_prefix)
					->where('file_created_at <', $expire_date)
					->get_all();

			foreach( fe( $result ) as $key => $row ) :
				$this->CI->kmh->log( $row, '올드 파일 삭제' );
				$this->CI->file_model->delete( $row->file_id );
			endforeach;

		}
}
