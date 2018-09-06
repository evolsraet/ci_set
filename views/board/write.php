<div class="
		board_wrapper
		board_write
		skin_<?=$board_info->board_skin?>
		<?=$board_info->board_use_editor?"use_editor":""?>
	"
	>
	<?
		// 공용 헤더
		$path = VIEWPATH."board/header.php";
		include( $path );

		// 스킨 로드
		$path = VIEWPATH."board/{$board_info->board_skin}/write.php";
		include( $path );

		// 공용 푸터
		$path = VIEWPATH."board/footer.php";
		include( $path );
	?>
</div>
