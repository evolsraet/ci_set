<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Product_model extends MY_Model {

	public $prefix = 'pd_';

	public function __construct()
	{
		parent::__construct();

		$this->protected[] = $this->primary_key;

		$this->created = true;
		$this->updated = true;
		$this->soft_delete = true;

		// 연결
		// $this->after_delete[] = 'after_delete';
		// $this->before_create[] = 'before_create';
		// $this->after_create[] = 'after_create';
		// $this->before_get[] = 'before_get';
		$this->before_create[] = 'pd_min';
		$this->before_update[] = 'pd_min';
		$this->after_delete[] = 'after_delete';

		// before_create
		// after_create
		// before_update
		// after_update
		// before_get
		// after_get
		// before_delete
		// after_delete
	}


	protected function after_delete($data) {
		$delete_post_ids = array();
		foreach( fe( $data['data'] ) as $key => $row ) :
			$delete_post_ids[] = $row->post_id;
		endforeach;

		// 파일 삭제
		$this->file_model
			->where('file_rel_type', 'product')
			->where_in( 'file_rel_id',  $delete_post_ids)
			->delete();

		// 댓글 삭제
		$this->comment_model
			->where('cm_type', 'product')
			->where_in('cm_post_id', $delete_post_ids)
			->force_delete()
			->delete();
	}

	public function pd_min($data) {
		if( !$data )
			$data = new stdClass;

		if( $data->pd_min && $data->pd_min < 1 ) $data->pd_min = 1;
		return $data;
	}

	public function get_list() {
		$result['order'] = $_GET['order'];

		if( $result['order'] == '' ) :
			$result['order'] = $this->input->cookie('product_order') ? $this->input->cookie('product_order') : '_null';
		elseif( $result['order'] == '_null' ) :
			$result['order'] = '_null';
			$this->input->set_cookie('product_order', null);
		else:
			$this->console->log( 'else' );
			$this->input->set_cookie('product_order', $result['order'], 0);
		endif;

		if( $this->input->cookie('product_order') )
			$_GET['order'] = $this->input->cookie('product_order');

		if( $this->input->get('category') )
			$this->product_model->where('pd_cate_id', $this->input->get('category'));

		if( $this->input->get('search_text') )
			$this->product_model->like('pd_name', $this->input->get('search_text'));

		if( $result['order'] != '_null' ) {
			switch ($result['order']) {
				case 'pd_order':
					$this->product_model->order_by( $result['order'], 'DESC' );
					break;

				default:
					$this->product_model->order_by( $result['order'], 'ASC' );
					break;
			}
		}


		$result['total'] = $this->product_model->qb_cache()->count_by();
		$paging_query = $this->product_model->paging(
							12,
							$result['total'],
							$this->input->get('page')
						);

		$result['pagination'] = $paging_query->pagenation();
		$result['list'] = $paging_query->get_all();

		return $result;
	}

}
