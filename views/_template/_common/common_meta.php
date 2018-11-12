	<?
    	$nav_sub = $this->config->item('nav_sub');
    	if( $nav_sub[$this->uri->segment(1)][$this->uri->segment(2)]!='' )
        	$title = "{$nav_sub[$this->uri->segment(1)][$this->uri->segment(2)]} - {$title}";
	?>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>

		<?=$view->post_title ? "{$view->post_title} - " : ""?>
		<?=$title?>
	</title>
	<meta name="description" content="<?=$description?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="keywords" content="<?=$keywords?><?=$view->post_title ? " , {$view->post_title}" : ""?>">
	<meta name="author" content="<?=$author?>">
	<meta name="csrf_token" id="csrf_token" content="<?=$this->security->get_csrf_hash()?>">
	<!-- PJAX 체크 -->
	<meta name="pjax_meta" id="<?=$this->pjax_meta?>" content="">
