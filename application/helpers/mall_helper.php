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
?>