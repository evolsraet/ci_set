<? ob_start(); ?>
<!DOCTYPE html>
<html lang="ko">
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="ko"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="ko"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->

<head>
	<? include_once( VIEWPATH."/_template/_common/common_meta.php" ); ?>

	<? // $this->assets->bundle_reset(); // 번들 리셋 필요시 ?>

	<!-- 헤드 CSS -->
		<!-- Bootstrap _ Common Style -->
		<? $this->assets->add_css( LIB."noto-sans-korean/css/noto-sans-korean.css" ); ?>
		<? $this->assets->add_css( LIB."bootstrap-3.3.7/less/bootstrap.less" ); ?>
		<? $this->assets->add_css( LIB."bootstrap-3.3.7/less/theme.less" ); ?>

		<? $this->assets->add_css( LIB."font-awesome-4.7.0/less/font-awesome.less" ); ?>
		<? $this->assets->add_css( LIB."pace-1.0.2/themes/white/pace-theme-flash.css" ); ?>
		<? $this->assets->add_css( LIB."sweetalert-1/dist/sweetalert.css" ); ?>
		<? $this->assets->add_css( LIB."magnific-popup/jquery.magnific-popup.css" ); ?>

		<!-- template -->
		<? $this->assets->add_css( CSS."kmh_bootstrap.less" ); ?>
		<? $this->assets->add_css( CSS."kmh_global.less" ); ?>
		<? $this->assets->add_css( TCSS."template.less" ); ?>

		<? foreach ( $css as $key => $row ) : ?>
			<? $this->assets->add_css($row); ?>
		<? endforeach; ?>

		<!-- Bundle Style -->
		<? $this->assets->bundle_css(); ?>


	<!-- JS Library -->
		<? $this->assets->add_js( LIB."jquery-1.12.4.min.js" ); ?>
		<? $this->assets->add_js( LIB."modernizr-2.8.3.min.js" ); ?>
		<? $this->assets->add_js( LIB."bootstrap-3.3.7/dist/js/bootstrap.min.js" ); ?>
		<!-- Bundle Script-->
		<? $this->assets->bundle_js('bundle', ''); ?>
	<!-- JS IE9 미만 Compatible -->
		<!--[if lt IE 9]>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->


	<?=$basejs?>

	<!-- 하단 JS 번들 -->
		<? $this->assets->add_js( LIB."jquery.form.min.js" ); ?>
		<? $this->assets->add_js( LIB."lodash.min.js" ); ?>
		<? $this->assets->add_js( LIB."moment-2.22.2/moment.min.js" ); ?>
		<? $this->assets->add_js( LIB."moment-2.22.2/moment-with-locales.min.js" ); ?>
		<? $this->assets->add_js( LIB."jquery-validation-1.17.0/dist/jquery.validate.min.js" ); ?>
		<? $this->assets->add_js( LIB."jquery-validation-1.17.0/dist/additional-methods.min.js" ); ?>
		<? $this->assets->add_js( LIB."jquery-validation-1.17.0/dist/localization/messages_ko.min.js" ); ?>
		<? $this->assets->add_js( LIB."cleave.js/dist/cleave.min.js" ); ?>
		<? $this->assets->add_js( LIB."cleave.js/dist/addons/cleave-phone.kr.js" ); ?>
		<? $this->assets->add_js( LIB."pace-1.0.2/pace.min.js" ); ?>
		<? $this->assets->add_js( LIB."sweetalert-1/dist/sweetalert.min.js" ); ?>
		<? $this->assets->add_js( LIB."magnific-popup/jquery.magnific-popup.min.js" ); ?>
		<? $this->assets->add_js( LIB."pjax.min.js" ); ?>

		<? $this->assets->add_js( JS."kmh_common.js" ); ?>
		<? $this->assets->add_js( TJS."template.js" ); ?>

		<? $this->assets->bundle_js('bundle_footer'); ?>
	<!-- 하단 JS 번들 -->


</head>
<body class="page-container section_<?=$this->uri->segment(1)?> page_<?=$this->uri->segment(2)?>">

	<? if( $this->hasNav ) include_once (TVIEW.'header.php'); ?>

	<main id="main">
		<div class="container">
			<div id="content_wrap">
				<?=$content_body?>
			</div>
		</div>
		<div class="pjax_js">
			<!-- pjax js : <?=count($javascript)?> -->
			<? foreach ( $javascript as $key => $row ) : ?>
				<? $this->assets->load_js($row); ?>
			<? endforeach; ?>
		</div>
	</main> <!-- main -->

	<? if( $this->hasNav ) include_once TVIEW.'footer.php'; ?>
	<? if( $this->hasNav ) include_once( VIEWPATH."/_template/_common/common_element.php" ); ?>

</body>
</html>
<? ob_end_flush(); ?>
