<!-- basejs -->
<script>
	var CI = (function(){
		var _baseUrl = "<?=base_url()?>";
		return {
			"language": "<?=$this->config->item('language')?>",
			"baseUrl": _baseUrl,
			"pjax_meta": "#<?=$this->pjax_meta?>",
			"uri_segment_1":"<?=$uri_segment_1?>",
			"uri_segment_2":"<?=$uri_segment_2?>"
		};
	})();

	// Jquery Ajax Setup
	// JQUERY로 AJAX 처리시, 별도 데이터가 없으면 기본 CSRF를 추가한다
	if (window.jQuery) {
		// var csrf_token = "<?=$this->security->get_csrf_token_name()?>";
		var csrf_hash = "<?=$this->security->get_csrf_hash()?>";
	    $.ajaxSetup({
	        data: {
	        	csrf_token: csrf_hash
	        },
	        error: function(x,e) {
	        	var msg = '';

				if(x.status==0){
					msg = 'You are offline!!n Please Check Your Network.';
				}else if(x.status==404){
					msg = 'Requested URL not found.';
				}else if(x.status==500){
					msg = 'Internel Server Error.';
				}else if(e=='parsererror'){
					msg = 'Error.nParsing JSON Request failed.';
				}else if(e=='timeout'){
					msg = 'Request Time out.';
				}else {
					msg = '통신에러가 발생했습니다. 새로고침 후 다시 시작해주세요.';
					console.log( x.responseText );
				}

				toastr.error( msg );
			}	        
	    });
	} else {
		console.log( 'Jquery Not Loaded' );
	}
</script>
<!-- basejs -->
