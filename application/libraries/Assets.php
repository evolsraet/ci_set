<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 * PHP 리소스 번들러 JS/Style
 *
 * 강민호
 * v1
 *
 * 	1.	add_() 함수 후, bundle_() 함수로 그동안 add 한 파일들 번들링
 *   	생성일을 기준으로 새 버전이 없는 경우 캐시화 가능토록
 *    	LESS의 경우 CSS 변환 후 처리
 *    	(Minifying, Caching 은 이 파일에서 관여하지 않음)
 *
 * 	2.	load_() 함수로 개별 파일로딩 가능
 *
 *  3.	외부 도메인의 파일은 bundle 불가 /// load_()로 자동
 *
 *  4.	번들제작시 :: 해당 번들.확장자.log 에 한번이라도 불려진 CSS,JS는 파일에 기록되고 파일베이스로 모든 요소들 번들링
 *  	요소 제거시 bundle_reset
 *
 *	*	bundle_() 의 경우, 최종파일의 작성일을 기준으로 버전관리되므로, add_로 모은 요소가 빠진경우 다시 컴파일 필요
 *
 * 사용 라이브러리
 * + oyejorge/less.php
 * + CI_Helper (file)
 *
 */


// use Less\Parser;

class Assets {

	protected $js = array();
	protected $css = array();
	protected $bundle_file_path = FCPATH.'/assets/bundle/';
	protected $logs = '';

	protected $all_reset = false;

	public $use_bundling = true;

	// bundle_file_path
	// --- FCPATH.ASSETS.'bundle/'
	// ASSETS 는 my_controller 에서 생성되므로, 그 이전에 로드되는 __construct 까지에서는 사용할수 없다

    public function __construct() {
    	$this->CI =& get_instance();
    	$this->CI->load->helper('file', 'kmh');
    }

    public function __destruct() {

    	if( ENVIRONMENT=='development' )
    		if( trim( $this->logs )!='' ) console_log( $this->logs );
    }

    public function asset_log($txt) {
    	$this->logs .= $txt . PHP_EOL;
    }


    // JS 추가
    public function add_js($url) {
    	if( strpos($url, 'http')!==false || $this->use_bundling===false ) {
    		$this->load_js($url);
    	} else {
    		$this->js[] = $url;
    	}

    }

    // 스타일 추가
    public function add_css($url) {
    	if( strpos($url, 'http')!==false || $this->use_bundling===false ) {
    		$this->load_css($url);
    	} else {
    		$this->css[] = $url;
    	}
    }

    public function all_reset() {
    	$this->all_reset = true;
    	$this->bundle_reset();
    }

    public function bundle_reset() {
    	if (is_dir($this->bundle_file_path)) delete_files($this->bundle_file_path);
    }

    // 파일베이스 추가
    // 한번이라도 로드된 리소스는 파일에 기록하여 놓치지않도록 한다
    public function making_list( $type, $bundle_file_name ) {
		if( trim( $this->logs )=='' )
			$this->asset_log('======================== kmh_assets ========================'.PHP_EOL);

    	$file_name = $bundle_file_name.'.json';
    	$this->asset_log("+ 진입 - 목록재작성 [ {$file_name} ]");

    	switch ( $type ) {
    		case 'js':
    			break;
    		default:
    			break;
    	}

    	try {
    		// 파일 로드 (없다면 기본배열)
    			if( $file = read_file($this->bundle_file_path.$file_name) ) {
    				$file = json_decode($file, true);
    			} else {
    				$file['files'] = array();
    			}

    		// 리소스 합집합 - 필요한 모든 리소스 (파일 우선)
			$all_need = array_unique(array_merge($file['files'], $this->{ $type }));

    		if( $file['files'] == $all_need ) {
    			$this->{ $type } = $file['files'];
    			throw new Exception('+---- JSON 동일');
    		}

    		// 파일에 없다면 $this->{ $type } 추가
	    		foreach ($all_need as $key => $row) :
	    			if( !in_array($row, $file['files']) )	$file['files'][] = $row;
	    		endforeach;

    		// 파일 쓰기
				if (!is_dir($this->bundle_file_path)) @mkdir($this->bundle_file_path, DIR_WRITE_MODE);
				@chmod($this->bundle_file_path, DIR_WRITE_MODE);
				if ( !write_file($this->bundle_file_path.$file_name, json_encode($file)) ) :	// 파일 작성
					throw new Exception('+---- 번들로그 파일쓰기 에러');
				endif;

    		// $this->css를 파일배열로 교체
    			$this->{ $type } = $file['files'];

    		return true;
    	} catch (Exception $e) {
    		$this->asset_log( $e->getMessage() );
    		return false;
    	}
    }

