<?
	use Phpform\Phpform;
	$form = new Phpform();
?>

<section id="comments">
	<h3><i class="fa fa-comments"></i> Comments</h3>
	<div id="comment_list"></div>

	<? if( $_POST['board_btn_data']->auth->comment || $CI->comment_auth ) : ?>
	<!-- 댓글 작성 -->
		<?

			$form_action = $CI->board_base.'comment_insert/'.$CI->post_id;

			if( !empty($CI->comment_type) ) :
				$form_action .= "?comment_type={$CI->comment_type}";
			endif;

			$form_config = array(
				// 'class'=>'form-horizontal',
				'ajax_before'=>'comment_write_before',
				'ajax_after'=>'comment_write_success',
			);
			// 폼 오픈
			$form->open('comment_write_form', $form_action, $form_config );
			$form->input('','hidden', 'cm_parent', null);
			// 본문
			$form->textarea('', 3, 'cm_content', '', array('placeholder'=>"댓글을 입력하세요.") );
			$form->button('댓글 등록', 'submit',
				array(
					'class' => 'btn btn-sm btn-block btn-default'
				)
			);
			$form->close();
		?>

		<!-- 코멘트 수정 폼 -->
		<div class="comment_edit_form_wrap" style="display:none">
			<form name="comment_edit_form" class="comment_edit_form" method="post" action="">
				<input type="hidden" name="<?=$CI->security->get_csrf_token_name()?>" value="<?=$CI->security->get_csrf_hash()?>">
				<input type="hidden" name="cm_id" value="">
				<div class="form-group">
					<textarea name="cm_content" class="form-control" rows="5" placeholder="내용을 넣으세요"></textarea>
				</div>
				<div class="form-group text-center">
					<button type="submit" class="btn btn-sm btn-success">
						<i class="fa fa-edit"></i> 수정
					</button>
				</div>
			</form>
		</div>
		<!-- 코멘트 수정 폼 -->


	<!-- End Of 댓글 작성 -->
	<? endif; ?>

</section>

<script>
	function reset_comment_form() {
		// 변수 초기화
		$("#comment_write_form").attr('action', '<?=$form_action?>');
		$("#comment_write_form #cm_parent").val('');
		$("#comment_write_form button[type='submit']").html('댓글 등록');
	}

	function reload_comment() {
		$("#comment_list").html( ajax_loading() );

		var url = $('#board_base').val() + 'comment_list/<?=$CI->post_id?>';

		<? if( !empty($CI->comment_type) ) : ?>
			// 게시판 외
			url += '?comment_type=<?=$CI->comment_type?>';
		<? endif; ?>

		$.post(url, function(data, textStatus, xhr) {
			reset_comment_form();
			// 목록 갱신
			$("#comment_list").html( data );
		});
	}

	function comment_write_before() {
		if( $("#cm_content").val()=='' ) {
			alert('댓글 내용을 입력하세요.');
			$("#cm_content").focus();
			return false;
		}
		else {
			$("#comments").append( $("#comment_write_form") );
			return true;
		}
	}
	function comment_write_success( response, btn ) {
		console.log( 'comment_write_success' );
		console.log( response );
		if( response.status == undefined ) {
			console.log( response );
			alert('통신에러');
		} else if( response.status == 'ok' ) {
			$("#cm_content").val('');
			$(btn).button('reset');
			reload_comment();
			// document.getElementById('comments').scrollIntoView();
			go_element('#comments');
		} else {
			alert( response.msg );
		}
	}

	function comment_reply(btn) {
		var cm_id = $(btn).attr('data-cm_id');
		var url = $("#board_base").val() + 'comment_reply/' + cm_id;

		<? if( !empty($CI->comment_type) ) : ?>
			// 게시판 외
			url += '?comment_type=<?=$CI->comment_type?>';
		<? endif; ?>

		$("#comment_write_form #cm_parent").val(cm_id);
		var parent_who = $("#comment_"+cm_id+" .comment-author").html();
		$("#comment_write_form button[type='submit']").html(parent_who+'(님)글에 댓글 등록');

		$("#comment_"+cm_id+" .media-body").append( $("#comment_write_form") );

		$("#cm_content").focus();
	}

	function comment_delete(btn) {
		var cm_id = $(btn).attr('data-cm_id');
		var url = $("#board_base").val() + 'comment_delete/' + cm_id;

		<? if( !empty($CI->comment_type) ) : ?>
			// 게시판 외
			url += '?comment_type=<?=$CI->comment_type?>';
		<? endif; ?>

		if( !confirm("삭제된 댓글은 복구가 불가능합니다.\n정말 삭제하시겠습니까?") ) return false;

		$.post( url, function(data, textStatus, xhr) {
			console.log( data );
			if( data.status != 'ok' )	alert( data.msg );
			reload_comment();
		});
	}

	function comment_edit(btn) {
		var cm_id = $(btn).attr('data-cm_id');
		var url = $("#board_base").val() + 'comment_update/' + cm_id;

		<? if( !empty($CI->comment_type) ) : ?>
			// 게시판 외
			url += '?comment_type=<?=$CI->comment_type?>';
		<? endif; ?>

		// 원본 보관
		var original_content = $.trim( $('#comment_'+cm_id+' .desc').html().replace(/\<br\>/g, "\r") );

		var edit_form = $('.comment_edit_form_wrap').html();

		$('#comment_'+cm_id+' .desc').html( edit_form );
		$('#comment_'+cm_id+' .desc form')
			.attr('action', url )
			.attr('name','comment_edit_form_'+cm_id)
			.attr('id','comment_edit_form_'+cm_id);
		$('#comment_'+cm_id+' [name=\'cm_content\']').html( original_content );
		$('#comment_'+cm_id+' [name=\'cm_content\']').attr( 'id','cm_content'+cm_id );
		$('#comment_'+cm_id+' [name=\'cm_id\']').val( cm_id );
	}

	// 초기화
	$(function(){
		reload_comment();
	});

	// 수정 폼 전송
	$(document).on("submit", ".comment_edit_form", function(event) {
		event.preventDefault();

		$.post( $(this).attr('action'), $(this).serialize(), function(data, textStatus, xhr) {
			console.log( data );
			if( data.status != 'ok' )	alert( data.msg );
			reload_comment();
		});
	});

</script>