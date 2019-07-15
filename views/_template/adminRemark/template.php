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

<?
	$site_menubar_fold = $_COOKIE['site_menubar_fold']=='true' ? 'site-menubar-unfold' : 'site-menubar-fold';
?>

<body class="<?=$body_class?> <?=$site_menubar_fold?> site-menubar-keep" data-auto-menubar="false">
	<!-- <div class="_wrapper"> -->
		<? if( $this->has_nav ) include_once (TPATH.'header.php'); ?>

		<main id="_main" class="page animsition">
			<div class="_container">
				<div id="content_wrap" class="page-content">
					<? include_once(MODULEPATH.'page_notice.php'); ?>
					<div class="page-header">
						<h1 class="page-title">
							<?
								// echo "[{$this->page_name}]";
								$admin_nav = array_merge( $admin_nav, (array) $this->config->item('admin_mall_nav') );
								$admin_nav_sub = array_merge( $admin_nav_sub, (array) $this->config->item('admin_mall_nav_sub') );

								if( $this->page_name )
									$page_title = $this->page_name;
								elseif( $admin_nav_sub[ $this->uri->segment(2) ][ $this->uri->segment(3) ] )
									$page_title = $admin_nav_sub[ $this->uri->segment(2) ][ $this->uri->segment(3) ];
								else
									$page_title = $admin_nav[ $this->uri->segment(2) ]['text'];

								echo $page_title;
							?>
						</h1>
						<!--
						<ol class="breadcrumb">
							<li><a href="/admin">ADMIN</a></li>
							<li>설정</li>
							<li>게시판</li>
						</ol>
						 -->
					</div>
					<!-- 기타 컨트롤러 일 경우 -->
					<? if( $this->page_wrap ) : ?>
						<div class="panel">
							<div class="panel-body">
					<? endif; ?>
					<!-- End Of 기타 컨트롤러 일 경우 -->

						<?=$content_body?>

					<!-- 기타 컨트롤러 일 경우 -->
					<? if( $this->page_wrap ) : ?>
							</div>	<!-- // panel-body -->
						</div>	<!-- // panel -->
					<? endif; ?>
					<!-- End Of 기타 컨트롤러 일 경우 -->
				</div>
			</div>
		</main> <!-- main -->

		<? if( $this->has_nav ) include_once TPATH.'footer.php'; ?>
		<? if( $this->has_nav ) include_once( VIEWPATH."/_template/_common/common_element.php" ); ?>
	<!-- </div> -->

	<!-- 추가 Script -->
	<? foreach ( $javascript as $key => $row ) : ?>
		<? $this->assets->load_js($row); ?>
	<? endforeach; ?>

</body>
</html>
<? ob_end_flush(); ?>
