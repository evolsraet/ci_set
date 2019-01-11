<ul class="list-unstyled">
	<? if( count($comments) ) : ?>
		<? foreach( (array) $comments as $comment ) : ?>
		<li class="comment_item" id="comment_<?=$comment->cm_id?>">
			<div class="row">
				<!-- 들여쓰기 -->
				<? if( $comment->cm_depth ) : ?>
				<div class="col-xs-<?=$comment->cm_depth?> text-right">
					<i class="fa fa-reply fa-rotate-180 text-muted"></i>
					<!-- <img src="<?=IMG?>board/icon_reply.gif" alt="대댓글"> -->
				</div>
				<? endif; ?>
				<!-- End Of 들여쓰기 -->
				<div class="col-xs-<?=12 - $comment->cm_depth?>">
					<div class="title" data-depth="<?=$comment->cm_depth?>">
						<?=$this->files->member_image($comment->mb_id, 40)?>
						<!-- <div class="photo_wrap">
							<img src="https://picsum.photos/64/64"
								class="img-circle"
								alt="댓글 이미지"

						</div> -->
						<strong class="comment-author"><?=$comment->mb_display?></strong>
						<span class="date"><?=get_datetime($comment->cm_created_at)?></span>
						<div class="functions">
							<div class="btn-group">
								<!-- 댓글 권한 -->
								<? if( $auth->comment ) : ?>
								<button type="button"
										class="btn btn-xs btn-default"
										data-cm_id="<?=$comment->cm_id?>"
										onclick="comment_reply(this)"
										title="답변"
										>
									<i class="fa fa-reply"></i>
								</button>
								<? endif; ?>

								<!-- 업데이트 삭제 권한 -->
								<? if( 	!$comment->cm_deleted_at
										&& ( $comment->cm_mb_id == $this->logined->mb_id || is_board_admin() )
									) : ?>
								<button type="button"
										class="btn btn-xs btn-default"
										data-cm_id="<?=$comment->cm_id?>"
										onclick="comment_delete(this)"
										title="삭제"
										>
									<i class="fa fa-ban"></i>
								</button>
								<button type="button"
										class="btn btn-xs btn-default"
										data-cm_id="<?=$comment->cm_id?>"
										onclick="comment_edit(this)"
										title="수정"
										>
									<i class="fa fa-pencil"></i>
								</button>
								<? endif; ?>
								<!-- End Of 업데이트 삭제 권한 -->

							</div>
						</div>	<!-- functions -->
					</div> <!-- title -->
					<p class="desc">
						<?=$comment->cm_content?>
					</p>
				</div>
			</div>
		</li>
		<? endforeach; ?>
	<? else : ?>
		<li>
			<p class="text-center">
				등록된 댓글이 없습니다.
			</p>
		</li>
	<? endif; ?>
</ul>
