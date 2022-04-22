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
				<th data-order_field="config_desc">항목</th>
				<th data-order_field="config_value">값</th>
				<th>관리</th>
			</tr>
		</thead>
		<tbody>
			<? foreach( (array) $list['list'] as $key => $row ) : ?>
			<tr>
				<td><?=$row->config_desc?></td>
				<td><?=$row->config_value?></td>
				<td class="text-center">
					<button type="button"
						class="open_write_form btn btn-sm btn-info"
						data-config_id="<?=$row->config_id?>"
						>
						수정
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
			if( $(this).attr('data-config_id') )
				url += $(this).attr('data-config_id');

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
			$.post(url, {config_id: $(btn).attr('data-config_id')}, function(data, textStatus, xhr) {
				// alert(data.msg);
				$(btn).button('reset');
				location.reload();
			});
		});
		
</script>