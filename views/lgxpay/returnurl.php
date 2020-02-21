<?php
/*
  payreq_crossplatform 에서 세션에 저장했던 파라미터 값이 유효한지 체크
  세션 유지 시간(로그인 유지시간)을 적당히 유지 하거나 세션을 사용하지 않는 경우 DB처리 하시기 바랍니다.
*/
  session_start();
  if(!isset($_SESSION['PAYREQ_MAP'])){
  	echo "세션이 만료 되었거나 유효하지 않은 요청 입니다.";
  	// kmh_print($_POST);
  	// kmh_print($_GET);
  	return;
  }
  $payReqMap = $_SESSION['PAYREQ_MAP'];//결제 요청시, Session에 저장했던 파라미터 MAP
?>
<html>
<head>
	<script type="text/javascript">
		/*
		 * 인증결과 처리
		 * 	 모바일용 처리
		 */
		function payment_return() {
			if (document.getElementById('LGD_RESPCODE').value == "0000") {
				document.getElementById("LGD_PAYKEY").value = document.getElementById('LGD_PAYKEY').value;
				document.getElementById("LGD_RETURNINFO").target = "_self";
				document.getElementById("LGD_RETURNINFO").action = "/lgxpay/payres";
				document.getElementById("LGD_RETURNINFO").submit();
			} else {
				alert("LGD_RESPCODE (결과코드) : " + document.getElementById('LGD_RESPCODE').value + "\n" + "LGD_RESPMSG (결과메시지): " + document.getElementById('LGD_RESPMSG').value);
			}
		}

		function setLGDResult() {
			<? if( $payReqMap['LGD_VERSION'] == 'PHP_Non-ActiveX_SmartXPay' ) : ?>
				// MOBILE
        		payment_return();
			<? else : ?>
				// PC
				parent.payment_return();
			<? endif; ?>

			try {
			} catch (e) {
				alert(e.message);
			}
		}
		
	</script>
</head>
<body onload="setLGDResult()">

<?php
  $LGD_RESPCODE = $this->input->post_get('LGD_RESPCODE');
  $LGD_RESPMSG 	= $this->input->post_get('LGD_RESPMSG');
  $LGD_PAYKEY	  = "";

  $payReqMap['LGD_RESPCODE'] = $LGD_RESPCODE;
  $payReqMap['LGD_RESPMSG']	=	$LGD_RESPMSG;

  if($LGD_RESPCODE == "0000"){
	  $LGD_PAYKEY = $this->input->post_get('LGD_PAYKEY');
	  $payReqMap['LGD_PAYKEY'] = $LGD_PAYKEY;
  }
  else{
	  echo "LGD_RESPCODE:" . $LGD_RESPCODE . " ,LGD_RESPMSG:" . $LGD_RESPMSG; //인증 실패에 대한 처리 로직 추가
  }
?>
<form method="post" name="LGD_RETURNINFO" id="LGD_RETURNINFO">
<?php
	  foreach ($payReqMap as $key => $value) {
      echo "<input type='hidden' name='$key' id='$key' value='$value'>";
    }

    // kmh_print( $payReqMap );
    // kmh_print( $_POST );
?>
<!-- <button type="button" onclick="setLGDResult()">결과 <?=$payReqMap['LGD_VERSION']?></button> -->
</form>
</body>
</html>