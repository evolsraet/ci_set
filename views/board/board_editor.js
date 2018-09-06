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
				            	send_file(files[i], this);
				            }
				        }
					}
			  	});

				// 파일 업로드
				function send_file(file, el) {
					var form_data = new FormData();
			      	form_data.append('file', file);
			      	form_data.append('csrf_token', csrf_hash);
			      	form_data.append('file_rel_type', 'board');
			      	form_data.append('file_rel_id', $("#_post_files_code").val());
			      	$.ajax({
			        	data: form_data,
			        	type: "POST",
			        	url: CI.baseUrl + 'file/editor_ajax_upload',
			        	cache: false,
			        	contentType: false,
			        	enctype: 'multipart/form-data',
			        	processData: false,
			        	success: function(res) {
			        		console.log(res);
			          		$(el).summernote('editor.insertImage', res.imgurl);

			          		if( res.status == 'fail' )	alert(res.msg);
			        	}
			      	});
			    }

			  	// 서밋 시 에디터 코드로 변환
				$("#post_write_form").submit(function(){
					console.log( 'post_write_form CODE1' );
					$('#post_content').html( $('#post_content').summernote('code') );
					console.log( 'post_write_form CODE1 END' );
				});
			});