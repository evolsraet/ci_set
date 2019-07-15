<?
	function order_status($val=null) {
		$array = array();
		$array['100_ask'] = "주문요청";
		$array['200_waitpaid'] = "입금대기";
		$array['300_prepare'] = "처리중";
		$array['500_complete'] = "완료";
		$array['900_cancel'] = "주문취소";

		switch ($val) {
			case '100_ask':
				$class = 'info';
				break;
			case '200_waitpaid':
			case '300_prepare':
				$class = 'danger';
				break;
			case '500_complete':
				$class = 'success';
				break;
			case '900_cancel':
			default:
				$class='default';
				break;
		}

		// ifelse
		if( $val ) :
			return "<label class=\"label label-{$class}\">{$array[$val]}</label>";
		else :
			return $array;
		endif;
		// End of ifelse
	}

	// 코드값으로 몇 뎁스의 코드인지
	function category_depth($str, $depth_per_str=3) {
		return (strlen($str) / $depth_per_str) - 1;
	}

	// 뎁스에 맞게 코드값 자르기
	function get_cate_id($str, $depth=0) {
		$cate_length = ($depth+1) * 3;
		return substr($str, 0, $cate_length);
	}
?>