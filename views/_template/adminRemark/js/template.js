// 사이드 메뉴바 상태 쿠키 저장
$(function(){
	$("#toggleMenubar > a").click(function(event) {
		set_cookie('site_menubar_fold',$.site.menubar.folded,7);
	});
});

// 관리자 테이블 정렬 
$(function(){
	// 초기화 
	var page_order_field = $("[name='order_field']").val();
	var page_order_value = $("[name='order_value']").val();
	if( page_order_field ) {
		$(".admin_table th[data-order_field='"+page_order_field+"']")
			.attr('data-order_value', page_order_value);
	}

	// 클릭시 정렬
	$(".admin_table th[data-order_field]").click(function(){
		var search_form = $(this).parents('.admin_table').siblings('.admin_table_search_form').eq(0);
		var order_field = $(this).attr('data-order_field');
		var order_value = $(this).attr('data-order_value');
		var new_asc = ( order_value == 'ASC' ) ? 'DESC' : 'ASC';

		$(search_form).find("[name='order_field']").val(order_field);
		$(search_form).find("[name='order_value']").val(new_asc);

		$("[name='admin_table_search_form']").submit();
	});
});