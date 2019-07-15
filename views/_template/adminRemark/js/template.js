// 사이드 메뉴바 상태 쿠키 저장
$(function(){
	$("#toggleMenubar > a").click(function(event) {
		set_cookie('site_menubar_fold',$.site.menubar.folded,7);
	});
});