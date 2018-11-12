// PJAX
// https://github.com/MoOx/pjax
// pjax_meta : MY_controller, basejs, common_meta

/*

	PJAX ISSUE

	+ 컨트롤러에서 개별 css, js 불러올 경우 추가 안됨
	+ title 등, 본문 영역 외 로딩이 필요없을 경우, 서버에서 본문영역만 보낼수있다

	주의사항

	+ 광역 클래스 설정 --- 예. $(".fancybox").fancybox() ]  방식은 pjax처리된 새 본문에는 적용 안됨. 본문내부에서는 가능

	* PJAX 용 document ready
		document.addEventListener("pjax:success", function() {
*/

// selectors 태그가 원본과 다를경우 풀 리로드
// $(function () {


	if ( typeof(Pjax) != 'undefined' && Pjax.isSupported()) {
		console.log('pjax active');

		// .pjax 로 변환 요소 일괄처리할지
		// 타임아웃 테스트 (csrf 변환 기간) - csrf는 헤더에 포함됨
		// X :: 전체 jquery (예. fancybox)
		// O :: 페이지별 개별 jquery 가능여부 (.fancybox) -- 도큐먼트 레디 작동됨
		//
		// 		정리 : 불러오는 부분 내 제이쿼리는 모두 작동 / 불러오는 부분 외는 작동안됨 (영역 외부의 js)
		//
		// pace.js 는 두번 실행됨 (클래스 두번 적용)
		// X :: 페이지별 추가 css, js 파일 로드
		// O :: 페이지 갱신 후, 애널리틱스 업데이트 ---- 메뉴얼 참고

		console.log( 'PJAX TIME : ' + moment().format('YYYY-MM-DD HH:mm:ss') );

		var pjax = new Pjax({
			// debug: true,
			// currentUrlFullReload: true,
			// timeout: 50, // ajax 요청 시간제한
			elements: "a:not(.non_pjax)", // default is "a[href], form[action]"
			cacheBust: false, // 캐시 but &t=....
			selectors: [ CI.pjax_meta, 'title', '#csrf_token', '#main', '#codeigniter_profiler']
		});
	} else{
		console.log('pjax unable');
	}

// });

// PJAX 대응 주소이동
function pjax_href( url ) {
	if (Pjax.isSupported())
		pjax.loadUrl( url );
	else
		location.href = url;
}

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