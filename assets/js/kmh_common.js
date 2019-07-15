// 전체 적용
$(document).ready(function() {
	// SELECT2 - Remark 사용시 data-plugin='select2'
	$(".select2_plugin").select2({
		width: '100%'
	});
});

$(function(){
	$(".foldable").click(function(event) {
		$(this).next('section').toggle();
		$(this).find('i').toggleClass('fa-angle-up').toggleClass('fa-angle-down');
	});
});

// PJAX 대응 주소이동
function pjax_href( url ) {
	if (typeof(Pjax) != 'undefined' && typeof(Pjax.isSupported) != 'undefined')
		pjax.loadUrl( url );
	else
		location.href = url;
}

// 에디터 파일 업로드
function send_file(file_rel_type, file, el) {
	var form_data = new FormData();
  	form_data.append('file', file);
  	form_data.append('csrf_token', csrf_hash);
  	form_data.append('file_rel_type', file_rel_type);
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

/*----------  데이터  ----------*/
	function add_comma(num) {
		var regexp = /\B(?=(\d{3})+(?!\d))/g;
		return num.toString().replace(regexp, ',');
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

// 폼체크
	function chkForm(f_name) {
		var i,currEl;
		f = document.getElementsByName(f_name);
		f= f[0];
		var errMsg = "필수 입력항목 입니다.";
		for(i = 0; i < f.elements.length; i++){
			currEl = f.elements[i];
			//필수 항목을 체크한다.
			if (currEl.getAttribute('required') == 'required' ) {
				// console.log( currEl );
				if(currEl.type == "TEXT" || currEl.type == "text" ||
					 currEl.tagName == "SELECT" || currEl.tagName == "select" ||
					 currEl.tagName == "TEXTAREA" || currEl.tagName == "textarea"){
					if(!chkText(currEl,errMsg)) return false;
				} else if(currEl.type == "PASSWORD" || currEl.type == "password"){
					if(!chkText(currEl,errMsg)) return false;
				} else if(currEl.type == "CHECKBOX" || currEl.type == "checkbox"){
					if(!chkCheckbox(f, currEl,errMsg)) return false;
				} else if(currEl.type == "RADIO" || currEl.type == "radio"){
					if(!chkRadio(f, currEl,errMsg)) return false;
				}

			}

			// 입력 페턴을 체크한다.
			if(currEl.getAttribute("option") != null && currEl.value.length > 0){
				console.log( currEl );
				if(!chkPatten(currEl,currEl.option,errMsg)) return false;
			}
		}

		return true;
	}


	function chkPatten(field,patten,name) {
		var regNum =/^[0-9]+$/;
		var regPhone =/^[0-9]{2,3}-[0-9]{3,4}-[0-9]{4}$/;
		var regMail =/^[_a-zA-Z0-9-]+@[._a-zA-Z0-9-]+\.[a-zA-Z]+$/;
		var regDomain =/^[.a-zA-Z0-9-]+.[a-zA-Z]+$/;
		var regAlpha =/^[a-zA-Z]+$/;
		var regHost =/^[a-zA-Z-]+$/;
		var regHangul =/[가-힣]/;
		var regHangulEng =/[가-힣a-zA-Z]/;
		var regHangulOnly =/^[가-힣]*$/;
		var regId = /^[a-zA-Z]{1}[a-zA-Z0-9_-]{4,15}$/;
		var regDate =/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/;
		patten = eval(patten);
		if(!patten.test(field.value)){
			alert(name + "\n\n항목의 형식이 올바르지 않습니다.");
			field.focus();
			return false;
		}
		return true;
	}

	function chkText(field, name) {
		if(field.value.length < 1){
			alert(name);
			field.focus();
			console.log('chkText');
			return false;
		}
		return true;
	}

	function chkCheckbox(form, field, name) {
		fieldname = eval(form.name+'.'+field.name);
		if (!fieldname.checked){
			alert(name);
			field.focus();
			console.log('chkCheckbox');
			return false;
		}
		return true;
	}

	function chkRadio(form, field, name) {
		fieldname = eval(form.name+'.'+field.name);
		for (i=0;i<fieldname.length;i++) {
			if (fieldname[i].checked)
				return true;
		}
		alert(name);
		field.focus();
		console.log('chkRadio');
		return false;
	}