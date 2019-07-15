<!-- comment_list for Remark -->

<?
	$photo_size = 50;
?>
<? if( count($comments) ) : ?>
	<div class="comments">
		<? foreach( (array) $comments as $key => $comment ) : ?>
			<div class="comment media comment_item" id="comment_<?=$comment->cm_id?>"
				style="margin-left: <?=$comment->cm_depth * ($photo_size+10)?>px"
				>
				<div class="media-left">
					<?=$this->files->member_image($comment->mb_id, $photo_size)?>
				</div>
				<div class="media-body">
					<div class="comment-body">
						<strong class="comment-author"><?=$comment->mb_display?></strong>
						<div class="comment-meta">
							<span class="date"><?=get_datetime($comment->cm_created_at)?></span>
						</div>
						<div class="comment-content desc">
							<?=$comment->cm_content?>
						</div>
						<div class="comment-actions">
								<!-- 댓글 권한 -->
								<? if( $auth->comment ) : ?>
									<button type="button"
											class="btn btn-link"
											data-cm_id="<?=$comment->cm_id?>"
											onclick="comment_reply(this)"
											title="답변"
											>
										답변
									</button>
								<? endif; ?>

								<!-- 업데이트 삭제 권한 -->
								<? if( 	!$comment->cm_deleted_at
										&& ( $comment->cm_mb_id == $this->logined->mb_id || is_board_admin() )
									) : ?>

									<button type="button"
											class="btn btn-link"
											data-cm_id="<?=$comment->cm_id?>"
											onclick="comment_delete(this)"
											title="삭제"
											>
										삭제
									</button>
									<button type="button"
											class="btn btn-link"
											data-cm_id="<?=$comment->cm_id?>"
											onclick="comment_edit(this)"
											title="수정"
											>
										수정
									</button>
								<? endif; ?>
								<!-- End Of 업데이트 삭제 권한 -->
						</div>
					</div> <!-- comment-body -->
				</div> <!-- media-body -->
			</div> <!-- comment -->
		<? endforeach; ?>
	</div>
<? else : ?>
	<div>
		<p class="text-center">
			등록된 댓글이 없습니다.
		</p>
	</div>
<? endif; ?>
