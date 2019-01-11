<?
    // 보드카테고리 (카테고리 반환 기본)
    //
    // 기본값 배열로 반환
    // 두번째 인자 추가시 선택된 카테고리 명 반환
    function get_category(&$board_info, $selected = null, $total_text=null) {
        $tmp_array = array();
        // 카테고리 양식
        // 키=이름|키=이름

        if( $total_text!== null )
            $tmp_array['_null'] = $total_text;

        foreach (explode('|', $board_info->board_category) as $key => $row) {
            $tmp = explode('=',$row);
            if( trim($tmp[1])!='' )
                $tmp_array[ trim($tmp[0]) ] = trim($tmp[1]);
        }

        if( !count($tmp_array) ) return array();

        // $text_total

        if( $selected==null )
            return $tmp_array;
        else
            return $tmp_array[$selected];
    }

    // 보드카테고리 - ul로
    function get_category_ul(&$board_info, $selected=null, $add_class="nav nav-pills") {
        $active_init = $selected==''?"active":"";
        $get_category = get_category($board_info);
        if( !count($get_category) ) return false;

        $result = "";
        $result .= "<ul class=\"{$add_class}\">".PHP_EOL;
        $result .= "    <li class=\"{$active_init}\">".PHP_EOL;
        $result .= "        <a href=\"?\">전체</a>".PHP_EOL;
        $result .= "    </li>".PHP_EOL;

        foreach( $get_category as $key => $row ) :
            $active_key = $selected==$key?"active":"";

            $result .= "        <li class=\"{$active_key}\">".PHP_EOL;
            $result .= "            <a href=\"?category={$key}\">{$row}</a>".PHP_EOL;
            $result .= "        </li>".PHP_EOL;
        endforeach;
        $result .= "</ul>".PHP_EOL;

        return $result;
    }

    function is_notice(&$row) {
        return $row->post_is_notice ? true : false;
    }

    // 공지사항 또는 숫자 출력
    function notice_or_no(&$row, $no, $class='label label-warning', $text='N') {
        $result = '';
        if( $row->post_is_notice ) {
            $result = "<span class=\"{$class}\">";
            $result .= $text;
            $result .= "</span>";
        } else {
            if( $no !== null )
                $result = $no;
        }

        return $result;
    }

    // 목록 제목/링크 출력
    function view_link(&$row, $type_or_length=null, $board_base = '/') {
        $result = '';
        $CI =& get_instance();

        switch ( $type_or_length ) {
            case 'link':    // 링크
                $result = '';

                $result .= "data-board-link=\"true\" href=\"";

                // 비밀글 여부
                if ( $row->post_is_secret && !( $CI->members->is_me($row->post_mb_id) || $CI->members->is_admin() ) ) :
                    // 비밀글이면서 볼 권한이 없을 경우
                    // return "javascript:board_modal('check_password', '{$row->post_id}')";
                    $result .= "javascript:check_password({$row->post_id})";
                else :
                    $result .= "{$board_base}view/{$row->post_id}";
                endif;
                // 비밀글 여부

                $result .= "\" ";
                return $result;
                break;
            default:    // 타이틀
                // 뎁스
                if ( $row->post_depth ) :
                    for( $i=0; $i<$row->post_depth; $i++ )
                        // $result .= '<img src="'.IMG.'board/icon_reply.gif" alt="icon">';
                        // $result .= '<i class="fa fa-angle-double-right text-muted" style="width: 20px;"></i>';
                        $result .= '<i class="fa fa-reply fa-rotate-180 text-muted" style="margin-right: 10px;"></i>';

                    $result .= ' ';
                endif;
                // 뎁스

                // 비밀글 여부
                if ( $row->post_is_secret ) :
                    $result .= '<i class="fa fa-lock"></i> ';
                endif;
                // 비밀글 여부
                if( is_numeric( $type_or_length ) ) {
                    $row->post_title = text_cut($row->post_title, $type_or_length);
                }

                $result .= $row->post_title;
                return $result;
                break;
        }

                if( $row->post_is_secret ) {
                    $result = "<span class=\"{$class}\">";
                    $result .= $text;
                    $result .= "</span>";
                } else {
                    $result = $no;
                }
    }

    // 작성자
    function writer_display( &$row, $field = 'mb_display' ) {
        if( $row->mb_id ) :
            $result = '<a href="#">';
            $result .= '<strong>';
            $result .= $row->{ $field };
            $result .= '</strong>';
            $result .= '</a>';
            return $result;
        else :
            return $row->post_writer;
        endif;
    }

    // 게시판 관리자 권한이 있는지 (최고 관리자 또는 게시판 관리자)
    function is_board_admin() {
        $CI =& get_instance();

        // 최고관리자 여부
        if( $CI->members->is_admin() ) :
            return true;
        else :
            $admins = array();
            foreach( (array) explode(',', $CI->board_info->board_admin) as $key => $row ) :
                if(!empty($row))  $admins[] = $row;
            endforeach;
            if( in_array( $CI->logined->mb_id, $admins) ) return true;
        endif;

        return false;
    }

    // 검색 include
    function module_search( $search_array=null, $file = 'module_search' ) {
        $CI =& get_instance();
        include(VIEWPATH."board/{$file}.php");
    }

    function module_comment( $file = 'module_comment' ) {
        $CI =& get_instance();
        include(VIEWPATH."board/{$file}.php");
    }

    function board_btn($type, $text=null, $add_class=null, $link=null) {
        $CI =& get_instance();
        // codeigniter input-> 은 오브젝트를 반환할수 없다 (배열은 가능)

        // board / header 에 정해진 참조용 포스트 활용
        $board_btn_data = $_POST['board_btn_data'];
        $CI->console->log( array( $type, $board_btn_data) );

        // kmh_print( isset($link) );
        // $CI->console->log( $board_btn_data );

        $btn_need_password = false;

        switch ( $type ) {
            case 'list':
                $text = $text ? $text : "목록";
                $add_class = 'btn btn-default '.$add_class;

                $list_link = $_COOKIE['list_query'] ? urldecode($_COOKIE['list_query']) : $board_btn_data->board_base;

                $link = $link ? $link : $list_link;
                break;
            case 'write':
                $text = $text ? $text : "글쓰기";
                $add_class = 'btn btn-success '.$add_class;
                $link = $link ? $link : $board_btn_data->board_base."write";
                break;
            case 'update':
                if( $board_btn_data->auth->need_password )
                    $btn_need_password = true;

                $text = $text ? $text : "수정";
                $add_class = 'btn btn-success '.$add_class;
                $link = $link ? $link : $board_btn_data->board_base."update/".$board_btn_data->post_id;
                break;
            case 'reply':
                $text = $text ? $text : "답변";
                $add_class = 'btn btn-info '.$add_class;
                $link = $link ? $link : $board_btn_data->board_base."reply/".$board_btn_data->post_id;;
                break;
            case 'delete':
                if( $board_btn_data->auth->need_password )
                    $btn_need_password = true;

                if( $board_btn_data->auth->update )
                    $board_btn_data->auth->delete = true;

                $text = $text ? $text : "삭제";
                $add_class = 'btn btn-danger '.$add_class;
                $link = $link ? $link : "javascript:delete_post({$board_btn_data->post_id});";
                break;
            default:
                $text = $text ? $text : "";
                $add_class = 'btn btn-default '.$add_class;
                $link = $link ? $link : $board_btn_data->board_base."";
                break;
        }
        $btn = '';

        // 비밀번호 체크용
        if( $btn_need_password )
            $link = "javascript:check_password({$board_btn_data->post_id}, '{$type}');";

        // if( $board_btn_data->auth->{ $type } || $board_btn_data->auth->need_password ) :
        if( $board_btn_data->auth->{ $type } || $btn_need_password ) :
            $btn .= "<a ";

            $btn .= " class=\"{$add_class}\" ";
            $btn .= " href=\"{$link}\" ";

            $btn .= ">";
            $btn .= $text;
            $btn .= "</a>";

            return $btn;
        endif;
    }

