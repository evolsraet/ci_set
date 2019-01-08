<?
	use Phpform\Phpform;
	$form = new Phpform();

	// 가입, 수정 텍스트 구분
	if ( $is_update ) :	// join or update
		$page_text = '정보수정';
		$btn_text = '수정';
		$form_action = base_url().'member/update_act';
		$redirect_url = base_url().'member/update';
	else :	// join or update
		$page_text = '회원가입';
		$btn_text = '가입';
		$form_action = base_url().'member/join_act';
		$redirect_url = base_url().'member/login';
	endif;	// join or update

	if( !empty($social_join->mb_social_id) ) :
		// kmh_print($social_join);
		$is_social_join = true;
		$view = $social_join;
	endif;
?>

	<div class="row">
		<div class="col-xs-12 col-md-6 col-md-offset-3">
			<?
				$form_config = array(
					// 'class'=>'form-horizontal',
					'ajax_before'=>'join_before',
					'ajax_after'=>'join_success',
					'data-send'=>'not',	// phpform
				);
				// 폼 오픈
				$form->open('join_form', $form_action, $form_config );
			?>
			<div class="login_wrap card">
				<div class="header header-full">
					<div class="header-bar">
						<h4><?=$page_text?></h4>
						<? if( !$is_update && !$is_social_join ) : ?>
							<? include(VIEWPATH.'member/module_social.php'); ?>
						<? elseif( $view->mb_social_type != 'web' ) : ?>
							<small><?=$view->mb_social_type?> 계정 로그인</small>
						<? endif; ?>
					</div>
				</div>
				<div class="body">

					<?
						// 소셜 가입 시
						if( $is_social_join ) :
							foreach( fe($social_join) as $key => $row ) :
								if( !empty($row) )
									$form->input('','hidden',$key,$row);
							endforeach;
						endif;
					?>

					<?
						$form->input('이메일','text', 'mb_email', $view->mb_email,
							array(
								'placeholder'=>'이메일'
							)
						);
						$form->input('닉네임','text', 'mb_display', $view->mb_display,
							array(
								'placeholder'=>'닉네임'
							)
						);

						// 비밀번호 설정 - 웹 회원만 (소셜은 사용안함)
						if( $view->mb_social_type == 'web' ||
							( !$is_update && !$is_social_join )
						) :
							$form->input('비밀번호','password', 'mb_password', $view->mb_password,
								array(
									'placeholder'=>'비밀번호'
								)
							);
							$form->input('비밀번호 확인','password', 'chk_password', $view->mb_password,
								array(
									'placeholder'=>'비밀번호 확인'
								)
							);
						endif;
					?>

					<!-- 회원 사진 - 업데이트 전용 -->
					<? if ( $is_update ) : ?>
						<div id="member_photo_wrap">
							<div class="member_photo">
								<?
									$member_photo = $this->file_model->member_image($view->mb_id);
								?>
								<? if( $member_photo->file_id ) : ?>
									<div class="row">
										<div class="col-sm-6 col-md-4">
											<div class="thumbnail">
												<img src="<?=$member_photo->web_path?>" alt="member image">
												<div class="caption text-center">
													<button type="button"
															onclick="ajax_file_delete(<?=$member_photo->file_id?>)"
															class="btn btn-warning"
														>
														<i class="fa fa-remove" alt="삭제"></i>
													</button>
												</div>
											</div>
										</div>
									</div>
								<? endif; ?>
							</div>
							<div class="file_add_wrap">
								<input type="file" name="_mb_photo[]" multiple="multiple">
							</div>
						</div>
					<? endif; ?>


				</div>
				<div class="footer">

					<div class="text-center">
						<button type="submit" class="btn btn-primary"><?=$btn_text?></button>
					</div>

				</div>
			</div>
			<? $form->close(); ?>
		</div>
	</div>

<script>
	// validation
	$(function(){
		$("#join_form").validate({
			rules: {
				mb_email: {
					required: true,
					remote: {
						url: "/member/check/mb_email",
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
				mb_display: {
					required: true,
					remote: {
						url: "/member/check/mb_display",
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
				if( $(form).valid() ) 	join_form_submit(); // by phpForm
			}
		}); // end .validation
	}); // end funtion

	function ajax_file_delete(id) {
		if( !confirm('정말 삭제하시겠습니까?') ) return false;

		$.post('/file/ajax_file_delete/'+id, function(response, textStatus, xhr) {
			if( response.status == undefined ) {
				console.log( response );
				alert('통신에러');
			} else if( response.status == 'ok' ) {
				$(".member_photo").remove();
			} else {
				console.log(response);
				alert( response.msg );
			}
		});
	}

	function join_before() {
		return true;
	}

	function join_success(response, btn) {
		if( response.status == undefined ) {
			console.log( response );
			alert('통신에러');
		} else if( response.status == 'ok' ) {
			location.href = '<?=$redirect_url?>';
		} else {
			alert( response.msg );
		}
	}

</script>