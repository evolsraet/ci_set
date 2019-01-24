	<!-- 상단 CSS -->
		<!-- Bootstrap _ Common Style -->
		<? $this->assets->add_css( CSS."noto-sans-korean.css" ); ?>
		<? $this->assets->add_css( "https://cdn.rawgit.com/hiun/NanumSquare/master/nanumsquare.css" ); ?>
		<!-- Remark Core -->
		<? $this->assets->add_css( LIB."remark/css/bootstrap.less" ); ?>
		<? $this->assets->add_css( LIB."remark/css/bootstrap-extend.less" ); ?>
		<? $this->assets->add_css( LIB."remark/_base/css/site.less" ); ?>
		<!-- Remark Plugins -->
		<? $this->assets->add_css( LIB."remark/vendor/animsition/animsition.css" ); ?>
		<? $this->assets->add_css( LIB."remark/vendor/asscrollable/asScrollable.css" ); ?>
		<? $this->assets->add_css( LIB."remark/vendor/switchery/switchery.css" ); ?>
		<? $this->assets->add_css( LIB."remark/vendor/intro-js/introjs.css" ); ?>
		<? $this->assets->add_css( LIB."remark/vendor/slidepanel/slidePanel.css" ); ?>
		<? $this->assets->add_css( LIB."remark/vendor/flag-icon-css/flag-icon.css" ); ?>
		<? $this->assets->add_css( LIB."remark/vendor/slick-carousel/slick.css" ); ?>
		<? $this->assets->add_css( LIB."remark/vendor/bootstrap-table/bootstrap-table.css" ); ?>
		<? $this->assets->add_css( LIB."remark/vendor/toastr/toastr.css" ); ?>
		<? $this->assets->add_css( LIB."remark/vendor/select2/select2.css" ); ?>
		<? $this->assets->add_css( LIB."remark/vendor/asspinner/asSpinner.css" ); ?>
		<!-- Remark Fonts -->
		<? $this->assets->add_css( LIB."remark/fonts/web-icons/web-icons.css" ); ?>
		<? $this->assets->add_css( LIB."remark/fonts/brand-icons/brand-icons.css" ); ?>
		<!-- KMH -->
		<? $this->assets->add_css( LIB."font-awesome-4.7.0/less/font-awesome.less" ); ?>
		<? // $this->assets->add_css( LIB."pace-1.0.2/themes/blue/pace-theme-flash.css" ); ?>
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
		<? $this->assets->bundle_css( $this->template ); ?>

	<!-- 상단 Script -->
		<? $this->assets->add_js( LIB."jquery-1.12.4.min.js" ); ?>
		<? $this->assets->add_js( LIB."modernizr-2.8.3.min.js" ); ?>
		<? $this->assets->add_js( LIB."remark/vendor/modernizr/modernizr.js" ); ?>
		<? $this->assets->add_js( LIB."remark/vendor/breakpoints/breakpoints.js" ); ?>
		<!-- Remark Core -->
		<? $this->assets->add_js( LIB."remark/vendor/bootstrap/bootstrap.js" ); ?>
		<? $this->assets->add_js( LIB."remark/vendor/animsition/animsition.js" ); ?>
		<? $this->assets->add_js( LIB."remark/vendor/asscroll/jquery-asScroll.js" ); ?>
		<? $this->assets->add_js( LIB."remark/vendor/mousewheel/jquery.mousewheel.js" ); ?>
		<? $this->assets->add_js( LIB."remark/vendor/asscrollable/jquery.asScrollable.all.js" ); ?>
		<? $this->assets->add_js( LIB."remark/vendor/ashoverscroll/jquery-asHoverScroll.js" ); ?>
		<!-- Bundle Script-->
		<? $this->assets->bundle_js( $this->template , false); ?>

	<!--[if lt IE 9]>
		<? $this->assets->load_js( LIB."remark/vendor/html5shiv/html5shiv.min.js", false ); ?>
		<![endif]-->
	<!--[if lt IE 10]>
		<? $this->assets->load_js( LIB."remark/vendor/media-match/media.match.min.js", false ); ?>
		<? $this->assets->load_js( LIB."remark/vendor/respond/respond.min.js", false ); ?>
		<![endif]-->

	<script>
		Breakpoints();
	</script>

	<!-- 베이직 Script -->
		<?=$basejs?>

	<!-- 하단 Script -->
		<? $this->assets->add_js( LIB."jquery.form.min.js" ); ?>
		<? $this->assets->add_js( LIB."lodash.min.js" ); ?>
		<? $this->assets->add_js( LIB."moment-2.22.2/moment.min.js" ); ?>
		<? $this->assets->add_js( LIB."moment-2.22.2/moment-with-locales.min.js" ); ?>
		<? $this->assets->add_js( LIB."jquery-validation-1.17.0/dist/jquery.validate.min.js" ); ?>
		<? $this->assets->add_js( LIB."jquery-validation-1.17.0/dist/additional-methods.min.js" ); ?>
		<? $this->assets->add_js( LIB."jquery-validation-1.17.0/dist/localization/messages_ko.min.js" ); ?>
		<? $this->assets->add_js( LIB."cleave.js/dist/cleave.min.js" ); ?>
		<? $this->assets->add_js( LIB."cleave.js/dist/addons/cleave-phone.kr.js" ); ?>
		<? // $this->assets->add_js( LIB."pace-1.0.2/pace.min.js" ); ?>
		<? $this->assets->add_js( LIB."sweetalert-1/dist/sweetalert.min.js" ); ?>
		<? $this->assets->add_js( LIB."magnific-popup/jquery.magnific-popup.min.js" ); ?>
		<? $this->assets->add_js( LIB."placeholders.min.js" ); ?>

		<!-- Remark Scripts -->
		<? $this->assets->add_js( LIB."remark/js/core.js" ); ?>
		<? $this->assets->add_js( LIB."remark/_base/js/site.js" ); ?>
		<!-- Remark Plugins -->
		<? $this->assets->add_js( LIB."remark/vendor/switchery/switchery.min.js" ); ?>
		<? $this->assets->add_js( LIB."remark/vendor/intro-js/intro.js" ); ?>
		<? $this->assets->add_js( LIB."remark/vendor/screenfull/screenfull.js" ); ?>
		<? $this->assets->add_js( LIB."remark/vendor/slidepanel/jquery-slidePanel.js" ); ?>
		<? $this->assets->add_js( LIB."remark/vendor/slick-carousel/slick.js" ); ?>
		<? // $this->assets->add_js( "https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.js" ); ?>
		<? $this->assets->add_js( LIB."remark/vendor/bootstrap-table/bootstrap-table.min.js" ); ?>
		<? $this->assets->add_js( LIB."remark/vendor/bootstrap-table/extensions/mobile/bootstrap-table-mobile.js" ); ?>
		<? $this->assets->add_js( LIB."remark/vendor/bootstrap-table/bootstrap-table-ko-KR.js" ); ?>
		<? $this->assets->add_js( LIB."remark/vendor/toastr/toastr.js" ); ?>
		<? $this->assets->add_js( LIB."remark/vendor/select2/select2.full.min.js" ); ?>
		<? $this->assets->add_js( LIB."remark/js/components/select2.js" ); ?>
		<? $this->assets->add_js( LIB."remark/vendor/asspinner/jquery-asSpinner.min.js" ); ?>
		<? $this->assets->add_js( LIB."remark/js/components/asspinner.js" ); ?>
		<!-- Remark Configs -->
		<? $this->assets->add_js( LIB."remark/js/configs/config-colors.js" ); ?>
		<? $this->assets->add_js( LIB."remark/_base/js/configs/config-tour.js" ); ?>
		<!-- Remark Components -->
		<? $this->assets->add_js( LIB."remark/_base/js/sections/menu.js" ); ?>
		<? $this->assets->add_js( LIB."remark/_base/js/sections/menubar.js" ); ?>
		<? $this->assets->add_js( LIB."remark/_base/js/sections/gridmenu.js" ); ?>
		<? $this->assets->add_js( LIB."remark/_base/js/sections/sidebar.js" ); ?>
		<? $this->assets->add_js( LIB."remark/js/components/asscrollable.js" ); ?>
		<? $this->assets->add_js( LIB."remark/js/components/animsition.js" ); ?>
		<? $this->assets->add_js( LIB."remark/js/components/slidepanel.js" ); ?>
		<? $this->assets->add_js( LIB."remark/js/components/switchery.js" ); ?>
		<? $this->assets->add_js( LIB."remark/js/site_run.js" ); ?>
		<!-- KMH -->
		<? // $this->assets->add_js( LIB."pjax.min.js" ); ?>
		<? // $this->assets->add_js( JS."kmh_pjax.js" ); ?>
		<? $this->assets->add_js( JS."kmh_common.js" ); ?>
		<? $this->assets->add_js( TJS."template.js" ); ?>
		<? foreach ( (array)$javascript_bundle as $key => $row ) : ?>
			<? $this->assets->add_js($row); ?>
		<? endforeach; ?>
		<!-- BUNDLE -->
		<? $this->assets->bundle_js( "{$this->template}_footer"); ?>