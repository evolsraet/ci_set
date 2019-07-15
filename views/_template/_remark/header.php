<? include( MODULEPATH . 'popup.php' ); ?>

<header id="header">
	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#gnb_wrap" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/">
					<!-- <img src="/assets/img/logo.svg" alt="logo"> -->
					<span>
						<?=$this->config->item('site_title')?>
					</span>
				</a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="gnb_wrap">
				<ul class="gnb nav navbar-nav">
					<? foreach ( $nav as $nav_key => $nav_row ) : ?>
						<? if ( $nav_key == 'member' ) : // 회원 ?>
							<? continue; ?>
						<? endif; // 회원 ?>
						<? if ( count($nav_sub[ $nav_key ]) > 1 ) : // 드롭다운 ifelse ?>
							<li class="dropdown <?=is_active($this->uri->segment(1), $nav_key)?>">
								<a href="#"
									class="dropdown-toggle"
									data-toggle="dropdown"
									role="button"
									aria-haspopup="true"
									aria-expanded="false"
									>
									<?=$nav_row?> <span class="caret"></span>
								</a>
								<ul class="gnb_sub dropdown-menu">
									<? foreach ( $nav_sub[$nav_key] as $sub_key => $sub_row ) : ?>
										<li class="<?=is_active($this->uri->segment(2), $sub_key)?>">
											<a href="/<?=$nav_key?>/<?=$sub_key?>" title="<?=$sub_row?>">
												<?=$sub_row?>
											</a>
										</li>
									<? endforeach; ?>
								</ul>
							</li>
						<? else : // 드롭다운 ifelse ?>
							<li class="<?=is_active($this->uri->segment(1), $nav_key)?>">
								<?
									$sub_link = array_first($nav_sub[$nav_key],'key');
								?>
								<a href="<?="/{$nav_key}/{$sub_link}"?>"
									title="<?=$nav_row?>"
									>
									<?=$nav_row?>
								</a>
							</li>
						<? endif; // 드롭다운 ifelse ?>
					<? endforeach; ?>
				</ul>
				<!--
				<form class="navbar-form navbar-left">
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Search">
					</div>
					<button type="submit" class="btn btn-default">Submit</button>
				</form>
				-->


				<ul class="nav navbar-nav navbar-right">
					<? if( 0 && !$this->members->is_admin() ) : ?>
						<li class="dropdown cart_count">
							<a href="/mall/cart"
								title="장바구니"
								data-toggle="tooltip"
								data-placement="bottom"
								data-container="body"
								data-animation="scale-up"
								role="button"
								rel="nofollow"
								>
								<i class="icon wb-shopping-cart"></i>
								<span class="badge badge-danger up cart_count_text"><?=$this->cart_model->login_check_where()->count_by()?></span>
							</a>
						</li>
						<li class="dropdown">
							<a href="/mall/order"
								title="주문확인"
								data-toggle="tooltip"
								data-placement="bottom"
								data-container="body"
								data-animation="scale-up"
								role="button"
								rel="nofollow"
								>
								<i class="icon wb-order"></i>
							</a>
						</li>
					<? endif; ?>

					<? if ( $this->members->is_login() ) : // 로그인 여부 ?>

						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" rel="nofollow">
								<span class="avatar avatar-xs avatar-online">
									<?=$this->files->member_image($this->logined->mb_id, 23)?>
					                <i></i>
              					</span>
              					&nbsp;
								<?=$this->logined->mb_display?> <span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
								<li><a href="/member/update" rel="nofollow">정보수정</a></li>
								<li><a href="/member/point" rel="nofollow">포인트 내역</a></li>
								<? if( $this->members->is_admin() ) : ?>
								<li><a href="/admin" rel="nofollow">관리자</a></li>
								<? endif; ?>
								<li role="separator" class="divider"></li>
								<li><a href="/member/logout" rel="nofollow">로그아웃</a></li>
							</ul>
						</li>
					<? else : // 로그인 여부 ?>
						<li><a href="/member/join" rel="nofollow">회원가입</a></li>
						<li><a href="/member/login" rel="nofollow">로그인</a></li>
					<? endif; // 로그인 여부 ?>
				</ul>

			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>
</header>
