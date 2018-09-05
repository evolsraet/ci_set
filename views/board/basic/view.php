<!-- 정보 -->
<section class="info">
	<h3 class="title"><?=$view->post_title?></h3>
	<ul class="list-unstyled list-inline">
		<li>
			<strong><i class="fa fa-user-circle-o"></i> 작성자</strong>
			<?=writer_display($view)?>
		</li>
		<li>
			<strong><i class="fa fa-calendar-check-o"></i> 작성일</strong>
			<?=get_datetime($view->post_created_at)?>
		</li>
		<li>
			<strong><i class="fa fa-check-circle-o"></i> 조회수</strong>
			<?=number_format($view->post_hit)?>
		</li>
		<!-- 카테고리 -->
		<? if( $view->post_category ) : ?>
		<li>
			<strong><i class="fa fa-bars"></i> 카테고리</strong>
			<?=get_category($board_info, $view->post_category)?>
		</li>
		<? endif; ?>
		<!-- End Of 카테고리 -->
	</ul>
</section>
<hr>


<!-- 컨텐츠 -->
<article class="board_content">
	<?=$view->post_content?>
</article>

<hr>

<!-- 파일 -->
<section class="board_file">
	<ul class="list-unstyled">
	<? foreach( fe($view->file) as $key => $row ) : ?>
		<li>
			<a href="javascript:download(<?=$row->file_id?>)">
				<i class="<?=$this->files->get_file_type($row)?>"></i>
				<?=$row->file_name?>
			</a>
		</li>
	<? endforeach; ?>
	</ul>
</section>

<!-- 댓글 -->
<? module_comment(); ?>

<!-- 버튼 -->
<div class="button_wrap">
	<?=board_btn('list')?>
	<?=board_btn('reply')?>
	<?=board_btn('update')?>
	<?=board_btn('delete')?>
</div>
