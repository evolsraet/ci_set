<? ob_start(); ?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js css-menubar lt-ie9 lt-ie8 lt-ie7" lang="ko"> <![endif]-->
<!--[if IE 7]>    <html class="no-js css-menubar lt-ie9 lt-ie8" lang="ko"> <![endif]-->
<!--[if IE 8]>    <html class="no-js css-menubar lt-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="ko"> <!--<![endif]-->

<head>
	<? include_once( VIEWPATH."/_template/_common/common_meta.php" ); ?>
	<? include_once( VIEWPATH."/_template/_common/remark_header_load.php" ); ?>
</head>
<body class="<?=$body_class?>">
	<div class="wrapper">

		<? if( $this->has_nav ) include_once (TPATH.'header.php'); ?>

		<main id="main" class="_animsition">
			<? if( $this->has_nav ) : ?>
				<h1 class="page_title"><?=page_title($nav_sub, $this->page_name)?></h1>
				<? if( $this->uri->segment(1) ) : ?>
					<div class="container">
						<ol class="breadcrumb">
							<li><a href="/">Home</a></li>
							<li class="active">
								<!-- ifelse -->
								<? if( count($nav_sub[$this->uri->segment(1)]) > 1 ) : ?>
									<a href="<?="/{$this->uri->segment(1)}/{$this->uri->segment(2)}"?>">
										<?=$nav_sub[$this->uri->segment(1)][$this->uri->segment(2)]?>
									</a>									
								<? else : ?>
									<a href="<?="/{$this->uri->segment(1)}/".array_first($nav_sub[$this->uri->segment(1)],'key')?>">
										<?=array_first($nav_sub[$this->uri->segment(1)])?>
									</a>									
								<? endif; ?>
								<!-- End Of ifelse -->
							</li>
						</ol>
					</div>
				<? endif; ?>
			<? endif; ?>
			<div class="container">
				<div id="content_wrap">
					<? include_once(MODULEPATH.'page_notice.php'); ?>
					<?=$content_body?>
				</div>
			</div>
			<div class="pjax_js">
				<!-- pjax js : <?=count($javascript)?> -->
				<? foreach ( (array)$javascript as $key => $row ) : ?>
					<? $this->assets->load_js($row); ?>
				<? endforeach; ?>
			</div>
		</main> <!-- main -->

		<? if( $this->has_nav ) include_once TPATH.'footer.php'; ?>
		<? if( $this->has_nav ) include_once( VIEWPATH."/_template/_common/common_element.php" ); ?>
	</div>
</body>
</html>
<? ob_end_flush(); ?>
