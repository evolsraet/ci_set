			// 에디터 코드
			// 컨트롤러에서 에디터 라이브러리 로드 후
			$(function(){
				// 에디터 초기화
			  	$('#post_content').summernote({
			  		lang: 'ko-KR',
			  		height: 300,
					callbacks: {
						onImageUpload: function(files, editor, welEditable) {
				            for (var i = files.length - 1; i >= 0; i--) {
				            	send_file('board', files[i], this);
				            }
				        }
					}
			  	});

			  	// 서밋 시 에디터 코드로 변환
				$("#post_write_form").submit(function(){
					console.log( 'post_write_form CODE1' );
					$('#post_content').html( $('#post_content').summernote('code') );
					console.log( 'post_write_form CODE1 END' );
				});
			});