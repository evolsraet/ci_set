<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title></title>
	<link rel="stylesheet" href="">
</head>
<body>
	
<form method="post" id="LGD_PAYINFO" action="lgxpay/payreq">
    <div>
        <table>
            <tr>
                <td>상점아이디(t를 제외한 아이디) </td>
                <td><input type="text" name="CST_MID" value="<?=$this->cst_mid?>"/></td>
            </tr>
            <tr>
                <td>서비스,테스트 </td>
                <td><input type="text" name="CST_PLATFORM" value="test"/></td>
            </tr>
            <tr>
                <td>구매자 이름 </td>
                <td><input type="text" name="LGD_BUYER" value="홍길동"/></td>
            </tr>
            <tr>
                <td>상품정보 </td>
                <td><input type="text" name="LGD_PRODUCTINFO" value="myLG070-인터넷전화기"/></td>
            </tr>
            <tr>
                <td>결제금액 </td>
                <td><input type="text" name="LGD_AMOUNT" value="50000"/></td>
            </tr>
            <tr>
                <td>구매자 이메일 </td>
                <td><input type="text" name="LGD_BUYEREMAIL" value=""/></td>
            </tr>
            <tr>
                <td>주문번호 </td>
                <td><input type="text" name="LGD_OID" value="test_1234567890020"/></td>
            </tr>
            <tr>
                <td>타임스탬프 </td>
                <td><input type="text" name="LGD_TIMESTAMP" value="1234567890"/></td>
            </tr>
            <tr>
                <td>초기결제수단 </td>
                <td>
					<select name="LGD_CUSTOM_FIRSTPAY">
						<option value="SC0010">신용카드</option>				
						<option value="SC0030">계좌이체</option>				
						<option value="SC0040">무통장입금</option>				
						<option value="SC0060">휴대폰</option>				
						<option value="SC0070">유선전화결제</option>				
						<option value="SC0090">OK캐쉬백</option>				
						<option value="SC0111">문화상품권</option>				
						<option value="SC0112">게임문화상품권</option>				
					</select></td>
			</tr>
            <tr>
                <td>
                <input type="submit" value="결제하기" /><br/>
                </td>
            </tr>
        </table>
    </div>
    </form>


</body>
</html>
