<?php
	// 모바일용

	// session_start();
	/*
	 * [결제 인증요청 페이지(STEP2-1)]
	 *
	 * 샘플페이지에서는 기본 파라미터만 예시되어 있으며, 별도로 필요하신 파라미터는 연동메뉴얼을 참고하시어 추가 하시기 바랍니다.     
	 */

	/*
	 * 1. 기본결제 인증요청 정보 변경
	 * 
	 * 기본정보를 변경하여 주시기 바랍니다.(파라미터 전달시 POST를 사용하세요)
	 */


	if( $_POST['CST_MID'] ) : // sample 로 진입시
	    $CST_PLATFORM               = $_POST["CST_PLATFORM"];
	    $CST_MID                    = $_POST["CST_MID"];   
	else :
		$CST_PLATFORM               = $this->testmode ? "test":"service";            //LG유플러스 결제 서비스 선택(test:테스트, service:서비스)
		$CST_MID                    = $this->cst_mid;              				     //상점아이디(LG유플러스으로 부터 발급받으신 상점아이디를 입력하세요)
																 					 //테스트 아이디는 't'를 반드시 제외하고 입력하세요.
	endif;

	$LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;  //상점아이디(자동생성)
	$LGD_OID                    = $_POST["LGD_OID"];					//주문번호(상점정의 유니크한 주문번호를 입력하세요)
	$LGD_AMOUNT                 = $_POST["LGD_AMOUNT"];					//결제금액("," 를 제외한 결제금액을 입력하세요)
	$LGD_BUYER                  = $_POST["LGD_BUYER"];					//구매자명
	$LGD_PRODUCTINFO            = $_POST["LGD_PRODUCTINFO"];			//상품명
	$LGD_BUYEREMAIL             = $_POST["LGD_BUYEREMAIL"];				//구매자 이메일
	$LGD_CUSTOM_FIRSTPAY        = $_POST["LGD_CUSTOM_USABLEPAY"];		//상점정의 초기결제수단
	$LGD_PCVIEWYN				= "Y";				//휴대폰번호 입력 화면 사용 여부(유심칩이 없는 단말기에서 입력-->유심칩이 있는 휴대폰에서 실제 결제)
	$LGD_CUSTOM_SKIN            = "SMART_XPAY2";                        //상점정의 결제창 스킨
	
	$configPath 				= $this->lgdacom_path; 						//LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정. 	    
	/*
	 * 가상계좌(무통장) 결제 연동을 하시는 경우 아래 LGD_CASNOTEURL 을 설정하여 주시기 바랍니다. 
	 */    
	$LGD_CASNOTEURL				= base_url("/lgxpay/cas_noteurl");

	/*
	 * LGD_RETURNURL 을 설정하여 주시기 바랍니다. 반드시 현재 페이지와 동일한 프로트콜 및  호스트이어야 합니다. 아래 부분을 반드시 수정하십시요.
	 */    
	$LGD_RETURNURL				= base_url("/lgxpay/returnurl");
	
	/*
	* ISP 카드결제 연동을 위한 파라미터(필수)
	*/

	$is_app_scheme = (IS_APP && $os = 'ios')
						? $this->config->item('app_name') . "://"
						: "";

	$LGD_KVPMISPWAPURL		= $is_app_scheme;
	$LGD_KVPMISPCANCELURL   = $is_app_scheme;
	
	$LGD_MPILOTTEAPPCARDWAPURL = $is_app_scheme; //iOS 연동시 필수
	   
	/*
	* 계좌이체 연동을 위한 파라미터(필수)
	*/
	$LGD_MTRANSFERWAPURL 		= $is_app_scheme;
	$LGD_MTRANSFERCANCELURL 	= $is_app_scheme;   


	/*----------  기기 변수  ----------*/
	if( $this->agent->is_mobile('iphone') || $this->agent->is_mobile('ipad') || $this->agent->is_mobile('ipod') ) :
		$os = 'ios';
	else :
		$os = 'android';
	endif;

	/*
	if( IS_APP && $os = 'ios' ) :
		// iOS
		$LGD_KVPMISPNOTEURL       	= $this->config->item('app_name') . "://";
		$LGD_KVPMISPWAPURL			= $this->config->item('app_name') . "://";   //ISP 카드 결제시, URL 대신 앱명 입력시, 앱호출함 
		$LGD_KVPMISPCANCELURL     	= $this->config->item('app_name') . "://";
		// kmh_print('IS_APP - iOS');
	elseif( IS_APP && $os = 'android' ) :
		// ANDROID
		$LGD_KVPMISPNOTEURL       	= "";
		$LGD_KVPMISPWAPURL			= "";   //ISP 카드 결제시, URL 대신 앱명 입력시, 앱호출함 
		$LGD_KVPMISPCANCELURL     	= "";
		// kmh_print('IS_APP - Android');
	endif;
	*/

	/*
	 *************************************************
	 * 2. MD5 해쉬암호화 (수정하지 마세요) - BEGIN
	 * 
	 * MD5 해쉬암호화는 거래 위변조를 막기위한 방법입니다. 
	 *************************************************
	 */
	require_once( $this->lgdacom_path . "/XPayClient.php");
	$xpay = new XPayClient($configPath, $CST_PLATFORM);
	$xpay->Init_TX($LGD_MID);
	$LGD_TIMESTAMP = $xpay->GetTimeStamp(); 
	$LGD_HASHDATA = $xpay->GetHashData($LGD_MID,$LGD_OID,$LGD_AMOUNT,$LGD_TIMESTAMP);
	$LGD_CUSTOM_PROCESSTYPE = "TWOTR";
	/*
	 *************************************************
	 * 2. MD5 해쉬암호화 (수정하지 마세요) - END
	 *************************************************
	 */
	$CST_WINDOW_TYPE = "submit";										// 수정불가
	$payReqMap['CST_PLATFORM']           = $CST_PLATFORM;				// 테스트, 서비스 구분
	$payReqMap['CST_WINDOW_TYPE']        = $CST_WINDOW_TYPE;			// 수정불가
	$payReqMap['CST_MID']                = $CST_MID;					// 상점아이디
	$payReqMap['LGD_MID']                = $LGD_MID;					// 상점아이디
	$payReqMap['LGD_OID']                = $LGD_OID;					// 주문번호
	$payReqMap['LGD_BUYER']              = $LGD_BUYER;            		// 구매자
	$payReqMap['LGD_PRODUCTINFO']        = $LGD_PRODUCTINFO;     		// 상품정보
	$payReqMap['LGD_AMOUNT']             = $LGD_AMOUNT;					// 결제금액
	$payReqMap['LGD_BUYEREMAIL']         = $LGD_BUYEREMAIL;				// 구매자 이메일
	$payReqMap['LGD_CUSTOM_SKIN']        = $LGD_CUSTOM_SKIN;			// 결제창 SKIN
	$payReqMap['LGD_CUSTOM_PROCESSTYPE'] = $LGD_CUSTOM_PROCESSTYPE;		// 트랜잭션 처리방식
	$payReqMap['LGD_TIMESTAMP']          = $LGD_TIMESTAMP;				// 타임스탬프
	$payReqMap['LGD_HASHDATA']           = $LGD_HASHDATA;				// MD5 해쉬암호값
	$payReqMap['LGD_RETURNURL']   		 = $LGD_RETURNURL;      		// 응답수신페이지
	$payReqMap['LGD_VERSION']         	 = "PHP_Non-ActiveX_SmartXPay";	// 버전정보 (삭제하지 마세요)
	$payReqMap['LGD_CUSTOM_FIRSTPAY']  	 = $LGD_CUSTOM_FIRSTPAY;		// 디폴트 결제수단
	$payReqMap['LGD_PCVIEWYN']			 = $LGD_PCVIEWYN;				// 휴대폰번호 입력 화면 사용 여부(유심칩이 없는 단말기에서 입력-->유심칩이 있는 휴대폰에서 실제 결제)
	$payReqMap['LGD_CUSTOM_SWITCHINGTYPE'] = "SUBMIT";
	$payReqMap['LGD_DOMAIN_URL'] 		   = "xpayvvip";

	//iOS 연동시 필수
	$payReqMap['LGD_MPILOTTEAPPCARDWAPURL'] = $LGD_MPILOTTEAPPCARDWAPURL;
  
	/*
	****************************************************
	* 신용카드 ISP(국민/BC)결제에만 적용 - BEGIN 
	****************************************************
	*/
	$payReqMap['LGD_KVPMISPWAPURL']		 	= $LGD_KVPMISPWAPURL;	
	$payReqMap['LGD_KVPMISPCANCELURL']  	= $LGD_KVPMISPCANCELURL;
	
	/*
	****************************************************
	* 신용카드 ISP(국민/BC)결제에만 적용  - END
	****************************************************
	*/
		
	/*
	****************************************************
	* 계좌이체 결제에만 적용 - BEGIN 
	****************************************************
	*/
	$payReqMap['LGD_MTRANSFERWAPURL']		= $LGD_MTRANSFERWAPURL;	
	$payReqMap['LGD_MTRANSFERCANCELURL']  	= $LGD_MTRANSFERCANCELURL;
	
	/*
	****************************************************
	* 계좌이체 결제에만 적용  - END
	****************************************************
	*/
	
	
	/*
	****************************************************
	* 모바일 OS별 ISP(국민/비씨), 계좌이체 결제 구분 값
	****************************************************
	- 안드로이드: A (디폴트)
	- iOS: N
	- iOS일 경우, 반드시 N으로 값을 수정
			Y : ISP 비동기 결제처리,  A: ISP 동기 결제처리,  N:  ISP 동기 결제처리(iOS Web-to-Web) 
	*/

	if( !IS_APP && $os == 'ios' ) :
		$LGD_KVPMISPAUTOAPPYN = 'N';
	else :
		$LGD_KVPMISPAUTOAPPYN = 'A';
	endif;

	$payReqMap['LGD_KVPMISPAUTOAPPYN']	= $LGD_KVPMISPAUTOAPPYN;		// 신용카드 결제 
	$payReqMap['LGD_MTRANSFERAUTOAPPYN']= $LGD_KVPMISPAUTOAPPYN;		// 계좌이체 결제

	// 가상계좌(무통장) 결제연동을 하시는 경우  할당/입금 결과를 통보받기 위해 반드시 LGD_CASNOTEURL 정보를 LG 유플러스에 전송해야 합니다 .
	$payReqMap['LGD_CASNOTEURL'] = $LGD_CASNOTEURL;               // 가상계좌 NOTEURL

	//Return URL에서 인증 결과 수신 시 셋팅될 파라미터 입니다.*/
	$payReqMap['LGD_RESPCODE']           = "";
	$payReqMap['LGD_RESPMSG']            = "";
	$payReqMap['LGD_PAYKEY']             = "";

	// 추가 정보
	$payReqMap['LGD_BUYERID']             = $this->logined->mb_id;
	$payReqMap['LGD_BUYERIP']             = get_ip();	

	// 모바일 정보
	$payReqMap['is_app']             	  = IS_APP;
	$payReqMap['os']             		  = $os;

	// UTF-8
	$payReqMap['LGD_ENCODING'] = "UTF-8";
	$payReqMap['LGD_ENCODING_NOTEURL'] = "UTF-8";
	$payReqMap['LGD_ENCODING_RETURNURL'] = "UTF-8";

	$_SESSION['PAYREQ_MAP'] = $payReqMap;
