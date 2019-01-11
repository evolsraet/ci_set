<?
// 글로벌 헬퍼

	// 클래스의 프로텍트 개체 조회
	function get_protected_member(&$class_object,$protected_member) {
	     $array = (array)$class_object;      //Object typecast into (associative) array
	     $prefix = chr(0).'*'.chr(0);           //Prefix which is prefixed to protected member
	     // $prefix = ord('*');           //Prefix which is prefixed to protected member
	     // $prefix = '*';
	     return $array[$prefix.$protected_member];
	}

	/*==========================
	=            통신            =
	==========================*/

		// JSON 형식의 외부 API 호출
		function get_api_json($url, $data, $options = null) {
			if( $options === null ) {
				$options = array(
					'http' => array (
						'method' => 'POST',
						'content' => http_build_query($data)
					)
				);
			}

			$context = stream_context_create($options);
			$response = file_get_contents($url, false, $context);

			return json_decode($response);
		}

		function email_send($from_email, $from_name, $to, $subject, $body) {
			$CI =& get_instance();

			if( empty($from_email) )	$from_email = $CI->config->item('site_email');
			if( empty($from_name) )		$from_email = $CI->config->item('site_title');

			$data = array();
			$data['site_title'] = $CI->config->item('site_title');
			$data['body'] = $body;

			$CI->load->library('email');
			$CI->email->from($from_email, $from_name);
			$CI->email->to($to);
			$CI->email->subject($data['site_title'] . ' - ' . $subject);
			$CI->email->message(
				$CI->load->view('email_template', $data, true)
			);

			if( !$CI->email->send() )
				return strip_tags( $CI->email->print_debugger(false) );
			else
				return true;
		}

	/*=====  End of 통신  ======*/


	/*=================================
	=            BOOTSTRAP            =
	=================================*/

		function form_group($type, $title, $value='', $attr=false, $require=false) {
			$result = '';
			switch ($type) {
				case 'select':
					# code...
					break;

				default:
					// input
					$result .= "<div class=\"form-group\"> ";
					$result .= "	<label for=\"\"> ";
					$result .= "		".$title;
					$result .= "	</label> ";
					$result .= "	<input type=\"{$type}\" ";
					$result .= "		> "; // close input
					$result .= "</div> ";
					break;
			}
		}

	/*=====  End of BOOTSTRAP  ======*/



	/*==========================
	=            날짜            =
	==========================*/
		define('DATETIME', "Y-m-d H:i:s");
		define('DATE', "Y-m-d");

		// 날짜시간 스트링 자르기
		function get_datetime($str = null, $type = null) {
			if( $str===null ) $str = date(DATETIME);
			return substr($str, 0, 16);
		}
		// 날짜 스트링 자르기
		function get_date($str = null, $type = null) {
			if( $str===null ) $str = date(DATE);
			return substr($str, 0, 10);
		}
	/*=====  End of 날짜  ======*/


	/*===========================
	=            조건부            =
	===========================*/

		// ONE or ZERO
		// tinyint 디비 용
		function oz( $val ) {
			return boolval( $val ) ? 1 : 0;
		}

		function is_false( $val ) {
			if( $val === false )
				return true;
			else
				return false;
		}

		function is_true( $val ) {
			if( $val === true )
				return true;
			else
				return false;
		}

		function is_not_false( $val ) {
			if( $val !== false )
				return true;
			else
				return false;
		}

		function is_not_true( $val ) {
			if( $val !== true )
				return true;
			else
				return false;
		}

		function is_active($a, $b, $class="active") {
			if( $a==$b ) return $class;
		}

		// 현 도메인일 경우 이전 주소, 아닐 경우 루트
		function server_referer() {
			if( strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])!==false
				&& strpos($_SERVER['HTTP_REFERER'], 'member')===false
				) {
				return $_SERVER['HTTP_REFERER'];
			} else {
				return '/';
			}

		}

	/*=====  End of 조건부  ======*/

	/*===========================
	=            출력부            =
	===========================*/

		// $nav_sub 페이지명
		function page_title( $array ) {
			$CI =& get_instance();
			if( $CI->uri->total_rsegments() >= 2 ) {
				return $array[$CI->uri->rsegment(1)][$CI->uri->rsegment(2)];
			} elseif( $CI->uri->total_rsegments() >= 1 ) {
				return $array[$CI->uri->rsegment(1)];
			} else {
				return '페이지 타이틀이 없습니다.';
			}
		}

		// vue 템플릿 가져오기
		// 따옴표 등 문자열을 변환 처리
		// 배열 data 를 변수로 extract
		function vue_template( $file, $data = null ) {
			if(is_array($data)) extract($data);
			ob_start();
			include($file);
			$buffer = ob_get_contents();
			@ob_end_clean();
			// return addslashes($buffer);
			return trim(addslashes($buffer));
		}

		function vue_component( $file, $data = null ) {
			if(is_array($data)) extract($data);
			ob_start();
			include($file);
			$buffer = ob_get_contents();
			@ob_end_clean();
			// return addslashes($buffer);
			return $buffer;
		}

		/**
		 * Ratchet Websocket Library: helper file
		 * @author Romain GALLIEN <romaingallien.rg@gmail.com>
		 */
		if (!function_exists('valid_json')) {

		    /**
		     * Check JSON validity
		     * @method valid_json
		     * @author Romain GALLIEN <romaingallien.rg@gmail.com>
		     * @param  mixed  $var  Variable to check
		     * @return bool
		     */
		    function valid_json($var) {
		        return (is_string($var)) && (is_array(json_decode($var, true))) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
		    }
		}


		/**
		 * Ratchet Websocket Library: helper file
		 * @author Romain GALLIEN <romaingallien.rg@gmail.com>
		 */
		if (!function_exists('output')) {

		    /**
		     * Output valid or invalid logs
		     * @method output
		     * @author Romain GALLIEN <romaingallien.rg@gmail.com>
		     * @param  string  $type  Log type
		     * @param  string  $var   String
		     * @return string
		     */
		    function output($type = 'success', $output = null) {
		        if ($type == 'success') {
		            echo "\033[32m".$output."\033[0m".PHP_EOL;
		        } elseif($type == 'error') {
		            echo "\033[31m".$output."\033[0m".PHP_EOL;
		        } elseif($type == 'fatal') {
		            echo "\033[31m".$output."\033[0m".PHP_EOL;
		            exit(EXIT_ERROR);
		        } else {
		            echo $output.PHP_EOL;
		        }
		    }
		}


		if (! function_exists('dump')) {
			/**
			 * Output the given variables with formatting and location.
			 *
			 * Huge props out to Phil Sturgeon for this one
		     * (http://philsturgeon.co.uk/blog/2010/09/power-dump-php-applications).
		     *
			 * To use, pass in any number of variables as arguments.
			 *
			 * @return void
			 */
			function dump()
			{
				list($callee) = debug_backtrace();
				$arguments = func_get_args();
				$totalArguments = count($arguments);

				echo "<fieldset class='dump'>" . PHP_EOL .
					"<legend>{$callee['file']} @ line: {$callee['line']}</legend>" . PHP_EOL .
					'<pre>';

			    $i = 0;
			    foreach ($arguments as $argument) {
					echo '<br /><strong>Debug #' . (++$i) . " of {$totalArguments}</strong>: ";

		            if (! empty($argument)
		                && (is_array($argument) || is_object($argument))
		            ) {
						print_r($argument);
					} else {
						var_dump($argument);
					}
				}

				echo '</pre>' . PHP_EOL .
					'</fieldset>' . PHP_EOL;
			}
		}

		// 에코 대용 : XSS 필터링
		if (! function_exists('e')) {
			/**
			 *
			 * 에코 대용 : XSS 필터링
			 *
			 * A convenience function to ensure output is safe to display. Helps to
			 * defeat XSS attacks by running the text through htmlspecialchars().
			 *
			 * Should be used anywhere user-submitted text is displayed.
			 *
			 * @param String $str The text to process and output.
			 *
			 * @return void
			 */
			function e($str)
			{
				echo htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
			}
		}

		// 구글 리캡챠
		function reCAPTCHA( $sitekey ) {
			$result = '';
			$result .= PHP_EOL.'<!-- reCAPTCHA -->';
			$result .= PHP_EOL.'<script src="https://www.google.com/recaptcha/api.js"></script>';
			$result .= PHP_EOL.'<div class="captcha_wrapper">';
			$result .= PHP_EOL.'	<div class="g-recaptcha" data-sitekey="'.$sitekey.'"></div>';
			$result .= PHP_EOL.'</div>';
			$result .= PHP_EOL.'<!-- reCAPTCHA -->';
			return $result;
		}

		// 파일 패스 urlencode
		function urlencode_path( $path ) {
			return str_replace( '%2F', '/', urlencode($path) );
		}

	    // 쿼리스트링 수정
	    function querystring($key = '', $val = '', $query_string = '') {
	    	$CI =& get_instance();

	        $query_string = $query_string ? $query_string : $_SERVER['QUERY_STRING'];
	        parse_str($query_string, $qr);

	        // $CI->console->log( $query_string );
	        // $CI->console->log( $qr );

	        // remove from query string
	        if ($key) {
	            if ($val) {
	                $qr[$key] = $val;
	            } else {
	                unset($qr[$key]);
	            }
	        }
	        // return result
	        $return = '?';
	        if (count($qr) > 0) {
	        	// $qr = array_unique($qr);
	            $return .= http_build_query($qr);
	        	// $CI->console->log( $return );
	        }

	        return $return;
	    }

	    // foreach 가능한 배열로 전환
	    function fe( $obj ) {
	    	if( is_array($obj) || is_object($obj) )
	    		return $obj;
	    	else
	    		return array();
	    }

		// JS console.log
		function console_log( $data ){
		  echo '<script>';
		  echo 'console.log('. json_encode( $data ) .')';
		  echo '</script>';
		}

		// xmp 형태 덤프 출력
		function kmh_print($data) {
			echo "<xmp>";
			print_r($data);
			echo "</xmp>";
		}

		// json 문서
		function kmh_json($result, $die=false) {
			// JSON
			header('Content-type: application/json');
			echo json_encode($result);

			if( $die ) die();
		}

		function text_trim($str) {
			return trim( html_entity_decode( strip_tags($str) ) , " \t\n\r\0\x0B\xC2\xA0");
		}

		function text_cut($str, $length, $suffix="…", $encoding='UTF-8') {
			// $str = strip_tags( trim($str) );
			return mb_strimwidth($str, 0, $length+1, $suffix, $encoding);
			// $length 글자수가 1 일경우, 0으로 나옴. +1 해준다

			/*
			$str = strip_tags( trim($str) );

			$full_length = mb_strlen($str);
			if( $length >= $full_length ) return $str;

			if( 'UTF-8'===strtoupper($encoding) ) $length = $length * 3;

			$str = htmlspecialchars_decode($str);
			$str = mb_strcut($str, 0, $length, $encoding);
			$str = htmlspecialchars($str);
			return $str.$suffix;
			*/
		}

		function js_escape($str, $chr_set='UTF-8')
		{
			$arr_dec = unpack("n*", iconv($chr_set, "UTF-16BE", $str));
			$callback_function = create_function('$dec', 'if(in_array($dec, array(42, 43, 45, 46, 47, 64, 95))) return chr($dec); elseif($dec >= 127) return "%u".strtoupper(dechex($dec)); else return rawurlencode(chr($dec));');
			$arr_hexcode = array_map($callback_function, $arr_dec);
			return implode($arr_hexcode);
		}

		function js_unescape($str, $chr_set='UTF-8')
		{
			$callback_function = create_function('$matches, $chr_set="'.$chr_set.'"', 'return iconv("UTF-16BE", $chr_set, pack("n*", hexdec($matches[1])));');
			return rawurldecode(preg_replace_callback('/%u([[:alnum:]]{4})/', $callback_function, $str));
		}

		if (!function_exists('json_encode'))
		{
			function json_encode($a=false)
			{
				if (is_null($a)) return 'null';
				if ($a === false) return 'false';
				if ($a === true) return 'true';
				if (is_scalar($a))
				{
					if (is_float($a))
					{
						// Always use "." for floats.
						return floatval(str_replace(",", ".", strval($a)));
					}

					if (is_string($a))
					{
						static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
						return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
					}
					else
						return $a;
				}
				$isList = true;
				for ($i = 0, reset($a); $i < count($a); $i++, next($a))
				{
					if (key($a) !== $i)
					{
						$isList = false;
						break;
					}
				}
				$result = array();
				if ($isList)
				{
					foreach ($a as $v) $result[] = json_encode($v);
					return '[' . join(',', $result) . ']';
				}
				else
				{
					foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
					return '{' . join(',', $result) . '}';
				}
			}
		}


	/*=====  End of 출력부  ======*/


	/*===========================
	=            데이터            =
	===========================*/
		// CI Flashdata 추가 (배열)
		function add_flashdata( $name, $msg, $class=null ) {
			$CI =& get_instance();
			$flashdata = $CI->session->flashdata($name);
			if( $flashdata == '' )
				$flashdata = array();
			$flashdata[] = array( 'msg' => $msg, 'class' => $class );
			$CI->session->set_flashdata($name, $flashdata);
		}

		function get_ip()
		{
			if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
			{
				$ip=$_SERVER['HTTP_CLIENT_IP'];
			}
			elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
			{
				$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			else
			{
				$ip=$_SERVER['REMOTE_ADDR'];
			}
			return $ip;
		}

	/*=====  End of 데이터  ======*/



	/*==========================
	=            배열            =
	==========================*/

		// 배열 첫번째
		function array_first($array, $which='value') {
			foreach ($array as $key => $value) {
				if( $which=='value' ) return $value;
				else return $key;
			}
		}

		// DB 인서트/업데이트용 필터
		// 배열에서 prefix 확인된 것만 필터링
		// '_null' 값을 null로 대치
		function db_filter($array, $prefix, $data_ex = null ) {
			$data = array();
			// 데이터 만들기
			foreach ($array as $key => $value) {
				if( !in_array($key, (array)$data_ex) && strpos($key,$prefix)===0 ) { // 예외 데이터 확인
					if( $value === '_null' )	$value = null;
					$data[$key] = $value;
				}
			}
			return $data;
		}

	/*=====  End of 배열  ======*/



	/*==========================
	=            실행            =
	==========================*/

		// 경로내 폴더 만들
		//
		// latest_folder 는 마지막 확인된 폴더. 해당폴더까지는 검사하지않으므로, 효율상 좋음. 무시해도 무관
		//
		// $path = 'thumb/';
		// $latest_folder = $this->CI->config->item('file_path_php');

		function mkdir_path( $path, $latest_folder = null ) {
			// file_path_php 가 path 에 포함되 있을 경우, lastest_folder 를 file_path_php 로 변경
			if( $latest_folder == null ) {
				$CI =& get_instance();
				if( strpos($path, $CI->config->item('file_path_php')) !== false ) {
					$path = str_replace($CI->config->item('file_path_php'), '', $path);
					$latest_folder = $CI->config->item('file_path_php');
				}
			}

			// 경로 생성
			foreach ( explode('/',$path) as $key => $row) :
				if( trim($row)!='' ) :
					$current_folder = $latest_folder.'/'.$row;
					if (!is_dir($current_folder) )
						mkdir($current_folder, DIR_WRITE_MODE);

					chmod($current_folder, DIR_WRITE_MODE);
					$latest_folder = $current_folder;
				endif;
			endforeach;

			/*
			// FCPATH
			foreach ( explode('/',$path) as $key => $row) :
				if( !empty($latest_folder) ) $current_folder = $latest_folder.'/'.$row;
				else $current_folder = $row;

				// echo "[ {$current_folder} ]";

				if( trim($row)!='' && trim($row)!='.' ) :

					if (!is_dir($current_folder) ) {
						// echo "만들어야함 ";
						mkdir($current_folder, DIR_WRITE_MODE);
					} else {
						// echo "이미있음 ";
					}


					@chmod($current_folder, DIR_WRITE_MODE);
				endif;

				$latest_folder = $current_folder;
			endforeach;
			*/
		}


	/*=====  End of 실행  ======*/



	/*===========================
	=            자바스크립트            =
	===========================*/

		function alert($txt) {
			echo "<script>alert(\"{$txt}\");</script>";
		}

	    function go_back($txt = "정상적인 접근이 아니거나 존재하지않는 게시물입니다.") {
	        alert($txt);
	        echo "<script>history.go(-1);</script>";
	        exit;
	    }

	/*=====  End of 자바스크립트  ======*/

?>