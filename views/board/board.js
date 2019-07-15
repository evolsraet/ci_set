/*============================
=            LIST            =
============================*/
	// 보드 전용 모달
	function board_modal(modal_id, post_id) {
		console.log( 'board_modal' );
		$('#'+modal_id ).modal('toggle');
		$('#'+modal_id+' #post_id').val(post_id);
	}

	// 비밀번호 확인
	function check_password(post_id, link) {
		if( 'undefined' == typeof link ) link = 'view';

		swal({
		    title: "비밀번호 확인",
		    type: 'input',
		    showCancelButton: true,
		    closeOnConfirm: false,
		    animation: "slide-from-top"
		}, function(inputValue) {
			var url = $("#board_base").val() + '/check_password/' + post_id;
			$.post(url, { password: inputValue })
				.done(function(res){
					if( res.status == 'ok' ) {
						swal.close();
						if( link == 'delete' )
							delete_post( post_id );
						else
							pjax_href( $("#board_base").val() + link + '/' + post_id );
					} else {
						swal( res.msg );
					}
				});
		});
	}

	// 삭제
	function delete_post( post_id ) {
		if( confirm("정말 삭제하시겠습니까?\n자료는 복구될 수 없습니다.") ) {
			url = $("#board_base").val() + 'delete/' + post_id;
			location.href = url;
		}
	}

/*=====  End of LIST  ======*/


/*=============================
=            WRITE            =
=============================*/


/*=====  End of WRITE  ======*/

