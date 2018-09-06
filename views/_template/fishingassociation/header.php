	<!-- /header -->

	<header id="header" class="">

		<div class="login_info">
			<div class="container">
				<ul class="login_info_ul">
					<li><a href="/">HOME</a></li>

					<? if ( is_login() ) : ?>
						<li><a href="/member/logout_now">로그아웃</a></li>
						<? if ( is_admin() ) : ?>
							<li><a href="/admin">관리자</a></li>
						<? endif; ?>
					<? else : ?>
						<li><a href="javascript:o_login()">로그인</a></li>
						<li><a href="/member/join">회원가입</a></li>
					<? endif; ?>
				</ul>
			</div>
		</div>

		<div class="container">
			<a href="#" title="mobile navigation" id="cd-menu-trigger">
				<span class="cd-menu-icon"></span>
			</a>
			<a class="logo" href="/">
				<img src="<?=base_url(TIMG."logo.png")?>" alt="logo">
			</a>
			<ul class="gnb">

				<? foreach ( $nav as $nav_key => $nav_row ) : ?>
					<? if ( $nav_key=='member' ) { continue; } ?>
					<li class="<?=isActive($this->uri->segment(1),$nav_key)?>">
						<a href="/<?=$nav_key?>/<?=array_first($nav_sub[$nav_key],'key')?>" title="<?=$nav_row?>"><?=$nav_row?></a>
						<? if ( count($nav_sub[$nav_key])  ) : ?>
							<ul class="gnb_sub" id="gnb_sub_<?=$nav_key?>">
							<? foreach ( $nav_sub[$nav_key] as $sub_key => $sub_row ) : ?>
								<li class="<?=isActive($this->uri->segment(2),$sub_key)?>">
									<a href="/<?=$nav_key?>/<?=$sub_key?>" title="<?=$sub_row?>"><?=$sub_row?></a>
								</li>
							<? endforeach; ?>
							</ul>
						<? endif; ?>
					</li>
				<? endforeach; ?>

			</ul>
		</div>
	</header>

	<div id="mobile_gnb_wrap">
		<div class="mobile_gnb_bg">
			<div class="text-right">
			<a href="#" class="close_mobile_gnb" title="close menu"><span></span></a>
			</div>
			<ul class="mobile_gnb">

				<? foreach ( $nav as $nav_key => $nav_row ) : ?>
					<? if ( $nav_key=='member' ) { continue; } ?>
					<li class="<?=isActive($this->uri->segment(1),$nav_key)?>">
						<a href="#" title="<?=$nav_row?>"><?=$nav_row?>
							<i class="icon icon-chevron-right"></i>
						</a>
						<? if ( count($nav_sub[$nav_key])  ) : ?>
							<ul class="mobile_gnb_sub" id="mobile_gnb_sub_<?=$key?>">
							<? foreach ( $nav_sub[$nav_key] as $sub_key => $sub_row ) : ?>
								<li class="<?=isActive($this->uri->segment(2),$sub_key)?>">
									<a href="/<?=$nav_key?>/<?=$sub_key?>" title="<?=$sub_row?>"><?=$sub_row?></a>
								</li>
							<? endforeach; ?>
							</ul>
						<? endif; ?>
					</li>
				<? endforeach; ?>

			</ul>
			<ul class="extra_function">
				<? if ( is_login() ) : ?>
					<li><a href="/member/logout_now">로그아웃</a></li>
					<? if ( is_admin() ) : ?>
						<li><a href="/admin">관리자</a></li>
					<? endif; ?>
				<? else : ?>
					<li><a href="javascript:o_login()"><i class="icon-gear"></i> 로그인</a></li>
				<? endif; ?>
			</ul>
		</div>
	</div>



	<script>
		function search_form() {
			// $('#search_form input').not('#where').not('#text').val('');
			$('#search_form #p').val('');
			$('#search_form').submit();
		}

		$("[name='where'], [name='text']").bind('keyup',function(e){
			if( e.which == 13){
				search_form();
			}
		});
	</script>

	<!-- /header -->