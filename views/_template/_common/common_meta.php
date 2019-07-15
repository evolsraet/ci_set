	<?
    	$nav_sub = $this->config->item('nav_sub');
    	$admin_nav_sub = $this->config->item('admin_nav_sub');

    	$add_title = '';

    	// 타이틀 추가
    	if( $view->pd_name )															// 상품뷰
        	$add_title = "{$view->pd_name} > {$view->cate_fullname}";
        else if( $view->post_title ) 													// 게시판
        	$add_title = "{$view->post_title} > {$this->board_info->board_name}";
        else if( $view->tab_id )														// 코드북
        	$add_title = "{$view->song_name} by {$view->artist_name} 코드 악보"
        					. ' > ' . $nav_sub[$this->uri->segment(1)]['index'];
    	else if( $nav_sub[$this->uri->segment(1)][$this->uri->segment(2)]!='' )			// 2뎁스
        	$add_title = "{$nav_sub[$this->uri->segment(1)][$this->uri->segment(2)]}";
       	else if( $nav_sub[$this->uri->segment(1)][$this->uri->segment(2)]=='' )			// 1뎁스만
        	$add_title = "{$nav_sub[$this->uri->segment(1)]['index']}";        	
        else if( $admin_nav_sub[$this->uri->segment(2)][$this->uri->segment(3)]!='' ) 	// 관리자
        	$add_title = "{$admin_nav_sub[$this->uri->segment(2)][$this->uri->segment(3)]}";

        // 기호 추가
        if( $add_title ) $add_title .= ' - ';

        $title = $add_title.$title;
    	$description = $add_title.$description;

        // BODY 태그 클래스
        $body_class = "page-container section_{$this->uri->segment(1)} page_{$this->uri->segment(2)}";
        $body_class .= ($this->render_type!='NO_NAV') ? "" : " no_nav";

        $domain = "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}";

        $add_keywords = array_values(
	    					array_filter(
					        	array(
					        		$view->post_title,
					        		$view->song_name,
					        		$view->artist_name
					        	)
					        )
				        );
       	$add_keyword_text = null;

        foreach( (array) $add_keywords as $key => $row ) :
        	if( $key ) :
        		$add_keyword_text .= ', ';
        	endif;

    		$add_keyword_text .= $row;
        endforeach;

        if( $add_keyword_text ) :
	    	$keywords = "{$add_keyword_text}, {$keywords}";
	    endif;
        // kmh_print($_SERVER);

        $canonical = $domain . $_SERVER['REDIRECT_URL'];

    	// codeigniter 기본 라이브러리 특성상 ?&page= 으로 연결됨 (not: ?page=)
        if( $this->input->get('page') )
        	$canonical .= "?&page=" . $this->input->get('page');
        
	?>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title><?=$title?></title>
	<link rel="canonical" href="<?=$canonical?>">
	<meta name="description" content="<?=$description?>">
	<meta name="keywords" content="<?=$keywords?><?=$view->post_title ? ",{$view->post_title}" : ""?>">
	<meta name="author" content="<?=$author?>">
	<meta name="robots" content="index,follow">	
	<!-- favicon 갱신 ?v= 방식으로 캐싱 취소 -->
  	<!-- <link rel="apple-touch-icon" href="/assets/images/apple-touch-icon.png"> -->
	<!-- <link rel="shortcut icon" href="data:;base64,iVBORwOKGO=" /> -->

	<link rel="apple-touch-icon" sizes="180x180" href="<?=$domain?>/assets/img/favicon/apple-touch-icon.png?v=rMqvL2jqE7">
	<link rel="icon" type="image/png" sizes="32x32" href="<?=$domain?>/assets/img/favicon/favicon-32x32.png?v=rMqvL2jqE7">
	<link rel="icon" type="image/png" sizes="16x16" href="<?=$domain?>/assets/img/favicon/favicon-16x16.png?v=rMqvL2jqE7">
	<link rel="manifest" href="<?=$domain?>/assets/img/favicon/site.webmanifest?v=rMqvL2jqE7">
	<link rel="mask-icon" href="<?=$domain?>/assets/img/favicon/safari-pinned-tab.svg?v=rMqvL2jqE7" color="#5bbad5">
	<link rel="shortcut icon" href="<?=$domain?>/assets/img/favicon/favicon.ico?v=rMqvL2jqE7">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="msapplication-config" content="<?=$domain?>/assets/img/favicon/browserconfig.xml?v=rMqvL2jqE7">
	<meta name="theme-color" content="#ffffff">

    <meta property="og:type" content="website">
    <meta property="og:title" content="<?=$title?>">
    <meta property="og:description" content="<?=$description?>">
    <meta property="og:image" content="<?=$domain?>/assets/img/favicon/apple-touch-icon.png">
    <meta property="og:url" content="<?=$domain?>">

	<meta name="csrf_token" id="csrf_token" content="<?=$this->security->get_csrf_hash()?>">
	<!-- PJAX 체크 -->
	<meta name="pjax_meta" id="<?=$this->pjax_meta?>" content="">
	<meta name="pjax_body_class"
		id="pjax_body_class"
		content="<?=$body_class?>"
		>
