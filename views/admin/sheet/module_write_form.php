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
	<h4 class="modal-title">양식</h4>
</div>
<div class="modal-body">
	<?
		// 파일 임시 저장용 코드 (게시글 작성시 필수)
		// $form->input('파일 저장 코드','hidden', '_biz_files_code',
		// 	$this->files->make_tmp_code( 'biz' )
		// );

		if( $view->sheet_id ) :
			$form->input('sheet_id','hidden', 'sheet_id', $view->sheet_id);
		else :
		endif;

		$form->input(
			'년월',
			'text',
			'sheet_mm',
			$view->sheet_mm,
			array(
				'placeholder'=>'여섯자리(202012)',
				'required'=>'required'
			)
		);

		$form->input(
			'양식명',
			'text',
			'sheet_folder',
			$view->sheet_folder,
			array('required'=>'required')
		);
?>				

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
	<button type="submit" class="btn btn-primary">저장</button>
</div>
<? $form->close(); ?>

<script>
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