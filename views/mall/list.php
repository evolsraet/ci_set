<?
	$cate = $this->category_model->as_nav(2);
	// kmh_print($cate);
	// die();
	// $cate_sub = $this->category_model->as_nav('sub');
?>

<form id="search_product" method="get">
	<div class="row">
		<!-- category wrap -->
		<div class="col-sm-6">
			<ul class="mall_category nav nav-tabs nav-tabs-line">
				<li class="<?=is_active($_GET['category'], '')?>">
					<a href="/mall/lists">
						전체
					</a>
				</li>
				<? foreach ( (array)$cate[0] as $cate_key => $cate_row ) : ?>
					<? if ( count($cate[1][ $cate_key ]) > 1 ) : // 드롭다운 ifelse ?>
						<li class="dropdown
							<?=is_active( get_cate_id($_GET['category']), $cate_key)?>
							"
							>
							<a href="#"
								class="dropdown-toggle"
								data-toggle="dropdown"
								role="button"
								aria-haspopup="true"
								aria-expanded="false"
								>
								<?=$cate_row?> <span class="caret"></span>
							</a>
							<ul class="gnb_sub dropdown-menu">
								<? foreach ( $cate[1][$cate_key] as $sub_key => $sub_row ) : ?>
									<li class="<?=is_active(get_cate_id($_GET['category'],1), $sub_key)?>">
										<a href="?category=<?=$sub_key?>" class="real_link">
											<?=$sub_row?>
										</a>
									</li>
								<? endforeach; ?>
							</ul>
						</li>
					<? else : // 드롭다운 ifelse ?>
						<li class="<?=is_active(get_cate_id($_GET['category']), $cate_key)?>">
							<a href="?category=<?=array_first($cate[1][$cate_key],'key')?>" class="real_link">
								<?=$cate_row?>
							</a>
						</li>
					<? endif; // 드롭다운 ifelse ?>
				<? endforeach; ?>
			</ul>
		</div>
		<!-- // category wrap -->
		<!-- 검색 wrap -->
		<div class="col-sm-3">
			<?
				$order_array = array(
					'_null' => '등록일순',
					'pd_price' => '가격순',
					'pd_order' => '인기순',
				);
			?>

			<div class="projects-sort">
				<span class="projects-sort-label">Sorted by : </span>
				<div class="inline-block dropdown">
					<span class="dropdown-toggle" id="projects-menu" data-toggle="dropdown" aria-expanded="false" role="button">
					<?=$order_array[$order]?>
					<i class="icon wb-chevron-down-mini" aria-hidden="true"></i>
					</span>
					<?=$this->kmh->set_array($order_array)->as_ul(
								'order',
								$order,
								$link='?order=',
								'dropdown-menu animation-scale-up animation-top-left animation-duration-250',
								$default_text = null
					)?>

					<script>
						$(function() {
							$(".projects-sort ul a").click(function(event) {
								event.preventDefault();
								$("#search_product [name='order']").val( $(this).attr('data-value') );
								$("#search_product").submit();
							});
						});
					</script>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<input type="hidden" name="category" value="<?=$_GET['category']?>">
			<input type="hidden" name="order" value="<?=$order?>">
			<div class="form-group">
				<div class="input-search">
					<button type="submit" class="input-search-btn"><i class="icon wb-search" aria-hidden="true"></i></button>
					<input type="text" class="form-control" name="search_text" placeholder="Search..." value="<?=$_GET['search_text']?>">
				</div>
			</div>
		</div>
		<!-- // 검색 wrap -->
	</div>

	<!-- 소분류 있을 경우 -->
	<? if( $cate[2][get_cate_id($_GET['category'],1)] ) : ?>
		<div class="row">
			<div class="col-xs-12 margin-bottom-30">
				<ul class="nav nav-pills">
					<li class="<?=is_active(category_depth($_GET['category']), 1)?>">
						<a href="?category=<?=get_cate_id($_GET['category'],1)?>">
							전체
						</a>
					</li>
					<? foreach( (array) $cate[2][get_cate_id($_GET['category'],1)] as $key => $row ) : ?>
						<li class="<?=is_active($_GET['category'], $key)?>">
							<a href="?category=<?=$key?>">
								<?=$row?>
							</a>
						</li>
					<? endforeach; ?>
				</ul>
			</div>
		</div>
	<? endif; ?>
	<!-- End Of 소분류 있을 경우 -->
</form>

<div id="product_list" class="projects-wrap">
	<!-- ifelse -->
	<? if( $total ) : ?>
		<ul class="blocks blocks-100 blocks-xlg-5 blocks-md-4 blocks-sm-3 blocks-xs-2"
				data-plugin="animateList" data-child=">li">
		<? foreach( (array) $list as $key => $item ) : ?>
			<li>
				<div class="panel">
					<figure class="overlay overlay-hover animation-hover">
						<img class="제품 이미지" src="<?=$this->files->front_image('product', $item->pd_id, 'pd_img', 500, 400)?>">
						<figcaption class="overlay-panel overlay-background overlay-fade text-center vertical-align">
							<div class="btn-group">
								<a href="/mall/view/<?=$item->pd_id?>"
									target="_blank"
									class="btn btn-icon btn-pure btn-default"
									title="새창으로"
									data-toggle="tooltip"
									data-placement="left"
									>
									<i class="fa fa-external-link"></i>
								</a>
							</div>
							<a href="/mall/view/<?=$item->pd_id?>" class="btn btn-outline btn-default project-button">제품보기</a>
						</figcaption>
					</figure>
					<h4 class="text-truncate" data-toggle="tooltip" title="<?=$item->pd_name?>">
						<a href="/mall/view/<?=$item->pd_id?>">
							<?=$item->pd_name?>
						</a>
					</h4>
					<div class="pull-left">
						<!-- 최소수량 -->
						<? if( $item->pd_min ) : ?>
							<span  data-toggle="tooltip" title="최소주문수량">
								<i class="fa fa-chevron-circle-down"></i> <?=$item->pd_min?>개부터
							</span>
						<? else : ?>
							&nbsp;
						<? endif; ?>
						<!-- End Of 최소수량 -->
					</div>
					<div class="price text-right"><?=number_format($item->pd_price)?> 원</div>
				</div>
			</li>
		<? endforeach; ?>
		</ul>
	<? else : ?>
		<div class="alert alert-info text-center">등록된 제품이 없습니다.</div>
	<? endif; ?>
	<!-- End Of ifelse -->
</div>

<div class="text-center">
	<?=$pagination?>
</div>