<?
	if( !$search_array ) :
		$search_array = array(
			'post_title' => '제목',
			'post_content' => '내용',
			'_writer' => '작성자',
			'' => '제목+내용'
		);
	endif;
?>

<div class="board_search">
	<form action="" class="form-inline" method="get">
		<?

		?>
		<div class="form-group _col-xs-2">
			<select name="stx" id="stx" class="form-control">
				<? foreach ( (array)$search_array as $key => $row ) : ?>
					<option value="<?=$key?>" <?=$key==$CI->input->get('stx')?"selected":""?>><?=$row?></option>
				<? endforeach; ?>
			</select>
		</div>

		<div class="form-group _col-xs-5">
			<input type="text" name="skey" id="skey" class="form-control"
				placeholder="검색어"
				value="<?=$CI->input->get('skey')?>"
				>
		</div>
		<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
	</form>
</div>