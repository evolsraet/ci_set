<? if( !$view->pd_deleted_at ) : ?>
<div id="option_form" class="form-inline">
	<input type="text" class="form-control ot_type" placeholder="옵션 종류">
	<input type="text" class="form-control ot_name" placeholder="이름">
	<input type="text" class="form-control ot_price" placeholder="추가 가격">

	<button type="button" class="btn btn-sm btn-primary" title="추가"><i class="fa fa-plus"></i></button>
</div>
<hr>
<? endif; ?>
<div id="option_list"></div>

<script>
	function option_list() {
		$.get('/admin/option_list/<?=$pd_id?>', function(data) {
			$("#option_list").html(data);
		});
	}

	$(document).ready(function() {
		option_list();

		$("#option_form .btn-primary").click(function(event) {
			var data = {
				ot_pd_id: $("#pd_id").val(),
				ot_type: $("#option_form .ot_type").val(),
				ot_name: $("#option_form .ot_name").val(),
				ot_price: $("#option_form .ot_price").val(),
			};

			if( data.ot_type=='' || data.ot_name=='' ) {
				alert('옵션 종류와 이름은 필수 입력입니다.');
				return false;
			}

			$.post('/admin/option_update', data, function(data, textStatus, xhr) {
				option_list();
				$("#option_form .ot_type").val('');
				$("#option_form .ot_name").val('');
				$("#option_form .ot_price").val('');
			});
		});
	});
</script>