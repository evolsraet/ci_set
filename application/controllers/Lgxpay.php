<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
	- 카드 결제 까지

	- 카드 플로우
		PC
			payreq_crossplatform -> returnurl(parent.payment_return()) -> 	성공 : payres
												 							실패 : alert & close
		MOBILE
			payreq_crossplatform -> returnurl(payment_return()) -> 	성공 : payres
																	실패 : url
	- lgxpay/* CSRF 패스 필요

	- DB 추가 (변경 가능)
		CREATE TABLE `kmh_lgxpay` (
		  `lg_tid` varchar(255) NOT NULL DEFAULT '',
		  `lg_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `lg_mb_id` int(11) DEFAULT NULL,
		  `lg_detail` text NOT NULL,
		  `lg_session` text,
		  PRIMARY KEY (`lg_tid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */

class Lgxpay extends MY_Controller {

	public $lgdacom_path = VIEWPATH . 'lgxpay/lgdacom';
	public $testmode = true;
	// public $cst_mid = 'watosys';
	public $cst_mid = 'lgdacomxpay';

	public $redirect = '/member/point';

	function __construct()
	{
		parent::__construct();
		$this->page_name = "결제";
	}

	public function index() {
		$this->sample();
	}

	// 테스트 오프너 -> payreq_crossplatform
	public function sample() {
		$this->kmh->log('sample', 'lgxpay');
		$this->_render('lgxpay/sample', 'AJAX');
	}

	// 결제 창 오픈너 -> payres
	public function payreq() {
		$this->load->library('user_agent');

		$this->members->require_login();
		$this->kmh->log('payreq_crossplatform', 'lgxpay');
		
		$payreq_file = $this->agent->is_mobile()
						? "lgxpay/payreq_smartxpay"
						: "lgxpay/payreq";

		$this->_render($payreq_file);
	}

	/* 가상계좌(무통장) 결제 연동을 하시는 경우 아래 LGD_CASNOTEURL 을 설정하여 주시기 바랍니다. */    
	public function cas_noteurl() {
		$this->kmh->log('cas_noteurl', 'lgxpay');
		die('cas_noteurl');
	}

	/* LGD_RETURNURL 을 설정하여 주시기 바랍니다. 반드시 현재 페이지와 동일한 프로트콜 및  호스트이어야 합니다. 아래 부분을 반드시 수정하십시요. */    
	public function returnurl() {
		$this->kmh->log('returnurl', 'lgxpay');
		$this->_render('lgxpay/returnurl', 'AJAX');
	}

