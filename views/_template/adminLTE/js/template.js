// 메뉴바 쿠키
	$(".sidebar-toggle").click(function(event) {
		if( get_cookie('admin-sidebar-collapse') )
			del_cookie('admin-sidebar-collapse');
		else
			set_cookie('admin-sidebar-collapse', '1', 7);
	});
//