<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Member_model extends MY_Model {

	public $prefix = 'mb_';

	public function __construct() {
		parent::__construct();

		$this->protected[] = $this->primary_key;

		$this->created = true;
		$this->updated = true;
		$this->soft_delete = true;

		$this->before_create[] = 'hash_password';
		$this->before_update[] = 'hash_password';
		$this->after_get[] = 'after_get';

		// before_create
		// after_create
		// before_update
		// after_update
		// before_get
		// after_get
		// before_delete
		// after_delete

	}

	// 앱 authKey
		public function encode_auth($id) {
			return urlencode($this->encryption->encrypt($id));
		}

		public function decode_auth($id) {
			return urldecode($this->encryption->decrypt($id));
		}

	// 해시아디
		public function hash_encode($id) {
			$hashids = new \Hashids\Hashids();
			return $hashids->encode($id);
		}

		public function hash_decode($id) {
			$hashids = new \Hashids\Hashids();
			$decode = $hashids->decode($id);
			return $decode[0];
		}		

	public function get_name($mb_id) {
		$mb = $this->where('mb_id', $mb_id)->get();
		return $mb->mb_display;
	}

	public function login_check($db) {
		// 에러 처리
		if( $db->mb_id=='' )
			throw new Exception("가입되지 않은 회원입니다.");
		else if( $db->mb_status == 'ask' )
			throw new Exception("심사중인 회원입니다.");
		else if( $db->mb_status != 'ok' )
			throw new Exception("정상회원이 아닙니다. 관리자에게 문의하세요.");
		else if( $this->input->post('mb_password') != $this->encryption->decrypt( $db->mb_password ) )
			throw new Exception("비밀번호가 일치하지 않습니다.");

		$this->member_model->login_process( $db );
	}

	public function login_process( $mb_data = null ) {
		$this->session->set_userdata('member', $mb_data);
		$this->logined = $this->session->userdata('member');
	}

	public function hash_password($data) {
		if( $data ) $data = (object) $data;

		// email 이 필수가 아니면서 중복을 방지할때 (빈값은 null로)
		// if( isset($data->mb_email) && trim($data->mb_email) == '' )
		// 	$data->mb_email = null;

		if( isset($data->mb_tid) )
			$data->mb_tid = trim($data->mb_tid);
		if( isset($data->mb_mobile) )
			$data->mb_mobile = only_number($data->mb_mobile);
		if( isset($data->mb_jumin) )
			$data->mb_jumin = only_number($data->mb_jumin);	

		if( empty($data->mb_password) ) :
			unset( $data->mb_password );
		else :
			$data->mb_password = $this->encryption->encrypt($data->mb_password);
		endif;

		return $data;
	}

	public function after_get($data) {
		if( isset($data->mb_mobile) )
			$data->mb_mobile = add_hyphen($data->mb_mobile);

		return $data;
	}

	// 목록
	public function get_list($limit = 10) {
		$this->trigger('before_get');
		// $this->controlable();

		if( !$_GET['search_type'] )
			$_GET['search_type'] = '';

		// 검색
		foreach( (array) $this->input->get() as $key => $row ) :
			// 검색어
			if( $key == 'search_text' && $row ) :
				$this->group_start();
				$this->or_like('mb_email', $this->input->get('search_text'));
				$this->or_like('mb_display', $this->input->get('search_text'));
				$this->or_like('mb_name', $this->input->get('search_text'));
				$this->group_end();

			// elseif( $key == 'search_type' ) :
			// 	switch ($row) {
			// 		case 'alltire':
			// 			$this->where('mb_level >=', 90);
			// 			break;
					
			// 		default:
			// 			$this->where('mb_level', 1);
			// 			break;
			// 	}
			
			// 기타 필드
			elseif( strpos($key, $this->prefix) !== false && $row ) :
				if( is_array($row) ) :
					$this->where_in($key, $this->input->get($key));
				elseif( $row != '' ) :
					$this->like($key, $this->input->get($key));
				endif;
			endif;
		endforeach;

		// 삭제회원 
		if( $_GET['with_deleted'] ) :
			$this->with_deleted();
		endif;

		// 정렬
		if(
			$this->input->get('order_field')
			AND $this->input->get('order_value')
		) :
			$this->order_by( $this->input->get('order_field'), $this->input->get('order_value') );
		else : 
			$this->order_by('mb_created_at', 'DESC');
		endif;	


		// 데이터들
		$result['total'] = $this->qb_cache()->count_by();

		$paging_query = $this->paging(
							$limit,
							$result['total'],
							$this->input->get('page')
						);
		// kmh_print($this->_database->last_query());

		$result['qry'] = $this->_database->last_query();
		$result['pagination'] = $paging_query->pagenation();

		$result['list'] = $paging_query->get_all();

		return $result;
	}		

}
