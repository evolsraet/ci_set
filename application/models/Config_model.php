
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Config_model extends MY_Model {

	public $prefix = 'config_';

	public function __construct()
	{
		parent::__construct();

		$this->protected[] = $this->primary_key;

		$this->created = true;
		$this->updated = true;

		// 연결
		// $this->after_get[] = 'after_get';

		// before_create
		// after_create
		// before_update
		// after_update
		// before_get
		// after_get
		// before_delete
		// after_delete
	}

	public function get_desc($id) {
		$db = $this->get($id);
		if( isset($db->config_desc) )
			return $db->config_desc;
		else
			return false;
	}

	public function get_value($id) {
		$db = $this->get($id);
		if( isset($db->config_value) )
			return $db->config_value;
		else
			return false;
	}

	public function get_list($limit = 10) {
		$this->trigger('before_get');
		// $this->controlable();

		// 검색
		foreach( (array) $this->input->get() as $key => $row ) :
			// 검색어
			if( $key == 'search_text' && $row ) :
				$this->group_start();
				$this->or_like('config_id', $this->input->get('search_text'));
				$this->or_like('config_value', $this->input->get('search_text'));
				$this->group_end();
			// 기타 필드
			elseif( strpos($key, $this->prefix) !== false && $row ) :
				if( is_array($row) ) :
					$this->where_in($key, $this->input->get($key));
				elseif( $row != '' ) :
					$this->like($key, $this->input->get($key));
				endif;
			endif;
		endforeach;

		// 정렬
		if(
			$this->input->get('order_field')
			AND $this->input->get('order_value')
		) :
			$this->order_by( $this->input->get('order_field'), $this->input->get('order_value') );
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
