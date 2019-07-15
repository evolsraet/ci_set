<script>
<? foreach( (array) $this->session->flashdata('option') as $key => $row ) : ?>
	<?
		$class = $row['class']!='' ? $row['class'] : 'success';
	?>

	toastr.<?=$class?>('<?=$row['msg']?>');
<? endforeach; ?>
</script>

<?
	$options = $this->option_model
				->where('ot_pd_id', $this->uri->segment(3))
				->order_by('ot_type', 'ASC')
				->order_by('ot_name', 'ASC')
				->get_all();
?>

<? if( !obj_count($options) ) : ?>
	<div class="alert alert-info text-center">
		등록된 옵션이 없습니다.
	</div>
<? endif; ?>

<ul id="option_list_ul" class="list-unstyled">
	<? foreach( (array) $options as $key => $row ) : ?>
		<li class="form-inline"
			data-temp_key="<?=$key?>"
			>
			<input type="text"
				class="form-control ot_type"
				placeholder="옵션 종류"
				readonly
				value="<?=$row->ot_type?>"
			>
			<input type="text"
				class="form-control ot_name"
				placeholder="이름"
				readonly
				value="<?=$row->ot_name?>"
			>
			<input type="text"
				class="form-control ot_price"
				placeholder="추가 가격"
				value="<?=$row->ot_price?>"
			>
			<span class="checkbox-custom checkbox-primary" data-toggle="tooltip" title="사용여부">
				<input type="checkbox"
					id="ot_use_<?=$key?>"
					class="ot_use"
					<?=($row->ot_use)?"checked":""?>
					>
				<label for="ot_use_<?=$key?>"></label>
			</span>

			<button type="button" class="btn_mod btn btn-sm btn-success" title="수정"><i class="fa fa-pencil"></i></button>
			<button type="button" class="btn_del btn btn-sm btn-danger" title="삭제"><i class="fa fa-remove"></i></button>
		</li>
	<? endforeach; ?>
</ul>

<script>
	$(document).ready(function() {
		// 수정
		$("#option_list_ul li .btn_mod").click(function(event) {
			var temp_key = $(this).closest('li').attr('data-temp_key');
			if( !confirm("정말 수정하시겠습니까?") )	return false;

			var data = {
				ot_pd_id: $("#pd_id").val(),
				ot_type: $(this).siblings('input.ot_type').val(),
				ot_name: $(this).siblings('input.ot_name').val(),
				ot_price: $(this).siblings('input.ot_price').val(),
				ot_use: $('#ot_use_'+temp_key).prop('checked'),
			};
			$.post('/admin/option_update', data, function(data, textStatus, xhr) {
				option_list();
			});
			// console.log(temp_key);
			console.log(data);
		});

		// 삭제
		$("#option_list_ul li .btn_del").click(function(event) {
			// var temp_key = $(this).closest('li').attr('data-temp_key');
			if( !confirm("삭제된 옵션은 복구 할 수 없습니다.\n정말 삭제하시겠습니까?") )	return false;
			var data = {
				ot_pd_id: $("#pd_id").val(),
				ot_type: $(this).siblings('input.ot_type').val(),
				ot_name: $(this).siblings('input.ot_name').val(),
				ot_price: $(this).siblings('input.ot_price').val(),
			};
			$.post('/admin/option_delete/', data, function(data, textStatus, xhr) {
				option_list();
			});
			// console.log(temp_key);
		});
	});
</script>