?>


<!-- 테스트 -->
<? if( $this->testmode ) : ?>
	<script language="javascript" src="https://pretest.uplus.co.kr:9443/xpay/js/xpay_crossplatform.js" type="text/javascript"></script>
<? else : ?>
	<script language="javascript" src="https://xpayvvip.uplus.co.kr/xpay/js/xpay_crossplatform.js" type="text/javascript"></script>
<? endif; ?>
<!-- End Of 테스트 -->

<script type="text/javascript">
	var LGD_window_type = '<?= $CST_WINDOW_TYPE ?>'; 
	/*
	* 수정불가
	*/
	function launchCrossPlatform(){
		  lgdwin = open_paymentwindow(document.getElementById('LGD_PAYINFO'), '<?= $CST_PLATFORM ?>', LGD_window_type);
		  // lgdwin = openXpay(document.getElementById('LGD_PAYINFO'), '<?= $CST_PLATFORM ?>', LGD_window_type);
	}
	/*
	* FORM 명만  수정 가능
	*/
	function getFormObject() {
			return document.getElementById("LGD_PAYINFO");
	}
</script>

<form method="post" name="LGD_PAYINFO" id="LGD_PAYINFO" action="">
	<h3 class="text-center">결제 진행 중입니다. SMARTXPAY</h3>
	<div class="text-center">
		<a href="/member/charge" class="btn btn-default">포인트 충전으로 이동</a>
	</div>	
	<!-- 
	<table>
		<tr>
			<td>구매자 이름 </td>
			<td><?= $LGD_BUYER ?></td>
		</tr>
		<tr>
			<td>상품정보 </td>
			<td><?= $LGD_PRODUCTINFO ?></td>
		</tr>
		<tr>
			<td>결제금액 </td>
			<td><?= $LGD_AMOUNT ?></td>
		</tr>
		<tr>
			<td>구매자 이메일 </td>
			<td><?= $LGD_BUYEREMAIL ?></td>
		</tr>
		<tr>
			<td>주문번호 </td>
			<td><?= $LGD_OID ?></td>
		</tr>
		<tr>
			<td colspan="2">* 추가 상세 결제요청 파라미터는 메뉴얼을 참조하시기 바랍니다.</td>
		</tr>
		<tr>
			<td colspan="2"></td>
		</tr>    
		<tr>
			<td colspan="2">
			<input type="button" value="인증요청" onclick="launchCrossPlatform();"/>         
			</td>
		</tr>    
	</table>
	-->
	
	<?php
	  foreach ($payReqMap as $key => $value) {
		echo "<input type='hidden' name='$key' id='$key' value='$value'>";
	  }
	  // kmh_print($_SESSION);
	?>
</form>

<script>
	$(function(){
		launchCrossPlatform();
	});
</script>