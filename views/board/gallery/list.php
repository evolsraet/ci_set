<?
	// use Carbon\Carbon;
	// Carbon::setLocale('ko');
	// kmh_print( $list );
	$test_image = array();
?>

<section class="board_search_warp">
	<div class="row">
		<div class="col-md-6 col-sm-12">
			<!-- 카테고리 -->
			<?=$this->kmh
				->set_array( get_category($board_info) )
				->as_ul(
					$id='category',
					$set=$this->input->get('category'),
					$link='?category=',
					$class="nav nav-tabs nav-tabs-line"
				);
			?>
		</div>
		<div class="col-md-6 col-sm-12 text-right">
			<!-- 검색 -->
			<? module_search(); ?>
		</div>
	</div>
</section>


<section class="list_table">
	<div class="row">
		<? if( !empty( $list ) ) : // $list ?>
			<? foreach ( $list as $key => $row ) : ?>

				<div class="col-md-3 col-sm-4 col-xs-6">
						<div class="gallery_item">
							<a href="">
							<div class="photo_wrap">
								<a <?=view_link($row,'link',$board_base)?>>
									<img src="<?=$this->files->post_image( $row->post_id, 500, 400 )?>" alt="image">
								</a>
								<? $test_image[] = $this->files->last_image; ?>
								<div class="overlay_wrap">
									<div class="buttons">
										<a href="<?=$this->files->last_image?>"
											class="_btn _btn-outline gallery_images non_pjax"
											title="<?=strip_tags(view_link($row, 50))?>"
											>
											<i class="fa fa-search"></i>
										</a>
										<a <?=view_link($row,'link',$board_base)?> class="gallery_link _btn _btn-outline">
											<i class="fa fa-link"></i>
										</a>
									</div>
								</div>
							</div>
							</a>
							<div class="desc">
								<p class="title">
									<?=notice_or_no($row, null)?>
									<a <?=view_link($row,'link',$board_base)?> >
										<?=view_link($row)?>
									</a>
								</p>
								<div class="row meta">
									<div class="col-sm-6 col-xs-12 text-left">
										<p class="writer"><?=writer_display($row)?></p>
									</div>
									<div class="col-sm-6 col-xs-12 text-right">
										<p class="date"><?=get_date( $row->post_created_at )?></p>
									</div>
								</div>
							</div>
						</div>
				</div>
			<? endforeach; ?>
		<? else : 	// $list ?>
				<div class="col-md-12">
					<p class="text-center">
						데이터가 없습니다.
					</p>
				</div>
		<? endif;?>
	</div>
</section>

<!-- 페이징 -->
<?=$pagination?>

<!-- 버튼 -->
<div class="button_wrap">
	<?=board_btn('write')?>
</div>