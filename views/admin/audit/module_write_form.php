<?
	use Phpform\Phpform;
	$form = new Phpform();

	$form_name   = "{$this->folder_name}_form";
	$form_action = $this->baseuri . 'insert_update_act';
	$form_config = array(
		'class' => 'form-horizontal',
		'ajax_before'=>"{$this->folder_name}_form_before",
		'ajax_after'=>"{$this->folder_name}_form_success",
	);
	$form->open($form_name, $form_action, $form_config );
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title">감사 매장</h4>
</div>
<div class="modal-body">
	<div class="margin-bottom-20">
		<select name="audit_trbiz_cd" id="audit_trbiz_cd">
			<option value=""><?=$this->tire->this_mm()?> 매장선택</option>
			<? foreach( (array) $this->shop_model->audit_able(true) as $key => $row ) : ?>
				<option value="<?=$row->TRBIZ_CD?>"
					data-van_biz_no="<?=$row->VAN_BIZ_NO?>"
					data-biz_no="<?=$row->BIZ_NO?>"
					data-shop_nm="<?=$row->SHOP_NM?>"
					>
					<?=$row->SHOP_NM?> (<?=$row->BIZ_NO?> / <?=$row->VAN_BIZ_NO?>)
				</option>
			<? endforeach; ?>
		</select>
	</div>

	<?
		// 파일 임시 저장용 코드 (게시글 작성시 필수)
		// $form->input('파일 저장 코드','hidden', '_biz_files_code',
		// 	$this->files->make_tmp_code( 'biz' )
		// );

		if( $view->audit_id ) :
			$form->input('audit_id','hidden', 'audit_id', $view->audit_id);
		else :
		endif;

		$form->input(
			'매장명',
			'text',
			'audit_shop_nm',
			$view->audit_shop_nm,
			array(
				'required'=>'required',
				'readonly'=>'readonly'
			)
		);

		$form->input(
			'사업자등록번호',
			'text',
			'audit_biz_no',
			$view->audit_biz_no,
			array(
				'required'=>'required',
				'readonly'=>'readonly'
			)
		);

		$form->input(
			'점번',
			'text',
			'audit_van_biz_no',
			$view->audit_van_biz_no,
			array(
				'required'=>'required',
				'readonly'=>'readonly'
			)
		);

?>				

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
	<button type="submit" class="btn btn-primary">저장</button>
</div>
<? $form->close(); ?>

<script>
	$(function(){
		$("#audit_trbiz_cd").select2({
			width: '100%'
		});

		$('#audit_trbiz_cd').on('select2:select', function (e) {
			var selected = $('#audit_trbiz_cd option:selected');
			$("#audit_biz_no").val( $(selected).data('biz_no') );
			$("#audit_van_biz_no").val( $(selected).data('van_biz_no') );
			$("#audit_shop_nm").val( $(selected).data('shop_nm') );
		});
	});

	// 폼 (추가/수정)
		function <?=$this->folder_name?>_form_before(btn) {
			return true;
		}

		function <?=$this->folder_name?>_form_success(response, btn) {
			if( response.status == undefined ) {
				console.log( response );
				alert('통신에러');
			} else if( response.status == 'ok' ) {
				location.reload();
			} else {
				alert( response.msg );
				if( response.field )
					$("[name='"+response.field+"']").focus();
			}

			$(btn).button('reset');			
		}	
</script>