	// 결과
	public function payres() {
		echo "<div style='text-align: center'><img src='/assets/img/common/ajax_loader_gray_128.gif'></div>";
		$this->kmh->log('payres', 'lgxpay');
		$log_message = '';		// 로그 메세지
		$params = new stdClass;	// 데이터
		$final_success = false;	// 최종 성공유무 - 메세지 표기용도

		/*
		 * [최종결제요청 페이지(STEP2-2)]
		 *
		 * 매뉴얼 "5.1. XPay 결제 요청 페이지 개발"의 "단계 5. 최종 결제 요청 및 요청 결과 처리" 참조
		 *
		 * LG유플러스으로 부터 내려받은 LGD_PAYKEY(인증Key)를 가지고 최종 결제요청.(파라미터 전달시 POST를 사용하세요)
		 */
		
		/* ※ 중요
		* 환경설정 파일의 경우 반드시 외부에서 접근이 가능한 경로에 두시면 안됩니다.
		* 해당 환경파일이 외부에 노출이 되는 경우 해킹의 위험이 존재하므로 반드시 외부에서 접근이 불가능한 경로에 두시기 바랍니다. 
		* 예) [Window 계열] C:\inetpub\wwwroot\lgdacom ==> 절대불가(웹 디렉토리)
		*/
		
		$configPath = $this->lgdacom_path; //LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf,/conf/mall.conf") 위치 지정. 

		/*
		 *************************************************
		 * 1.최종결제 요청 - BEGIN
		 *  (단, 최종 금액체크를 원하시는 경우 금액체크 부분 주석을 제거 하시면 됩니다.)
		 *************************************************
		 */
		$CST_PLATFORM               = $_POST["CST_PLATFORM"];
		$CST_MID                    = $_POST["CST_MID"];
		$LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
		$LGD_PAYKEY                 = $_POST["LGD_PAYKEY"];

		require_once( $this->lgdacom_path . "/XPayClient.php" );

		// (1) XpayClient의 사용을 위한 xpay 객체 생성
		// (2) Init: XPayClient 초기화(환경설정 파일 로드) 
		// configPath: 설정파일
		// CST_PLATFORM: - test, service 값에 따라 lgdacom.conf의 test_url(test) 또는 url(srvice) 사용
		//				- test, service 값에 따라 테스트용 또는 서비스용 아이디 생성
		$tmp = new XPayClient($configPath, $CST_PLATFORM);
		$xpay = &$tmp;

		// (3) Init_TX: 메모리에 mall.conf, lgdacom.conf 할당 및 트랜잭션의 고유한 키 TXID 생성
		$xpay->Init_TX($LGD_MID);    
		$xpay->Set("LGD_TXNAME", "PaymentByKey");
		$xpay->Set("LGD_PAYKEY", $LGD_PAYKEY);
		
		//금액을 체크하시기 원하는 경우 아래 주석을 풀어서 이용하십시요.
		//$DB_AMOUNT = "DB나 세션에서 가져온 금액"; //반드시 위변조가 불가능한 곳(DB나 세션)에서 금액을 가져오십시요.
		//$xpay->Set("LGD_AMOUNTCHECKYN", "Y");
		//$xpay->Set("LGD_AMOUNT", $DB_AMOUNT);
			
		/*
		 *************************************************
		 * 1.최종결제 요청(수정하지 마세요) - END
		 *************************************************
		 */

		/*
		 * 2. 최종결제 요청 결과처리
		 *
		 * 최종 결제요청 결과 리턴 파라미터는 연동메뉴얼을 참고하시기 바랍니다.
		 */
		// (4) TX: lgdacom.conf에 설정된 URL로 소켓 통신하여 최종 인증요청, 결과값으로 true, false 리턴
		if ($xpay->TX()) {
			//1)결제결과 화면처리(성공,실패 결과 처리를 하시기 바랍니다.)
			$log_message .= "결제요청이 완료되었습니다.  \r\n";
			$log_message .= "TX 통신 응답코드 = " . $xpay->Response_Code() . "\r\n";		//통신 응답코드("0000" 일 때 통신 성공)
			$log_message .= "TX 통신 응답메시지 = " . $xpay->Response_Msg() . "\r\n";
				
			$log_message .= "거래번호 : " . $xpay->Response("LGD_TID",0) . "\r\n";
			$log_message .= "상점아이디 : " . $xpay->Response("LGD_MID",0) . "\r\n";
			$log_message .= "상점주문번호 : " . $xpay->Response("LGD_OID",0) . "\r\n";
			$log_message .= "결제금액 : " . $xpay->Response("LGD_AMOUNT",0) . "\r\n";
			$log_message .= "결과코드 : " . $xpay->Response("LGD_RESPCODE",0) . "\r\n";	//LGD_RESPCODE 가 반드시 "0000" 일때만 결제 성공, 그 외는 모두 실패
			$log_message .= "결과메세지 : " . $xpay->Response("LGD_RESPMSG",0) . "\r\n";
				
			$keys = $xpay->Response_Names();
			foreach($keys as $name) {
				$params->{$name} = $xpay->Response($name, 0);
			}
			  
			$log_message .= "\r\n";
			
			// (5) DB에 요청 결과 처리
			if( "0000" == $xpay->Response_Code() ) {
				//통신상의 문제가 없을시
				//최종결제요청 결과 성공 DB처리(LGD_RESPCODE 값에 따라 결제가 성공인지, 실패인지 DB처리)
				// $log_message .= "최종결제요청 결과 성공 DB처리하시기 바랍니다.\r\n";
				//최종결제요청 결과를 DB처리합니다. (결제성공 또는 실패 모두 DB처리 가능)

				/*----------  디비처리  ----------*/
				$isDBOK = $this->db_process($params);
				if( $isDBOK ) {
					$final_success = true;
				}
				/*----------  디비처리  ----------*/
				
				//상점내 DB에 어떠한 이유로 처리를 하지 못한경우 false로 변경해 주세요.
				if( !$isDBOK ) {
					$log_message .= "\r\n";
					$xpay->Rollback("상점 DB처리 실패로 인하여 Rollback 처리 [TID:" . $xpay->Response("LGD_TID",0) . ",MID:" . $xpay->Response("LGD_MID",0) . ",OID:" . $xpay->Response("LGD_OID",0) . "]");            		            		
						
					$log_message .= "TX Rollback Response_code = " . $xpay->Response_Code() . "\r\n";
					$log_message .= "TX Rollback Response_msg = " . $xpay->Response_Msg() . "\r\n";
						
					if( "0000" == $xpay->Response_Code() ) {
						$log_message .= "자동취소가 정상적으로 완료 되었습니다.\r\n";
					}else{
						$log_message .= "자동취소가 정상적으로 처리되지 않았습니다.\r\n";
					}
				}            	
			}else{
				//통신상의 문제 발생(최종결제요청 결과 실패 DB처리)
				// $log_message .= "최종결제요청 결과 실패 DB처리하시기 바랍니다.\r\n";
				$log_message .= "최종결제요청 결과 실패.\r\n";
			}
		}else {
			//2)API 요청실패 화면처리
			$log_message .= "결제요청이 실패하였습니다.  \r\n";
			$log_message .= "TX Response_code = " . $xpay->Response_Code() . "\r\n";
			$log_message .= "TX Response_msg = " . $xpay->Response_Msg() . "\r\n";
				
			//최종결제요청 결과 실패 DB처리
			// $log_message .= "최종결제요청 결과 실패 DB처리하시기 바랍니다.\r\n";            	                        
		}

		$this->kmh->log($log_message, 'lgxpay');
		$this->kmh->log($params, 'lgxpay');
		
		if( $final_success ) :
			add_flashdata(
				'page_notice',
				'결제요청이 완료되었습니다.'
			);			
		else : 
			add_flashdata(
				'page_notice',
				str_replace("\r\n", "<br>", $log_message)
				
			);
		endif;

		redirect('/member/point','refresh');
	}


	// 디비 처리
	private function db_process($data) {
		$data = (object) $data;
		try {
			$this->load->model('point_model');
			$this->db->trans_start();
				// 포인트 아이템
				$point_item = $this->db->where('pi_money', $data->LGD_AMOUNT)->get('point_item')->row();
				// 포인트 삽입
				$this->point_model->add(
					$data->LGD_BUYERID, 
					$point_item->pi_point, 
					'LG 결제', 
					$data->LGD_TID
				);
				// 결제데이터 삽입
				$this->db->insert(
					'lgxpay',
					array(
						'lg_tid' => $data->LGD_TID,
						'lg_mb_id' => $this->logined->mb_id,
						'lg_detail' => print_r($data, true),
						'lg_session' => print_r($_SESSION['PAYREQ_MAP'], true),
					)
				);
			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {
				throw new Exception("DB 에러", 1);
			}			

			return true;
		} catch (Exception $e) {
			$this->kmh->log($e->getMessage(), 'lgxpay');
			return false;
		}
	}

}
