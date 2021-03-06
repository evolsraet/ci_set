<pre class="hidden">
<?
	/*
	use Carbon\Carbon;

	echo PHP_EOL;
	Carbon::setLocale('ko');
	$date = new Carbon();
	// echo Carbon::now();                          // de
	// echo $date->toDateString();
	setlocale(LC_TIME, 'Korean');
	echo $date->toDateTimeString() . PHP_EOL;
	echo $date->addDays(5) . PHP_EOL;
	kmh_print( Carbon::createFromDate(1981, 01, 01)->age );
	*/

?>
</pre>
	<div class="row">
		<div class="col-xs-12 col-sm-6 col-sm-offset-3">
			<?
				$attr = array();
				$attr['id'] = "login_form";
			?>
			<?=form_open('#', $attr); ?>
				<div class="login_wrap">
					<div class="card">
						<div class="header header-full">
							<div class="header-bar">
								<h4>로그인</h4>
								<? include(VIEWPATH.'member/module_social.php'); ?>
							</div>
						</div>
						<div class="body">

							<input type="hidden"
								name="redirect"
								value="<?=$this->input->get_post('redirect') ? $this->input->get_post('redirect') : server_referer()?>"
								>
							<div class="form-group">
								<label for="mb_email" class="_col-sm-2 control-label">이메일</label>
								<div class="_col-sm-10">
									<input type="email"
										class="form-control"
										required="required"
										id="mb_email"
										name="mb_email"
										placeholder="이메일">
								</div>
							</div>
							<div class="form-group">
								<label for="mb_password" class="_col-sm-2 control-label">비밀번호</label>
								<div class="_col-sm-10">
									<input type="password"
										class="form-control"
										required="required"
										id="mb_password"
										name="mb_password"
										placeholder="비밀번호">
								</div>
							</div>
						</div>
						<div class="footer">

							<div class="text-center">
								<a href="/" class="btn btn-default non_pjax">메인으로</a>
								<a href="/member/join" class="btn btn-default non_pjax">회원가입</a>
								<button type="submit" class="btn btn-primary">로그인</button>
							</div>
							<hr>
							<div class="text-center">
								<a href="/member/find/account" class="btn btn-link">계정 찾기</a>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

	<p class="login_footer text-center">
		Copyright © <?=date('Y')?> <strong><?=$this->config->item('site_title')?></strong>. All rights reserved.
	</p>

<script>
	// form submit
	$(function(){
		$("#login_form").submit(function( event ) {
			event.preventDefault();

			var form = $(this);
			$(form).find("[type='submit']").button('loading');

			$.post('/member/login_act', $(this).serialize() )
				.done( function(res) {
					if( res.status == 'ok' )	{
						<? if( IS_APP ) : ?>
							console.log(res.app_scheme);
							location.href = res.app_scheme;
						<? else : ?>
							location.href = res.redirect;
						<? endif; ?>	
					} else {
						swal({
							type: 'error',
							title: res.msg
						});
						$(form).find("[type='submit']").button('reset');
					}
				})
				.fail( function(xml, textstatus, error) {
					console.log( error );
					swal({title: '통신 중 에러가 발생했습니다.'},
						function(){
							location.reload();
						});
				});
		});
	});
</script>