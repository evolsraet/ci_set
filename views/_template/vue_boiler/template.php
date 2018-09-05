<? ob_start("ob_gzhandler"); ?>
<!DOCTYPE html>
<html lang="ko">
<!--[if lt IE 7]> <html class다"no-js lt-ie9 lt-ie8 lt-ie7" lang="ko"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="ko"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="ko"> <!--<![endif]-->

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<? include_once( VIEWPATH."/_template/_common/common_meta.php" ); ?>

	<? $this->kmh_assets->load_css( LIB."noto-sans-kr/styles.css" ); ?>

	<!-- BOOTSTRAP + 템플릿 스타일 번들 -->
	<? $this->kmh_assets->add_css( LIB."bootstrap-3.3.7/less/bootstrap.less" ); ?>
	<? $this->kmh_assets->add_css( LIB."bootstrap-3.3.7/less/theme.less" ); ?>
	<? // $this->kmh_assets->add_css( LIB."bootstrap-3.3.7/dist/css/bootstrap-theme.min.css" ); ?>
	<? $this->kmh_assets->add_css( TCSS."template.less" ); ?>
	<? $this->kmh_assets->bundle_css(); ?>
	<!-- BOOTSTRAP + 템플릿 스타일 번들 -->

	<? foreach ( $css as $key => $row ) : ?>
		<? $this->kmh_assets->load_css($row); ?>
	<? endforeach; ?>

<!--
	<script src="https://unpkg.com/babel-standalone@6/babel.min.js"></script>
	<script src="https://unpkg.com/vue@2.5.16/dist/vue.js"></script>
	<script src="https://unpkg.com/vue-router/dist/vue-router.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/vue-resource@1.5.1"></script>
	<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
-->

	<? $this->kmh_assets->add_js( LIB.'babel.min.js'); ?>
	<? $this->kmh_assets->add_js( LIB.'vue.js'); ?>
	<? $this->kmh_assets->add_js( LIB.'vue-router.js'); ?>
	<? $this->kmh_assets->add_js( LIB.'vue-resource.js'); ?>
	<? $this->kmh_assets->add_js( LIB.'axios.min.js'); ?>
	<? //$this->kmh_assets->add_js( LIB.'https://unpkg.com/vue-router/dist/vue-router.js'); ?>


	<? $this->kmh_assets->add_js( LIB."jquery-1.12.4.min.js" ); ?>
	<? $this->kmh_assets->add_js( LIB."moment-2.22.2/moment.min.js" ); ?>
	<? $this->kmh_assets->add_js( LIB."moment-2.22.2/moment-with-locales.min.js" ); ?>
	<? $this->kmh_assets->add_js( LIB."lodash.min.js" ); ?>

	<? $this->kmh_assets->add_js( LIB."cleave.js/dist/cleave.min.js" ); ?>
	<? $this->kmh_assets->add_js( LIB."cleave.js/dist/addons/cleave-phone.kr.js" ); ?>
	<? $this->kmh_assets->add_js( LIB."bootstrap-3.3.7/dist/js/bootstrap.min.js" ); ?>

	<? //$this->kmh_assets->add_js( 'https://unpkg.com/vue-cleave-component'); ?>


	<? $this->kmh_assets->bundle_js('bundle_vue_header'); ?>

	<?=$basejs?>
</head>
<body>
	<div id="app_wrapper">

		<? // include_once TVIEW.'header.php'; ?>
		<gnb-bar></gnb-bar>

		<div class="container">
			<div id="app">

			    <section class="content">
					<transition name="fade" mode="out-in">
						<router-view class="view"></router-view>
					</transition>
			    </section>

				<!-- <x-template></x-template> -->
			</div> <!-- app -->
		</div> <!-- container -->

	</div> <!-- app_wrapper -->

	<script type="text/x-template" id="x-template">
		<div>
			<h5>x-template</h5>
			<div v-for="(item, index) in nav_sub">{{ item.name }}</div>
		</div>
	</script>

	<!-- Vue.js 모음 번들 -->
	<? $this->kmh_assets->add_js( TJS."variable.js" ); ?>
	<? $this->kmh_assets->add_js( TJS."components/header.js" ); ?>
	<? $this->kmh_assets->add_js( TJS."components/x-template.js" ); ?>
	<? $this->kmh_assets->add_js( TJS."components/literals.js" ); ?>
	<? $this->kmh_assets->add_js( TJS."components/foo.js" ); ?>
	<? $this->kmh_assets->add_js( TJS."route.js" ); ?>
	<? $this->kmh_assets->add_js( TJS."app.js" ); ?>

	<? $this->kmh_assets->bundle_js( "bundle_vue" ); ?>
	<!-- Vue.js 모음 번들 -->

	<? foreach ( $javascript as $key => $row ) : ?>
		<? $this->kmh_assets->load_js($row); ?>
	<? endforeach; ?>

	<!-- KMH CUSTOM -->
	<div id="kmh_ajax_loading" style="display:none">
		<div style="position: relative; width:100%; height:100%; text-align:center; padding: 20px 0">
			<img src="<?=IMG?>common/ajax_loader_gray_64.gif" style="position: relative; margin:0 auto; vertical-align:middle;" alt="loading">
		</div>
	</div>
	<div id="kmh_ajax_div" style="display:none"></div>
	<iframe id="kmh_hidden_frame" name="kmh_hidden_frame" style="border:1px solid #ccc; width:90%; display: none"></iframe>
	<!-- KMH CUSTOM -->
</body>
</html>
<? ob_end_flush(); ?>