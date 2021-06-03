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

	// 엑셀
	function member_excel($type = null, $val = null) {
		$field = array(
			'mb_id'     => '상태',
			// 'mb_email'      => '이메일',
			// 'mb_display'    => '닉네임',
			'mb_created_at' => '가입일',
			// 'mb_name'       => '성명',
			// 'mb_mobile'     => '연락처',
		);

		switch ($type) {
			default:
				return $field;
				break;
		}
	}	
?>