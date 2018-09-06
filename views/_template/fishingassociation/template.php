<? ob_start(); ?>
<!doctype html>
<html lang="ko">
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="ko"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="ko"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="ko"> <!--<![endif]-->
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?=$title?></title>
<meta name="description" content="<?=$title?>">


<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="keywords" content="<?=$keywords?><?=', '.$view['BBS_TITLE']?>">
<meta name="author" content="<?=$author?>">

<link rel="stylesheet" href="<?=TCSS?>style.css">
<link rel="stylesheet" href="<?=TCSS?>theme.css">
<link rel="stylesheet" href="<?=base_url(CSS."bootstrap-responsive.min.css");?>">

<?
	// LESS/CSS 선택 ( 'less' or 'css' )
	$lesscss = 'less';

	switch ($lesscss) {
		case 'less':
			$lesscss_code = 'stylesheet/less';
			break;
		default:
			$lesscss_code = 'stylesheet';
			break;
	}
?>

<!-- 템플릿 스타일 -->
<link rel="<?=$lesscss_code?>" type="text/css" href="<?=TCSS?>/template.<?=$lesscss?>">
<script src="<?=base_url(JS."less.js");?>"></script>

<link rel="stylesheet" href="<?=base_url(CSS."kmh_board.css");?>">
<link rel="stylesheet" href="<?=base_url(JS."fancybox/source/jquery.fancybox.css");?>">
<link rel="stylesheet" href="<?=base_url(CSS."font-awesome.min.css");?>">
<link rel="stylesheet" href="<?=base_url(CSS."font-awesome-ie7.min.css");?>">

<!-- extra CSS-->
<?php foreach($css as $c):?>
<link rel="stylesheet" href="<?=$c?>">
<?php endforeach;?>

<!-- extra fonts-->
<?php foreach($fonts as $f):?>
<link href="http://fonts.googleapis.com/css?family=<?=$f?>"	rel="stylesheet" type="text/css">
<?php endforeach;?>

<script src="<?=base_url(JS."libs/modernizr-2.6.1-respond-1.1.0.min.js");?>"></script>
<script src="<?=base_url(JS."libs/jquery-1.9.1.min.js");?>"></script>
<script src='<?=base_url(JS)?>/libs/jquery-ui.js'></script>
<link rel="stylesheet" href="<?=base_url(JS)?>/libs/jquery-ui.css">

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js'></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.css">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
 -->

<!-- Le fav and touch icons -->
<link rel="apple-touch-icon" sizes="57x57" href="<?=base_url(IMAGES)?>/ico/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="<?=base_url(IMAGES)?>/ico/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="<?=base_url(IMAGES)?>/ico/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="<?=base_url(IMAGES)?>/ico/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="<?=base_url(IMAGES)?>/ico/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="<?=base_url(IMAGES)?>/ico/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="<?=base_url(IMAGES)?>/ico/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="<?=base_url(IMAGES)?>/ico/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="<?=base_url(IMAGES)?>/ico/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="<?=base_url(IMAGES)?>/ico/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?=base_url(IMAGES)?>/ico/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="<?=base_url(IMAGES)?>/ico/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?=base_url(IMAGES)?>/ico/favicon-16x16.png">
<link rel="manifest" href="<?=base_url(IMAGES)?>/ico/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">