	private function bundling($type, $bundle_file_name, $load_type='defer') {
    	$this->CI->benchmark->mark('bundle_full_time');

    	// 번들 작성 여부 변수
		$bundle_write = false;

    	$bundle_file_name = $bundle_file_name.".{$type}";
		$bundle_write = $this->making_list($type, $bundle_file_name);

		$this->asset_log('+---- 결과 - 목록재작성 : '.(bool)$bundle_write);

    	try {
			$this->asset_log('+ 번들링 시작 : '.$bundle_file_name);

    		if( !is_array($this->{ $type }) )		throw new Exception("add_{$type}()를 먼저 실행하세요.");

    		$bundle_version = $this->get_asset_version($this->bundle_file_path.$bundle_file_name);
    		$this->asset_log('+---- 파일 : '.$this->bundle_file_path.$bundle_file_name);
    		$this->asset_log('+---- 버전 : '.$bundle_version);

    		if ( !$bundle_write ) :	// bundle_write
	    		// 파일 루프
	    		foreach ($this->{ $type } as $key => $row) :
					$file_name = substr(strrchr($row,"/"),1);
					$folder_name = FCPATH.str_replace($file_name, '', $row);
					$this->asset_log('+-------- 리소스 검사 : '.$row);

					$version = $this->get_asset_version($folder_name.$file_name); 		// 최종 수정시간

					// $this->asset_log($row);
					if( $bundle_version < $version ) {
						$bundle_write = true;
						break;
					}
	    		endforeach;
	    	endif;

	    	// $this->asset_log('파일 검사 후 : '.$bundle_write);

    		if ( $bundle_write ) :	// bundle_write
				$this->asset_log( '+---- 재작성 : '.$bundle_file_name );
    			$this->CI->benchmark->mark('bundle_time');

    			$bundle_css_content = '';
	    		foreach ($this->{ $type } as $key => $row) :
	    			$this->asset_log( '+-------- '.$row );
					if( $type == 'css' ) $row = $this->convert2css( $row );
					$file_name = substr(strrchr($row,"/"),1);
					$folder_name = FCPATH.str_replace($file_name, '', $row);
					$web_folder_name = str_replace(FCPATH, '', $folder_name);

					// 상위 패스 찾기 (../ 리플레이스 용)
					$web_folder_seperate = array_filter( explode('/', $web_folder_name) );
					$web_folder_count = $web_folder_seperate;
					$web_folder_parent = false;
					if( $web_folder_count ) {
						$web_folder_parent = str_replace( array_pop($web_folder_seperate).'/'  , '', $web_folder_name);
					}

					// $this->CI->console->log($web_folder_seperate);
					// $this->CI->console->log($web_folder_count);

					$row_contents = file_get_contents( $folder_name.$file_name );

					// CSS 패스 수정 ( ./ -> path/ )
					if( $type == 'css' ) {
						if( $web_folder_parent ) {
							$row_contents = str_replace('../', $web_folder_parent, $row_contents);
						}
						$row_contents = str_replace('./', $web_folder_name, $row_contents);

						// 소스맵 지정 제거
						$row_contents = str_replace('sourceMappingURL', 'ORIGINAL_MAP_URL', $row_contents);

						// Remove comments also applicable in javascript
						$row_contents= preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $row_contents);

						// Remove space after colons
						$row_contents= str_replace(': ', ':', $row_contents);

						// Remove whitespace
						$row_contents= str_replace(array("\n", "\t", '  ', '    ', '    '), '', $row_contents);
					}

					$file_contents = PHP_EOL.'/* '.$web_folder_name.$file_name.' */'.PHP_EOL.$row_contents.PHP_EOL;

					$bundle_css_content .= $file_contents;
	    		endforeach;

				// 파일 작성
				if (!is_dir($this->bundle_file_path)) @mkdir($this->bundle_file_path, DIR_WRITE_MODE);
				@chmod($this->bundle_file_path, DIR_WRITE_MODE);
				if ( !write_file($this->bundle_file_path.$bundle_file_name, $bundle_css_content) ) :	// 파일 작성
					throw new Exception('+-------- [ 번들 파일쓰기 에러 ]');
				endif;	// 파일 작성

    			$this->CI->benchmark->mark('bundle_time_end');
				$this->asset_log(
					'+-------- '
					." 버전 : ".$version.PHP_EOL
					." 시간 ".$this->CI->benchmark->elapsed_time('bundle_time', 'bundle_time_end')
				);
    		endif;	// bundle_write

    		// 비움
			$this->{ $type } = array();

			switch ($type) {
				case 'css':
		    		// css 태그 리턴
					$return = '<link rel="stylesheet" href="';
					$return .= ASSETS.'bundle/'.$bundle_file_name.'?v='.$bundle_version;
					$return .= '">';
					break;

				default:
		    		// js 태그 리턴
					$return = '<script '.$load_type.' src="';
					$return .= ASSETS.'bundle/'.$bundle_file_name.'?v='.$bundle_version;
					$return .= '" ></script>';
					break;
			}

			echo $return;

    	} catch (Exception $e) {
    		$this->asset_log( $e->getMessage() );
    	}

