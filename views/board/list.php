<div class="board_wrapper board_list skin_<?=$board_info->board_skin?>">

	<?
        // 공용 헤더
        $path = VIEWPATH."board/header.php";
        include( $path );

        // 스킨 로드
        $path = VIEWPATH."board/{$board_info->board_skin}/list.php";
		include( $path );

        // 공용 푸터
        $path = VIEWPATH."board/footer.php";
        include( $path );
	?>
</div>

<div id="check_password" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">비밀번호 확인</h4>
            </div>
            <div class="modal-body">
                <div class="form">
                    <input type="hidden" id="post_id" value="">
                    <div class="form-group">
                        <input type="password" class="form-control" id="password" placeholder="password">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
                <button type="button" class="btn btn-primary">확인</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->