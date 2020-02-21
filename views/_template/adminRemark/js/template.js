// 사이드 메뉴바 상태 쿠키 저장
$(function(){
	$("#toggleMenubar > a").click(function(event) {
		set_cookie('site_menubar_fold',$.site.menubar.folded,7);
	});
});

// G-CRUD datetime
// $(function(){
//     $(".datepicker-input").datetimepicker({
//     	locale: 'ko',
//     	format : 'YYYY/MM/DD',
//     });

//     $(".datetime-input").datetimepicker({
//     	locale: 'ko',
//     	format : 'YYYY/MM/DD/ HH:mm:ss',
//     });
// });