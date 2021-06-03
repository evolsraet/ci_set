<?
	use Phpform\Phpform;
	$form = new Phpform();

	$form_name   = 'mb_form';
	$form_action = $this->baseuri . 'insert_update_act';
	$form_config = array(
		// 'class' => 'form-horizontal',
		'ajax_before'=>'mb_form_before',
		'ajax_after'=>'mb_form_success',
		'data-send'=>'not',	// phpform
		'autocomplete'=>'off'
	);
	$form->open($form_name, $form_action, $form_config );

	// kmh_print($view->file);
?>			
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title">회원 정보</h4>
</div>
<div class="modal-body">
	<? include( VIEWPATH . "admin/member/form.php"); ?>
</div>
<div class="modal-footer">
	<? if( $view->mb_deleted_at ) : ?>
	<span class="label label-danger">삭제회원</span>
	<? endif; ?>
	<button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
	<button type="submit" class="btn btn-primary">저장</button>
</div>
<? $form->close(); ?>

<script>
	$(function(){
		// 셀렉트2
		// $("#mb_biz_id").select2({width: '100%'});

		// 앱 리셋
		// $(".app_reset").click(function(event) {
		// 	if( !confirm("사용자의 기기제한을 리셋하며, 최초 로그인한 디바이스로 설정됩니다.\n\r정말 리셋하시겠습니까?") )
		// 		return false;

		// 	var btn = $(this);
		// 	var url = '/admin/partner/member/app_reset';

		// 	$(btn).button('loading')
		// 	$.post(url, {mb_id: $(btn).attr('data-mb_id')}, function(data, textStatus, xhr) {
		// 		alert(data.msg);
		// 		$(btn).button('reset');
		// 	});
		// });

		// 삭제
		// $(".member_delete").click(function(){
		// 	if( !confirm("정말 삭제하시겠습니까?") )
		// 		return false;

		// 	var btn = $(this);
		// 	var url = '<?=$this->baseuri?>delete_act/';

		// 	$(btn).button('loading')
		// 	$.post(url, {mb_id: $(btn).attr('data-mb_id')}, function(data, textStatus, xhr) {
		// 		// console.log(data);
		// 		alert(data.msg);
		// 		$(btn).button('reset');
		// 		location.reload();
		// 	});
		// });

	});

	// (추가/수정)
		function mb_form_before(btn) {
			return true;
		}

		function mb_form_success(response, btn) {
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

	// validation
	$(function(){
		var update_mode = '?mode_admin=true';
		<? if( $view->mb_id ) : ?>
			update_mode += '&mode_mb_id=<?=$view->mb_id?>';
		<? endif; ?>

		$("#mb_form").validate({
			rules: {
				mb_email: {
					required: true,
					remote: {
						url: "/member/check/mb_email" + update_mode,
						type: "post",
						data: {
							'mb_email': function() {
								return $("[name='mb_email']").val();
							}
						},
						error: function(d) {
							console.log(d.status);
						}
					}
				}, // mb_email
				mb_mobile: {
					required: true,
					rangelength: [10, 16]
				},
				mb_display: {
					required: true,
					remote: {
						url: "/member/check/mb_display" + update_mode,
						type: "post",
						data: {
							mb_display: function() {
								return $("[name='mb_display']").val();
							}
						},
						error: function(d) {
							console.log(d.status);
						}
					}
				}, // mb_display
				mb_password: {
					<? if ( !$is_update ) : ?>
					required: true,
					<? endif; ?>
					rangelength: [6, 16]
				}, // mb_password
				chk_password: {
					<? if ( !$is_update ) : ?>
					required: true,
					<? endif; ?>
					equalTo: "[name='mb_password']"
				}, // mb_password
			}, // end rules
			messages: {
				mb_email: {
					remote: "사용할 수 없는 이메일 입니다."
				},
				mb_display: {
					remote: "사용할 수 없는 닉네임 입니다."
				}
			},
  			errorElement: "span",
  			errorClass: 'help-block',
			highlight: function(element) {
				$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
			},
			success: function(element) {
				$(element).closest('.form-group').removeClass('has-error').addClass('has-success');
			},
			submitHandler: function(form) {
				if( $(form).valid() ) 	mb_form_submit(); // by phpForm
			}
		}); // end .validation
	}); // end funtion		
</script>