    	$this->CI->benchmark->mark('bundle_full_time_end');
    	$this->asset_log( '+---- 총 소요시간 : '.$this->CI->benchmark->elapsed_time('bundle_full_time', 'bundle_full_time_end').PHP_EOL );
	}


    // JS 번들링
    public function bundle_js($bundle_file_name='bundle', $load_type='defer') {
    	$this->bundling('js', $bundle_file_name, $load_type);
    }

    // 스타일 번들링
    public function bundle_css($bundle_file_name='bundle') {
		$this->bundling('css', $bundle_file_name, $load_type='');
    }

	// 개별 파일 JS 로드
	public function load_js( $url, $option='defer' ) {
		$file_name = substr(strrchr($url,"/"),1);
		$folder_name = FCPATH.str_replace($file_name, '', $url);

		$external = ( strpos($url, 'http')===false ) ? false : true;

		if( !$external && !file_exists($folder_name.$file_name) )
			return false;

		$version = $this->get_asset_version($folder_name.$file_name); 		// 최종 수정시간

		$return = '<script '.$option.' src="';
		$return .= $url;
		if( !$external ) $return .= '?v='.$version;
		$return .= '" ';
		// $return .= $pjax ? 'class="pjax_element"' : '';
		$return .= '></script>';
		echo $return;
	}

	// 개별 스타일 로드
	public function load_css( $url, $pjax=false ) {
		$url = $this->convert2css($url);

		$file_name = substr(strrchr($url,"/"),1);
		$folder_name = FCPATH.str_replace($file_name, '', $url);

		$version = $this->get_asset_version($folder_name.$file_name); 		// 최종 수정시간

		$return = '<link rel="stylesheet" href="';
		// $return .= $url;
		$return .= $url.'?v='.$version;
		$return .= '" ';
		// $return .= $pjax ? 'class="pjax_element"' : '';
		$return .= '>';

		echo $return;
	}

	private function convert2css( $url ) {
		$ext = substr(strrchr($url,"."),1);	// 확장자앞 .을 제거하기 위하여 substr()함수를 이용
		$ext = strtolower($ext);			// 확장자를 소문자로 변환

		switch ( $ext ) {
			case 'less':
				$file_name = substr(strrchr($url,"/"),1);
				$folder_name_web = str_replace($file_name, '', $url);
				$folder_name = FCPATH.$folder_name_web;

				if( !$this->all_reset )
					$version = $this->get_asset_version($folder_name.$file_name); 		// 최종 수정시간

				$file_name_as_css = str_replace($ext, 'css', $file_name);
				if( !$this->all_reset )
					$css_version = $this->get_asset_version($folder_name.$file_name_as_css);

				@chmod($folder_name, DIR_WRITE_MODE);

				// $this->asset_log( $file_name_as_css );
				// $this->asset_log( "LESS 파일 버전 ".$version );
				// $this->asset_log( "CSS 파일 버전 ".$css_version );
				// die();

				// LESS와 CSS 수정일시 비교 후 변환
				if( $version > $css_version || $this->all_reset ) {
					$this->CI->benchmark->mark('less_convert');

					// LESS 변환
					$options = array();
					$options['compress'] = true;

					if( ENVIRONMENT == 'development' ) :
						// $options['sourceMap']         = false;  // output .map file?
						$options['sourceMap']         = true;  // output .map file?
						$options['sourceMapWriteTo']  = FCPATH . $folder_name_web . $file_name . ".map";
						$options['sourceMapURL']      = $folder_name_web . $file_name . ".map";
						$options['sourceMapBasepath'] = FCPATH;
						$options['sourceMapRootpath'] = '/'; // sourceMapBasepath -> sourceMapRootpath 로 교체함
					endif;

					$parser = new Less_Parser($options);
					$file = $folder_name.$file_name;
					$parser->parseFile( $file, base_url() );
					$css = $parser->getCss();

					// 파일 작성
					@chmod($folder_name, DIR_WRITE_MODE);

					if ( !write_file($folder_name.$file_name_as_css, $css) ) :	// 파일 작성
						echo 'CSS 파일 작성 에러.';
					else :	// 파일 작성
						touch($folder_name.$file_name, $version);
					endif;	// 파일 작성

					$this->CI->benchmark->mark('less_convert_end');
					$this->asset_log(
						'+------------ CSS 변환 : '.$file_name
						." -v ".$version
						." :: 시간 ".$this->CI->benchmark->elapsed_time('less_convert', 'less_convert_end')
					);
				}

				// CSS로 로드 파일명 변경
				$url = str_replace(".{$ext}", '.css', $url);

				break;
		}

		return $url;
	}

	private function get_asset_version( $php_url ) {
		if( file_exists($php_url) ) {
			return filemtime($php_url);
		} else {
			return 'no_'.time();
		}
	}
}