<?
	// 가입, 수정 텍스트 구분

	if ( $is_update ) :	// join or update
		$page_text = '정보수정';
		$btn_text = '수정';
	else :	// join or update
		$page_text = '회원가입';
		$btn_text = '가입';
	endif;	// join or update
?>

<?
	// 인풋 폼

	$form_data = array(
		(object) array(
			'id'=>'mb_email',
			'el'=>'input',
			'type'=>'email',
			'placeholder'=>'이메일',
			'value' => $view->mb_email
		),
		(object) array(
			'id'=>'mb_display',
			'el'=>'input',
			'type'=>'text',
			'placeholder'=>'닉네임',
			'value' => $view->mb_display
		),
		(object) array(
			'id'=>'mb_password',
			'el'=>'input',
			'type'=>'password',
			'placeholder'=>'비밀번호',
		),
		(object) array(
			'id'=>'chk_password',
			'el'=>'input',
			'type'=>'password',
			'placeholder'=>'비밀번호 확인',
		)
	);
?>

	<div class="row">
		<div class="col-sm-6 col-sm-offset-3">
			<?
				$attr = array();
				$attr['id'] = "join_form";
			?>
			<?=form_open('#', $attr); ?>
				<div class="login_wrap card">
					<div class="header header-full">
						<div class="header-bar">
							<h4><?=$page_text?></h4>
							<?
								$social_login = [];

								// $social_login[] = (object)array(
								// 	'link' => '#',
								// 	'title' => '페이스북',
								// 	'icon' => 'fa-facebook-official'
								// 	);
								// $social_login[] = (object)array(
								// 	'link' => '#',
								// 	'title' => '구글+',
								// 	'icon' => 'fa-google-plus-square'
								// 	);
							?>
							<? if ( $social_login ) : // 소셜로그인 ?>
								<ul class="social_login list-unstyled list-inline">
									<? foreach ( $social_login as $key => $row ) : ?>
									<li>
										<a href="<?=$row->link?>" class="btn btn-link btn-white" title="<?=$row->title?>">
											<i class="fa <?=$row->icon?>"></i>
										</a>
									</li>
									<? endforeach; ?>
								</ul>
							<? endif; // 소셜로그인 ?>
						</div>
					</div>
					<div class="body">

						<? foreach ( (array)$form_data as $key => $row ) : ?>
							<div class="form-group">
								<label for="<?=$row->id?>" class="control-label"><?=$row->placeholder?></label>
								<div>
									<input
										type="<?=$row->type?>"
										class="form-control"
										id="<?=$row->id?>"
										name="<?=$row->id?>"
										placeholder="<?=$row->placeholder?>"
										value="<?=$row->value?>"
										>
								</div>
							</div>
						<? endforeach; ?>

					</div>
					<div class="footer">

						<div class="text-center">
							<button type="submit" class="btn btn-primary"><?=$btn_text?></button>
						</div>

					</div>
				</div>
			</form>
		</div>
	</div>

<script>
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
				if( !$(form).valid() ) 	alert('err');
				else 					alert('do');
			}
		}); // end .validation

		// submit
		function form_submit(form) {
			$(form).find("[type='submit']").button('loading');

			<? if ( !$is_update ) : ?>
				var url = CI.baseUrl + 'member/join_act';
				var redirect_url = CI.baseUrl + 'member/login';
			<? else : ?>
				var url = CI.baseUrl + 'member/update_act';
				var redirect_url = CI.baseUrl + 'member/update';
			<? endif; ?>

			$.post(url, $(form).serialize(), function(res, textStatus, xhr) {
				console.log(textStatus);
				console.log(xhr);
				if( res.status == 'ok' )	{
					location.href = redirect_url;
				} else {
					console.log(res);
					swal({
						type: 'error',
						title: res.msg
					});
					$(form).find("[type='submit']").button('reset');
				}
			});
		}
	}); // end funtion

</script>