// header
	// 네비게이션
	// PJAX 작동시 자동 처리 되도록
	$(function(){
		$("#header .nav a:not('.dropdown-toggle')").click(function(){
			$("#header .nav li").removeClass('active');
			$(this).parents('li').addClass('active');

			// 모바일
			$(".navbar-collapse.collapse").removeClass('in');
		});
	});