// PJAX 대응 주소이동
function pjax_href( url ) {
	if (Pjax.isSupported())
		pjax.loadUrl( url );
	else
		location.href = url;
}

/*----------  체크  ----------*/
	function is_json(str) {
	    try {
	        JSON.parse(str);
	    } catch (e) {
	        return false;
	    }
	    return true;
	}

/*----------  UI  ----------*/

	function go_element( el, time ) {
		if( typeof time == 'undefined' )	time = 400;
		$('html, body').animate( { scrollTop : $(el).offset().top }, time );
	}

	function ajax_loading() {
		return $("#kmh_ajax_loading").html();
	}

	function bs3_modal(title, body, footer) {
		$("#kmh_modal h4").html(title);
		$("#kmh_modal .modal-body").html(body);
		$("#kmh_modal .modal-footer").html(footer);
		$("#kmh_modal").modal();
	}

	function bs3_modal_close() {
		$("#kmh_modal").modal('hide');
	}


/*----------  쿠키  ----------*/

	function set_cookie(c_name,value,expiredays) {
		var exdate=new Date();
		exdate.setDate(exdate.getDate()+expiredays);
		//alert(exdate.toGMTString());
		document.cookie=c_name+ "=" +escape(value)+((expiredays==null) ? "" : ";expires="+exdate.toGMTString())+"; path=/";
	}

	function get_cookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}

	function del_cookie(name) {
		set_cookie(name,"",-1);
	}

/*----------  FILE  ----------*/

	// 파일 추가 (BS2-3 기준)
	// - 현재 처럼 직접 태그 쓰는 방식은 지양
	// - 다만, ajax 파일로드는 분량에 비해 비효율적이므로 우선 처리한다.
	// - 모듈화 진행시 버전 별로 불러오는 방향으로 수정 필요
	function add_file( file_post_name, append_div ) {
		var tag = '';
		tag += '<div class="file_add_wrap">';

		tag += '	<input type="file" name="'+file_post_name+'[]" multiple="multiple">';
		tag += '	<button type="button" onclick="remove_file(this)" class="btn btn-sm btn-default">삭제</button>';
		tag += '</div>';

		$( tag ).appendTo( append_div );
	}

	function remove_file( me ) {
		$(me).closest('.file_add_wrap').remove();
	}

	function download(id) {
		var ajax_url = '/file/download/'+id;
		kmh_hidden_frame.location.href = ajax_url;
	}

/*----------  팝업  ----------*/

	function never_popup(pu_id) {
		set_cookie('popup_'+pu_id, 'yes', 7);
		close_popup(pu_id);
	}

	function browser_popup(pu_id) {
		set_cookie('popup_'+pu_id, 'yes', 1);
		close_popup(pu_id);
	}
	function close_popup(pu_id) {
		$("#popup_"+pu_id).slideUp('fast');
	}