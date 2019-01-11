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
$(function () {
	// if ( typeof(Pjax) != 'undefined' && Pjax.isSupported()) {
	if ( typeof(Pjax) != 'undefined' && typeof(Pjax.isSupported) != 'undefined' ) {
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

		console.log( 'PJAX SET TIME : ' + moment().format('YYYY-MM-DD HH:mm:ss') );

		var pjax = new Pjax({
			// debug: true,
			// currentUrlFullReload: true,
			// timeout: 50, // ajax 요청 시간제한
			elements: "a:not(.non_pjax)", // default is "a[href], form[action]"
			cacheBust: false, // 캐시 but &t=....
			selectors: [ CI.pjax_meta, '#pjax_body_class', 'title', '#csrf_token', '#main', '#codeigniter_profiler']
		});

		document.addEventListener('pjax:complete', function(e){
			console.log('pjax:complete : ' + e.request.responseURL);	// IE NOT
			// 바디 클래스 적용
			var pjax_body_class = $("#pjax_body_class").attr('content');
			$('body').attr('class', pjax_body_class);
		});

	} else{
		console.log('pjax unable');
	}
});