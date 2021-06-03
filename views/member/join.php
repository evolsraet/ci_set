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

					<? include( VIEWPATH . "admin/member/form.php"); ?>


					<!-- 회원 사진 - 업데이트 전용 -->
					<? if ( $is_update ) : ?>
						<div class="form-group">
							<label for="member_photo_wrap"
								class="control-label"
								>
								프로필 사진
							</label>
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
						</div>
					<? endif; ?>

					<? if ( !$is_update ) : ?>
						<!-- 약관 -->
							<div class="agree_box">
								<? include(FCPATH . 'personal.html'); ?>
							</div>
							<? $form->checkbox('약관동의',false,'_agree','',array('required'=>'required'), false); ?>
						<!-- // 약관 -->
						<!-- 앱 이 기기의 푸시 설정 -->
						<? if( IS_APP && $_SESSION['dv_uuid'] ) : ?>
						<?
							// kmh_print($_SESSION['dv_uuid']);
							$dv_info = $this->device_model
											->where('dv_mb_id', $this->logined->mb_id)
											->where('dv_uuid', $_SESSION['dv_uuid'])
											->get();
						?>
						<div class="row">
							<div class="col-xs-8">
								이 기기의 푸시알림
							</div>
							<div class="col-xs-4 text-right">
								<input
									type="checkbox"
									id="dv_push"
									name="dv_push"
									data-plugin="switchery"
									data-authkey="<?=$this->member_model->encode_auth($this->logined->mb_id)?>"
									data-uuid="<?=$_SESSION['dv_uuid']?>"
									<?=$dv_info->dv_push ? 'checked' : ''?>
								>
							</div>
						</div>
						<? endif; ?>
						<!-- End Of 앱 이 기기의 푸시 설정 -->						
					<? endif; ?>

					<?
						if( !$this->members->is_login() )
							reCAPTCHA();
					?>

				</div>
				<div class="footer">
					<div class="text-center">
						<? if( !$is_update ) : ?>
							<a href="/" class="btn btn-default">메인으로</a>
						<? endif; ?>
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
				mb_mobile: {
					required: true,
					rangelength: [10, 16]
				},
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

		$(btn).button('reset');
	}

	$(function(){
		$("#dv_push").change(function(event) {
			// location.href = "<?=$this->config->item('app_name')?>://mobileapp?control=push_toggle";
			// toastr.info('준비중입니다');
			btn = $(this);
			$.post('/member/app_push_toggle',
				{
					authkey: $(this).attr('data-authkey'),
					mb_app_uuid: $(this).attr('data-uuid'),
				},
				function(data, textStatus, xhr) {
				/*optional stuff to do after success */
			});
		});
	});
</script>