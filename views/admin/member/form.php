<?
	$is_update = ($view->mb_id) ? true : false;
?>

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
						// UPDATE
						if( $is_update ) :
							$form->input('아이디','hidden', 'mb_id', $view->mb_id);
							$form->input('이메일','hidden', 'mb_email', $view->mb_email);
						else :
							$form->input('이메일','email', 'mb_email', $view->mb_email,
								array(
									'required' => 'required',
									'placeholder'=>'이메일',
									'without_label'=>true
								)
							);
						endif;
						// End of UPDATE

						$form->input('닉네임','text', 'mb_display', $view->mb_display,
							array(
								'required' => 'required',
								'placeholder'=>'닉네임',
								'without_label'=>true
							)
						);

						// 비밀번호 설정 - 웹 회원만 (소셜은 사용안함)
						if( $view->mb_social_type == 'web'
							OR
							( !$is_update && !$is_social_join )
						) :
							$password_option = array();
							$password_option['placeholder'] = '비밀번호';
							$password_option['without_label'] = true;
							if( !$is_update )
								$password_option['required'] = 'required';

							$form->input('비밀번호','password', 'mb_password', null, $password_option);

							$password_option['placeholder'] = '비밀번호 확인';
							$form->input('비밀번호 확인','password', 'chk_password', null, $password_option);
						endif;

					?>

					<? if( 0 ) : ?>
					<div class="form-group">
						<label for="mb_post"
							class="control-label"
							>
							주소
						</label>
						<div class="">
							<div class="form-inline margin-bottom-5">
								<div class="input-group">
									<? $form->input('우편번호','text','mb_post',$view->mb_post,null,false); ?>
										<span class="input-group-btn">
											<button
												type="button"
												class="btn btn-info open_member_postcode"
												>
												&nbsp;<i class="fa fa-map-pin"></i>&nbsp;
											</button>
										</span> <!-- btn -->
								</div> <!-- inputgroup -->
							</div>	<!-- form-inline -->

							<!-- 다음 우편번호 -->
								<?
									$postcode_id = 'member_postcode';
									$postcode_post = 'mb_post';
									$postcode_addr1 = 'mb_addr1';
									$postcode_addr2 = 'mb_addr2';
									include(MODULEPATH . 'daum_postcode.php');
								?>
							<!-- // 다음 우편번호 -->

							<? $addr_attr = array('class'=>'margin-bottom-5');?>
							<? $form->input('기본주소','text','mb_addr1',$view->mb_addr1,$addr_attr,false); ?>
							<? $form->input('상세주소','text','mb_addr2',$view->mb_addr2,$addr_attr,false); ?>
						</div> <!-- col -->
					</div> <!-- form-group -->
					<? endif; ?>					
