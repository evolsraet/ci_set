<!-- 제품리스트 -->

<!-- 버튼 -->
<div class="button_wrap text-left">
	<a href="/admin/product/write" class="btn btn-primary">제품등록</a>
</div>

<table id="product_list"
		data-toggle="table"
		data-url="/admin/product/get_list"
		data-query-params="queryParams"
		data-mobile-responsive="true"
		data-pagination="true"
		data-icon-size="outline"
		data-search="true"
>
	<thead>
		<tr>
			<!-- <th data-field="state" data-checkbox="true"></th> -->
			<th data-sortable="true" data-align="left" data-field="cate_fullname">카테고리</th>
			<th data-formatter="formatter_pd_name" data-sortable="true" data-field="pd_name">제품명</th>
			<th data-sortable="true" data-align="center" data-field="pd_use">사용여부</th>
			<th data-sortable="true" data-align="right" data-field="pd_price">기본가</th>
			<th data-sortable="true" data-align="right" data-field="pd_min">최소주문수량</th>
		</tr>
	</thead>
</table>



<script>
	function formatter_pd_name(value, row, index, field) {
		return '<a href="/admin/product/view/'+ row.pd_id +'">' + value + '</a>';
	}
	// data-query-params
	function queryParams() {
		return {
			type: 'owner',
			sort: 'updated',
			direction: 'desc',
			per_page: 100,
			page: 1
		};
	}

	$(function(){
		$('#product_list').bootstrapTable({
			toolbar: '.button_wrap',
		});
	});
</script>