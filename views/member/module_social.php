								<?
									$hybridauth_config = $this->config->item('hybridauth');

									$social_login = [];

									foreach( (array) $hybridauth_config['providers'] as $key => $row ) :
										if( $row['enabled'] != true ) continue;

										switch ($key) {
											case 'Facebook':
												$social_login[] = (object)array(
													'link' => '/member/social_login/Facebook',
													'title' => '페이스북',
													'icon' => 'fa-fw fa-facebook-official'
												);
												break;
											case 'Google':
												$social_login[] = (object)array(
													'link' => '/member/social_login/Google',
													'title' => '구글+',
													'icon' => 'fa-fw fa-google-plus-square'
													);
												break;
											case 'Kakao':
												$social_login[] = (object)array(
													'link' => '/member/social_login/Kakao',
													'title' => '카카오톡',
													'image' => 'https://developers.kakao.com/assets/img/about/logos/kakaolink/kakaolink_btn_medium.png'
													);
												break;
											case 'Twitter':
												$social_login[] = (object)array(
													'link' => '/member/social_login/Twitter',
													'title' => '트위터',
													'icon' => 'fa-fw fa-twitter-square'
													);
												break;
											default:
												# code...
												break;
										}
									endforeach;

								?>
								<? if ( $social_login ) : // 소셜로그인 ?>
								<ul class="social_login list-unstyled list-inline">
									<? foreach ( $social_login as $key => $row ) : ?>
									<li>
										<a href="<?=$row->link?>" class="non_pjax btn btn-link btn-white" title="<?=$row->title?>">
											<!-- 아이콘 or 이미지 -->
											<? if( $row->icon ) : ?>
												<i class="fa <?=$row->icon?>"></i>
											<? else : ?>
												<img src="<?=$row->image?>" alt="<?=$row->title?>"
													width="33"
													>
											<? endif; ?>
											<!-- End Of 아이콘 or 이미지 -->
										</a>
									</li>
									<? endforeach; ?>
								</ul>
								<? endif; // 소셜로그인 ?>