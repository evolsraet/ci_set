# KMH SET info
	- CI 318
	- v0.1

# 목표
	- 뷰는 간결해야한다
	- 퍼블리싱 요소는 이전 가능해야한다
	- 기능은 라이브러리 화 하여, 다른 프로젝트에서 쉽게 사용할수 있어야한다

# Library
	- 날짜 시간 : Carbon (패키지)	PHP 5.4+ 	use Carbon\Carbon;
	- 얼렛 : 스윗얼렛1 ( https://github.com/errakeshpd/sweetalert-1 )
# 개발 가이드

	- 공통
		- 가시성 확보
			- 들여쓰기는 정확하게, tab, space4 를 정해서 사용한다. (햔재는 tab)
			? lint 사용을 고려한다
			- 줄이 길여질 경우 속성, 요소를 줄넘김 한다.
				- 한 줄은 최대 80자를 넘지 않는다
				- html 요소의 속성이 많을 경우 속성별로 줄 넘김 및 들여쓰기를 한다
			- 반복요소는 최대한 배열 및 반복문을 사용한다 (for, foreach) => html에서 직접 반복되는 태그 복사를 하지않는다


	- PHP
		- MY_MODEL with 와 join
			- 1:1 의관계는 join (검색 조건들을 위해) - 경제적임
			- 1:n 의 관계는 with 를 사용하는 룰을 만드는것이 작업에 좋다

		? 추가 개발된 요소는 최대한 git 사용 composer로 통합가능토록 한다
			- 관리 git id 필요

		- 코딩 파일
			- 인코딩 UTF-8
			- 마지막 빈 줄을 넣는다

		- uri 체계는 최소 2레벌
		- 반복 데이터 집합은 오브젝트 활용 (DB 포함)
			- 배열 관련 기능활용시에 (array)$object 로 처리가능
				- 예 : count( (array)$object );
		- uri에 동적요소 적용시 : 각 컨트롤러에서 uri 의 요소를 정의해 사용한다 ( 메소드나 뷰에서 uri-segment 를 확인 하지 않는다. )
			예 : /member/update/userid
				컨트롤러에서 $this->data['userid'] = $this->uri->segment(3) 로 지정해 사용
				뷰나 함수에서 $this->uri->segment(3) 등을 사용하지 말아야한다
			게시판이나 페이지에서 게시글 아이디, 코멘트 아이디, 메소드, 등을 미리 지정하지 않을 경우, uri 시스템이 달라질때 대응이 불가함
		- 가시성 확보
			- 뷰 내부에서는 숏코드 사용
				- <?=?> ( <?php~ 를 사용하지 않는다 ) 가시성 확보
			- 뷰에서 조건,반복문 사용시 : 형태 대체코드 사용
				<? foreach ( $variable as $key => $row ) : ?>
				<? endforeach; ?>
			- 줄넘김
				줄넘김 (html 등 출력용) 은 PHP_EOL 로 사용
		- 로깅
			- 일시적인 로그 확인은 $this->kmh->log() 로 사용한다.
				- 디비로 등록, 디벨롭먼트 상태에서만 기록



	- JS
		- PJAX
			- 강제 리로딩이 필요한 페이지는 MY_Controller 에서 pjax 설정을 체크한다
			- 레이아웃 단에서 처리되는 광역 jquery 는 사용하지 않는다.
				예 : $(.fancybox).fancybox()
			- 각 페이지별로(pjax 로딩 되는 부분) 사용되는 jquery는 무관
			- 강제 리로딩 처리되지 않은 모든 구역은, css,js 파일을 공유한다

	- CSS
		- LESS / SCSS 활용
			최대한 LESS를 활용해 필요영역을 처리한다 => 파일을 통합하여 공유 가능하도록
			예 : .board.skinname {
					.title {}
					.desc {}
				}
