	<?
    	$nav_sub = $this->config->item('nav_sub');
    	$admin_nav_sub = $this->config->item('admin_nav_sub');
    	if( $nav_sub[$this->uri->segment(1)][$this->uri->segment(2)]!='' )
        	$title = "{$nav_sub[$this->uri->segment(1)][$this->uri->segment(2)]} - {$title}";
        if( $admin_nav_sub[$this->uri->segment(2)][$this->uri->segment(3)]!='' )
        	$title = "{$admin_nav_sub[$this->uri->segment(2)][$this->uri->segment(3)]} - {$title}";

        // BODY 태그 클래스
        $body_class = "page-container section_{$this->uri->segment(1)} page_{$this->uri->segment(2)}";
        $body_class .= ($this->render_type!='NO_NAV') ? "" : " no_nav";
	?>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>
		<?=$view->post_title ? "{$view->post_title} - " : ""?>
		<?=$view->pd_title ? "{$view->pd_title} - " : ""?>
		<?=$view->pd_title ? "{$view->pd_title} - " : ""?>
		<?=$title?>
	</title>

	<!-- favicon 갱신 ?v= 방식으로 캐싱 취소 -->
  	<!-- <link rel="apple-touch-icon" href="/assets/images/apple-touch-icon.png"> -->
	<link rel="shortcut icon" href="data:;base64,iVBORwOKGO=" />

	<meta name="description" content="<?=$description?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="keywords" content="<?=$keywords?><?=$view->post_title ? " , {$view->post_title}" : ""?>">
	<meta name="author" content="<?=$author?>">
	<meta name="csrf_token" id="csrf_token" content="<?=$this->security->get_csrf_hash()?>">
	<!-- PJAX 체크 -->
	<meta name="pjax_meta" id="<?=$this->pjax_meta?>" content="">
	<meta name="pjax_body_class"
		id="pjax_body_class"
		content="<?=$body_class?>"
		>
