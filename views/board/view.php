<div class="board_wrapper board_view skin_<?=$board_info->board_skin?>">
	<?
		// 공용 헤더
		$path = VIEWPATH."board/header.php";
		include( $path );

		// 스킨 로드
		$path = VIEWPATH."board/{$board_info->board_skin}/view.php";
		include( $path );

		// 공용 푸터
		$path = VIEWPATH."board/footer.php";
		include( $path );
	?>
</div>