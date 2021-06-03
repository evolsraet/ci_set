<?
	use Phpform\Phpform;
	$form = new Phpform();

	$list = $this->{$this->model_name}
				->get_list();

?>

<div class="car_list">

	<!-- 검색 -->
	<form class="admin_table_search_form" name="admin_table_search_form" method="get">
		<input type="hidden" name="order_field" value="<?=$_GET['order_field']?>">
		<input type="hidden" name="order_value" value="<?=$_GET['order_value']?>">

		<div class="row">
			<div class="col-sm-6">
				<button class="open_write_form btn btn-success" type="button">등록</button>
				<button class="audit_tuncate btn btn-danger" type="button">모두 삭제</button>
			</div>
			<div class="col-sm-6">
				<div class="input-group">
					<input type="text" 
						class="form-control" 
						name="search_text" 
						placeholder="검색" 
						value="<?=$_GET['search_text']?>"
					>
					<span class="input-group-btn">
						<button type="submit" class="btn btn-primary"><i class="icon wb-search" aria-hidden="true"></i></button>
						<a href="<?=$_SERVER['REDIRECT_URL']?>" class="btn btn-default"><i class="icon wb-refresh" aria-hidden="true"></i></a>
					</span>
				</div>
			</div>
		</div>
	</form>

	<p class="text-right">
		총 <?=number_format($list['total'])?>건
	</p>

	<!-- 리스트 -->
	<table class="admin_table table table-bordered table-striped table_vertical_middle">
		<thead>
			<tr>				
				<th data-order_field="audit_shop_nm">매장명</th>
				<th data-order_field="audit_van_biz_no">점번</th>
				<th data-order_field="audit_trbiz_cd">수탁코드</th>
				<th data-order_field="audit_biz_no">사업자번호</th>
				<th data-order_field="audit_created_at">등록일</th>
				<th>관리</th>
			</tr>
		</thead>
		<tbody>
			<? foreach( (array) $list['list'] as $key => $row ) : ?>
			<tr>
				<td><?=$row->audit_shop_nm?></td>
				<td><?=($row->audit_van_biz_no)?></td>
				<td><?=$row->audit_trbiz_cd?></td>
				<td><?=$row->audit_biz_no?></td>
				<td><?=get_date($row->audit_created_at)?></td>
				<td class="text-center">
					<!-- <button type="button"
						class="open_write_form btn btn-sm btn-info"
						data-audit_id="<?=$row->audit_id?>"
						>
						수정
					</button> -->
					<button type="button"
						class="delete_act btn btn-sm btn-danger"
						data-audit_id="<?=$row->audit_id?>"
						>
						삭제
					</button>					
				</td>
			</tr>
			<? endforeach; ?>
		</tbody>
	</table>

	<? if( !count( $list['list'] ) ) : ?>
	<div class="text-center">
		내역이 없습니다.
	</div>	
	<? endif; ?>

	<div class="text-center">
		<?=$list['pagination']?>
	</div>

</div>	<!-- page_wrap -->

<script>
	// 엑셀 업로드
		$("#excel_upload_form button").click(function(event) {
			event.preventDefault();

		    if( !$("[name='excel_file']").val() ) {
		    	alert('파일을 선택해주세요.');
		    	return false;
		    }

		    $("#excel_upload_form button").button('loading');
		    // if( confirm("업로드 된 엑셀파일로 모든 연락처가 대체되며 복구할수 없습니다.\n만일을 위해 엑셀다운로드로 백업 후 작업해주세요.\n정말 업로드 하시겠습니까?") ) {
		    if( confirm("기존 디비가 교체됩니다.\n이 작업은 되돌릴수 없습니다. 정말 업로드 하시겠습니까?") ) {

		    	$("#excel_upload_log").css('display', 'block');
				var f = document.getElementById('excel_upload_form');
				f.submit();
		    }
		    $("#excel_upload_form button").button('reset');
		});

	// 등록 폼 열기
		$(".open_write_form").click(function(){
			var url = '<?=$this->baseuri?>write_form/';
			if( $(this).attr('data-audit_id') )
				url += $(this).attr('data-audit_id');

			$("#kmh_modal_lg .modal-content").load(url, function(){
				$("#kmh_modal_lg").modal();
			});
		});

	// 삭제
		$(".delete_act").click(function(){
			if( !confirm("정말 삭제 하시겠습니까?") )
				return false;

			var btn = $(this);
			var url = '<?=$this->baseuri?>delete_act/';

			$(btn).button('loading')
			$.post(url, {audit_id: $(btn).attr('data-audit_id')}, function(data, textStatus, xhr) {
				// alert(data.msg);
				$(btn).button('reset');
				location.reload();
			});
		});

	// 모두 삭제
		$(".audit_tuncate").click(function(event) {
			if( !confirm("정말 삭제 하시겠습니까?") )
				return false;

			var btn = $(this);
			var url = '<?=$this->baseuri?>audit_tuncate/';

			$(btn).button('loading')
			$.post(url, function(data, textStatus, xhr) {
				// alert(data.msg);
				$(btn).button('reset');
				location.reload();
			});
		});
</script>