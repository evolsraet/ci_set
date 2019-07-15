<?
	function mb_status($val = null) {
		$data = array();
		$data['ask']  = '심사중';
		$data['ok']   = '정상회원';
		$data['fail'] = '심사거부';
		$data['out']  = '탈퇴';

		if( $val )
			return $data[$val];
		else
			return $data;
	}
?>