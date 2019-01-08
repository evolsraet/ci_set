<?
	// !! 반응형으로 수정

	// use Carbon\Carbon;
	// Carbon::setLocale('ko');
	// kmh_print( $list );
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
					$link='?category='
				);
			?>
		</div>
		<div class="col-md-6 col-sm-12 text-right">
			<!-- 검색 -->
			<? module_search(); ?>
		</div>
	</div>
</section>


<table class="table table-hover list_table">
	<colgroup>
		<col>
		<col width="40%">
		<col>
		<col>
		<col>
	</colgroup>
	<thead class="hidden-xs">
		<tr>
			<th>번호</th>
			<th>제목</th>
			<th>작성자</th>
			<th>작성일</th>
			<th>조회</th>
		</tr>
	</thead>
	<tbody>
		<? if( !empty( $list ) ) : // $list ?>
			<? foreach ( $list as $key => $row ) : ?>
				<tr>
					<!-- 번호 -->
					<td class="no text-center hidden-xs">
						<?=notice_or_no($row, $start_no + $key)?>
					</td>
					<!-- 제목 -->
					<td class="title <?=is_notice($row)?"notice":""?>">
						<span class="visible-xs">
							<?=notice_or_no($row, null)?>
						</span>

						<a <?=view_link($row,'link',$board_base)?>
							class="text_cut"
							>
								<?=view_link($row, 50)?>
								<? if( $row->cm_cnt ) : ?>
									<span class="text-muted">(<?=$row->cm_cnt?>)</span>
								<? endif; ?>
						</a>
						<p class="visible-xs">
							<i class="fa fa-user-circle-o"></i> <?=writer_display($row)?>
							&nbsp;&nbsp;
							<i class="fa fa-calendar-check-o"></i> <?=get_date( $row->post_created_at )?>
						</p>
					</td>
					<!-- 작성자 -->
					<td class="text-center hidden-xs">
						<?=writer_display($row)?>
					</td>
					<!-- 작성일 -->
					<td class="text-center hidden-xs">
						<?=get_date( $row->post_created_at )?>
						<? //=Carbon::parse( $row->post_created_at )->toDateString()?>
						<? //=Carbon::parse( $row->post_created_at )->diffForHumans()?>
					</td>
					<!-- 조회 -->
					<td class="text-center hidden-xs">
						<?=number_format( $row->post_hit )?>
					</td>
				</tr>
			<? endforeach; ?>
		<? else : 	// $list ?>
			<tr>
				<td colspan="100" class="text-center">
					데이터가 없습니다.
				</td>
			</tr>
		<? endif; 	// $list ?>
	</tbody>
</table>

<!-- 페이징 -->
<?=$pagination?>

<!-- 버튼 -->
<div class="button_wrap">
	<?=board_btn('write')?>
</div>
