Vue.component('gnb-bar',{
	template: `
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
						<a class="navbar-brand" href="#">ADMIN</a>
					</div>

					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<ul class="nav navbar-nav">
								<router-link active-class="active" :to="key" tag="li" v-for="(nav_name, key) in nav" :key="key">
									<a>{{ nav_name }}</a>
								</router-link>
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
							<template v-if="is_login">
								<li><a href="/member/logout">로그아웃</a></li>
							</template>
							<template v-else>
								<li><a href="/member/join">회원가입</a></li>
								<li><a href="/member/login">로그인</a></li>
							</template>
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
	`,
	data: function () {
		return {
			nav: nav
		}
	},
	props: ['is_login']
});