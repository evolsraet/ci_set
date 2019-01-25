<?
	use Phpform\Phpform;
	$form = new Phpform();
?>

	<div class="row">
		<div class="col-xs-12 col-md-6 col-md-offset-3">
			<?
				$form_config = array(
					'ajax_before'=>'find_before',
					'ajax_after'=>'find_success',
				);
				// 폼 오픈
				$form->open('find_form', $form_action, $form_config );
			?>
			<div class="login_wrap card">
				<div class="header header-full">
					<div class="header-bar">
						<h4><?=page_title($nav_sub)?></h4>
					</div>
				</div>
				<div class="body">

					<?
						$form->input('이메일','text', 'mb_email', '',
							array(
								'placeholder'=>'이메일'
							)
						);
					?>

				</div>
				<div class="footer">

					<div class="text-center">
						<button type="submit" class="btn btn-primary">검색</button>
					</div>

				</div>
			</div>
			<? $form->close(); ?>
		</div>
	</div>

<script>
	function find_before() {

	}

	function find_success(response) {
		console.log( response );
		if( response.data==null ) {
			swal({
				type: 'error',
				title: '일치하는 계정이 없습니다.'
			});
		} else {
			mb_email = response.data.<?=$this->auth_field?>;
			bs3_modal('계정찾기 메일발송',
				'<p class="text-center">' + mb_email + ' 메일로 비밀번호를 초기화 하시겠습니까?</p>',
				'<button type="button" class="reset_passsword btn btn-danger">메일발송</button>'+
				'<button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>'
				);
		}
	}

	$(document).on('click', '.reset_passsword', function(){
		url = '/member/reset_passsword';

		$.ajax({
			url: url,
			type: 'post',
			dataType: 'json',
			data: { '<?=$this->security->get_csrf_token_name()?>' : '<?=$this->security->get_csrf_hash()?>' },
			error : function(request ,status, error) {
				alert('AJAX 통신 중 에러가 발생했습니다.');
				console.log( request.responseText );
			},
			success : function(response, status, request) {
				if( response.status == 'ok' ) {
					swal({
						type: 'success',
						title: '메일이 발송되었습니다.',
						text: response.reset_email + ' 메일을 확인해주세요.',
					}, function(){
						location.href='/';
					});
				} else {
					swal({
						type: 'error',
						title: '에러가 발생했습니다.',
						text: response.msg
					}, function(){
						$('.btn').button('reset');
					});
				}
			}
		});
	});


</script>