<?
	// 버튼 헬퍼 데이터
	// 컨트롤러의 모든 처리가 끝난 뒤에 정의 필요
	$_POST['board_btn_data'] = new stdClass;
	$_POST['board_btn_data']->auth       = $auth;
	$_POST['board_btn_data']->board_base = $board_base;;
	$_POST['board_btn_data']->post_id    = $this->post_id;;
?>

<input type="hidden" id="board_base" value="<?=$board_base?>">