</head>
<body class="page-container <?=$this->uri->segment(2)==''?'layout_main':'layout_sub'?> page_<?=$this->uri->segment(2)?>_<?=$this->uri->segment(3)?>" >
	<dl id="accessibility">
		<dt>Skip Navigation</dt>
		<dd><a href="#content">Skip to Content</a></dd>
		<!-- <dd><a href="#sitemap">Skip to Sitemap</a></dd> -->
	</dl>

	<?php echo $basejs?>
	<a href="#" class="scrollToTop"></a>

	<!-- 컨텐츠 -->
	<? $this->load->view(TVIEW."header.php"); ?>

	<div id="main" role="main">
			<? if ( $this->uri->segment(2)=='' ) : 	// main ?>
				<div id="content" class="page_<?=$this->uri->segment(1)?>">
					<?php echo $content_body ?>
				</div>
			<? else : 								// sub?>
				<div class="container">
					<div class="_span3" id="lnb">
						<h3 class="lnb_title">
							<?=$nav[$this->uri->segment(1)]?>
						</h3>
						<ul class="lnb_ul unstyled">
							<? foreach ( $nav_sub[ $this->uri->segment(1) ] as $nav_key => $nav_row ) : ?>
								<? if ( $this->uri->segment(1)=='member' && $this->uri->segment(2)!=$nav_key ) { continue; } ?>
								<li class="<?=isActive($this->uri->segment(2),$nav_key)?>"
									>
									<a href="/<?=$this->uri->segment(1)?>/<?=$nav_key?>">
										<?=$nav_row?>
										<i class="icon icon-chevron-right"></i>
									</a>
								</li>
							<? endforeach; ?>
						</ul>
					</div>
					<div id="content" class="_span9 page_<?=$this->uri->segment(1)?>">
						<div class="page_info">
							<h2>
								<?=$nav_sub[ $this->uri->segment(1) ][ $this->uri->segment(2) ]?>
							</h2>
							<ul class="visible-desktop baracum unstyled">
								<li>
									<i class="icon icon-home"></i>
									<i class="icon icon-angle-right"></i>
								</li>
								<li>
									<?=$nav[$this->uri->segment(1)]?>
									<i class="icon icon-angle-right"></i>
								</li>
								<li class="last">
									<?=$nav_sub[ $this->uri->segment(1) ][ $this->uri->segment(2) ]?>
								</li>
							</ul>
						</div>
						<?php echo $content_body ?>
					</div>
				</div>
			<? endif; ?>
		</div>
	</div>

	<? $this->load->view(TVIEW."footer.php"); ?>
	<!-- // 컨텐츠 -->

	<script src="<?=base_url(JS."bootstrap.min.js");?>"></script>
	<script src="<?=base_url(JS."libs/underscore-min-1.4.4.js");?>"></script>
	<script src="<?=base_url(JS."fancybox/source/jquery.fancybox.js");?>"></script>
	<script src="<?=base_url(JS."jquery-validation/jquery.validate.js");?>"></script>
	<script src="<?=base_url(JS."jquery-validation/localization/messages_ko.js");?>"></script>
	<script src="<?=base_url(JS."raty-2.5.2/lib/jquery.raty.min.js");?>"></script>
	<script src="<?=base_url(JS."plugins.js");?>"></script>
	<script src="<?=base_url(JS."script.js");?>"></script>
	<script src="<?=base_url(JS."common.js");?>"></script>

	<script src="<?=base_url(JS."bxslider/jquery.bxslider.min.js");?>"></script>
	<link rel="stylesheet" href="<?=base_url(JS."bxslider/jquery.bxslider.bdsr.css");?>">

	<script src="<?=base_url(TJS."template.js");?>"></script>

	<!-- extra js-->
	<?php foreach($javascript as $js):?>
	<script defer src="<?=$js?>"></script>
	<?php endforeach;?>

	<script>
		// 모더나이저 활용 플레이스홀더
		$(document).ready(function() {
			if (!Modernizr.input.placeholder) {

				$('[placeholder]').focus(function() {
					var input = $(this);
					if (input.val() == input.attr('placeholder')) {
						input.val('');
						input.removeClass('placeholder');
					}
				}).blur(function() {
					var input = $(this);
					if (input.val() == '' || input.val() == input.attr('placeholder')) {
						input.addClass('placeholder');
						input.val(input.attr('placeholder'));
					}
				}).blur();
				$('[placeholder]').parents('form').submit(function() {
					$(this).find('[placeholder]').each(function() {
						var input = $(this);
						if (input.val() == input.attr('placeholder')) {
							input.val('');
						}
					})
				});

			}
		});
	</script>
	<script>
		// 브라우저 업데이트 알림
		var $buoop = {vs:{i:9,f:-4,o:-4,s:7},c:4};
		function $buo_f(){
			var e = document.createElement("script");
			e.src = "//browser-update.org/update.min.js";
			document.body.appendChild(e);
		};
		try {document.addEventListener("DOMContentLoaded", $buo_f,false)}
		catch(e){window.attachEvent("onload", $buo_f)}
	</script>

</body>
</html>
<? ob_end_flush(); ?>
