<!DOCTYPE html>
<html lang="ko">
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="ko"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="ko"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->

<head>
	<? include_once( VIEWPATH."/_template/_common/common_meta.php" ); ?>

	<? $this->assets->load_css( LIB."noto-sans-korean/css/noto-sans-korean.css" ); ?>

	<? $this->assets->load_css( TDIR.'build/bootstrap-less/bootstrap.less'); ?>
	<? $this->assets->load_css( LIB."font-awesome-4.7.0/less/font-awesome.less" ); ?>
	<? $this->assets->load_css( TDIR.'build/less/AdminLTE.less'); ?>
	<? $this->assets->load_css( TDIR.'build/less/skins/_all-skins.less'); ?>

	<? $this->assets->load_css( CSS."kmh_bootstrap.less" ); ?>
	<? $this->assets->load_css( CSS.'kmh_global.less'); ?>
	<? $this->assets->load_css( TCSS.'template.less'); ?>

	<? foreach ( $css as $key => $row ) : ?>
		<? $this->assets->load_css($row); ?>
	<? endforeach; ?>

	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

	<? $this->assets->load_js( LIB."jquery-1.12.4.min.js", '' ); ?>
	<? $this->assets->load_js( LIB."modernizr-2.8.3.min.js", '' ); ?>

	<? $this->assets->load_js( LIB."bootstrap-3.3.7/dist/js/bootstrap.min.js" ); ?>
	<? $this->assets->load_js( TDIR."dist/js/adminlte.js" ); ?>

	<?=$basejs?>

</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="
		hold-transition fixed skin-black sidebar-mini
		page-container section_<?=$this->uri->segment(1)?> page_<?=$this->uri->segment(2)?>
		<?=$this->input->cookie('admin-sidebar-collapse')?"sidebar-collapse":""?>
	">
	<div class="wrapper">

		<? if( $this->has_nav ) include_once (TPATH.'header.php'); ?>

		<main id="main">
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<section class="content-header">
					<h1>
						<?=$admin_nav_sub[ $this->uri->segment(2) ][ $this->uri->segment(3) ]?>
						<!-- <small>Optional description</small> -->
					</h1>
					<!--
					<ol class="breadcrumb">
						<li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
						<li class="active">Here</li>
					</ol>
					 -->
				</section>

				<!-- Main content -->
				<section id="content_wrap" class="content container-fluid">
					<!-- 기타 컨트롤러 일 경우 -->
					<? if( $this->router->fetch_class()!='admin' ) : ?>
						<div class="box">
							<div class="box-header with-border">
								<h3 class="box-title">&nbsp;</h3>
								<div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
									</button>
									<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
								</div>
							</div>
							<!-- /.box-header -->
							<div class="box-body">
					<? endif; ?>
					<!-- End Of 기타 컨트롤러 일 경우 -->
						<?=$content_body?>
					<!-- 기타 컨트롤러 일 경우 -->
					<? if( $this->router->fetch_class()!='admin' ) : ?>
							</div>	<!-- // box-body -->
						</div>	<!-- // box -->
					<? endif; ?>
					<!-- End Of 기타 컨트롤러 일 경우 -->
				</section>
				<!-- /.content -->
			</div>
		</main>

		<? if( $this->has_nav ) include_once TPATH.'footer.php'; ?>
		<? if( $this->has_nav ) include_once( VIEWPATH."/_template/_common/common_element.php" ); ?>

	</div>
	<!-- ./wrapper -->

	<!-- 하단 JS 번들 -->
		<? $this->assets->load_js( LIB."jquery.form.min.js" ); ?>
		<? $this->assets->load_js( LIB."sweetalert-1/dist/sweetalert.min.js" ); ?>
		<? $this->assets->load_js( LIB."magnific-popup/jquery.magnific-popup.min.js" ); ?>

		<? $this->assets->load_js( JS."kmh_common.js" ); ?>
		<? $this->assets->load_js( TJS."template.js" ); ?>
	<!-- 하단 JS 번들 -->

	<!-- REQUIRED JS SCRIPTS -->
	<? foreach ( $javascript as $key => $row ) : ?>
		<? $this->assets->load_js($row); ?>
	<? endforeach; ?>
</body>
</html>