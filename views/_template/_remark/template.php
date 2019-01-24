<? ob_start(); ?>
<!DOCTYPE html>
<html class="no-js css-menubar" lang="ko">
<!--[if lt IE 7]> <html class="no-js css-menubar lt-ie9 lt-ie8 lt-ie7" lang="ko"> <![endif]-->
<!--[if IE 7]>    <html class="no-js css-menubar lt-ie9 lt-ie8" lang="ko"> <![endif]-->
<!--[if IE 8]>    <html class="no-js css-menubar lt-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->

<head>
	<? include_once( VIEWPATH."/_template/_common/common_meta.php" ); ?>

	<? // $this->assets->all_reset(); // 에셋 완전 리셋 - 오래걸림 ?>
	<? // $this->assets->bundle_reset(); // 번들만 리셋 ?>
	<? include_once( VIEWPATH."/_template/_common/remark_header_load.php" ); ?>
</head>
<body class="<?=$body_class?>">
	<div class="wrapper">

		<? if( $this->has_nav ) include_once (TPATH.'header.php'); ?>

		<main id="main" class="animsition">
			<? if( $this->has_nav ) : ?>
				<h1 class="page_title"><?=page_title($nav_sub, $this->page_name)?></h1>
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
