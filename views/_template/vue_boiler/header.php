<header id="header">
	<nav class="navbar navbar-inverse navbar-static-top">
		<div class="container">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#"><?=$this->config->item('site_title')?></a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
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
								<ul class="dropdown-menu">
									<? foreach ( $nav_sub[$nav_key] as $sub_key => $sub_row ) : ?>
										<li class="<?=is_active($this->uri->segment(2), $sub_key)?>">
											<a href="/<?=$nav_key?>/<?=$sub_key?>"><?=$sub_row?></a>
										</li>
									<? endforeach; ?>
								</ul>
							</li>
						<? else : // 드롭다운 ifelse ?>
							<li class="<?=is_active($this->uri->segment(1), $nav_key)?>">
								<a href="/<?=$nav_key?>/<?=array_first($nav_sub[$nav_key],'key')?>">
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
					<? if ( is_login() ) : // 로그인 여부 ?>
						<li><a href="/member/logout">로그아웃</a></li>
					<? else : // 로그인 여부 ?>
						<li><a href="/member/join">회원가입</a></li>
						<li><a href="/member/login">로그인</a></li>
					<? endif; // 로그인 여부 ?>

					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="#">Action</a></li>
							<li><a href="#">Another action</a></li>
							<li><a href="#">Something else here</a></li>
							<li role="separator" class="divider"></li>
							<li><a href="#">Separated link</a></li>
						</ul>
					</li>
				</ul>

			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>
</header>