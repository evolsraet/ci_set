<?php
/**
 * A base model with a series of CRUD functions (powered by CI's query builder),
 * validation-in-model support, event callbacks and more.
 *
 * @link http://github.com/jamierumbelow/codeigniter-base-model
 * @copyright Copyright (c) 2012, Jamie Rumbelow <http://jamierumbelow.net>
 */


/*
 *
 *   위 기반으로 수정됨
 *   강민호 2018-08
 *
 * 	추가 할것
 *      v force_delete
 *      리스토어 옵저버
 *
 *      v after : update, soft_delete, delete  에는 적용된 로우 키값 또는 where 값을 돌려줘야한다 : 가능?
 *      before : update, create 에는 data 를 돌려준다
 *      before : get 에는 data
 *      before : soft_delete, delete 는 데이터 읽어서 보내줘야할것
 *
 *
 *	주의 사항
 *		v 직접 set, where 를 쓸 경우, 옵저버에 전달되지 않는다
 *			방법을 찾아야함!!! 옵저버용 데이터와 연계할 수 있는 방법이 필요함
 *
 *
 *
 */

/*
	obsever TEST

	create, update 옵저버 전달되는 데이터는? 배열인가?

	before_create  = 데이터배열			(many-개별)
	after_create   = 키값				(many-개별)
	before_update  = 데이터배열		(many-개별)
	v after_update = [data]데이터배열, [result] ? 성공여부?
	before_get     = 아무것도 안옴
	v after_get      = 리딩된 값		(여러개일때 개별)

	v before_delete = 리스트값['data']						= 리턴 받지 않음
	v after_delete  = 리스트값['data'] + 삭제 결과[result]		= 리턴 받지 않음

	// before_delete = 키값 	 	by 조건배열	many 키값 배열
	// after_delete  = 성공여부
*/

class MY_Model extends CI_Model
{

	/* --------------------------------------------------------------
	 * VARIABLES
	 * ------------------------------------------------------------ */

	/**
	 * This model's default database table. Automatically
	 * guessed by pluralising the model name.
	 */
	protected $_table;

	/**
	 * The database connection object. Will be set to the default
	 * connection. This allows individual models to use different DBs
	 * without overwriting CI's global $this->db connection.
	 */
	public $_database;

	/**
	 * This model's default primary key or unique identifier.
	 * Used by the get(), update() and delete() functions.
	 */
	protected $prefix = '';
	protected $primary_key = 'id';

	/**
	 * Support for soft deletes and this model's 'deleted' key
	 */
	protected $soft_delete = FALSE; // deleted_at 필드 사용
	protected $_temporary_with_deleted = FALSE;
	protected $_temporary_only_deleted = FALSE;

	protected $force_delete = FALSE;	// 강제 삭제 (soft_delete 사용안함 - 사용후 돌릴것)

	// 자동기입 설정
	protected $created = FALSE;
	protected $updated = FALSE;

	// 시간관련 필드
	protected $created_at = FALSE;
	protected $updated_at = FALSE;
	protected $deleted_at = FALSE;

	/**
	 * The various callbacks available to the model. Each are
	 * simple lists of method names (methods will be run on $this).
	 */
	protected $before_create      = array();
	protected $after_create       = array();
	protected $before_update      = array();
	protected $after_update       = array();
	protected $before_get         = array();
	protected $after_get          = array();
	protected $before_delete      = array();
	protected $after_delete       = array();
	protected $before_soft_delete = array();
	protected $after_soft_delete  = array();

	protected $callback_parameters = array();

	/**
	 * Protected, non-modifiable attributes
	 */
	protected $protected = array();

	/**
	 * Relationship arrays. Use flat strings for defaults or string
	 * => array to customise the class name and primary key
	 */
	protected $has_one = array();
	protected $has_many = array();

	protected $_with = array();
	protected $_with_attr = array();

	/**
	 * An array of validation rules. This needs to be the same format
	 * as validation rules passed to the Form_validation library.
	 */
	protected $validate = array();

	/**
	 * Optionally skip the validation. Used in conjunction with
	 * skip_validation() to skip data validation for any future calls.
	 */
	protected $skip_validation = FALSE;

	/**
	 * By default we return our results as objects. If we need to override
	 * this, we can, or, we could use the `as_array()` and `as_object()` scopes.
	 */
	protected $return_type = 'object';
	protected $_temporary_return_type = NULL;


	/*
	 *
	 * 옵저버와 공용을 위해
	 *
	 */

	// 쿼리빌더 실행 기능들
	public $qb_functions = array();

	// 쿼리 펑션의 캐싱
	public $qb_cache = FALSE; // TRUE 이면 리셋안됨


	/* --------------------------------------------------------------
	 * GENERIC METHODS
	 * ------------------------------------------------------------ */

	/**
	 * Initialise the model, tie into the CodeIgniter superobject and
	 * try our best to guess the table name.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->primary_key = $this->prefix.$this->primary_key;

		$this->created_at = $this->prefix.'created_at';
		$this->updated_at = $this->prefix.'updated_at';
		$this->deleted_at = $this->prefix.'deleted_at';

		// $this->load->helper('inflector');

		$this->_fetch_table();

		$this->_database = $this->db;

		array_unshift($this->before_create, 'protect_attributes');
		array_unshift($this->before_update, 'protect_attributes');

		$this->_temporary_return_type = $this->return_type;
	}

	/* --------------------------------------------------------------
	 * CRUD INTERFACE
	 * ------------------------------------------------------------ */

	/**
	 * Fetch a single record based on the primary key. Returns an object.
	 */
	public function get($primary_value = false)
	{
		if( $primary_value !== false )
			$this->where( $this->primary_key, $primary_value );

		if( $primary_value===false && !$this->qb_where_count() )
			return false;

		$this->trigger('before_get');

		$this->_where_trashed();

		$this->_qb_do();
		$result = $this->_database->get($this->_table)
						   ->{$this->_return_type()}();
		$this->_qb_reset();

		$this->_temporary_return_type = $this->return_type;

		$result = $this->trigger('after_get', $result);

		$this->_with = array();
		$this->_with_attr = array();

		return $result;
	}

	/**
	 * Fetch a single record based on an arbitrary WHERE call. Can be
	 * any valid value to $this->_database->where().

	public function get_by()
	{
		$where = func_get_args();

		$this->_where_trashed();

		$this->trigger('before_get');

		$this->_set_where($where);
		$this->_qb_do();
		$row = $this->_database->get($this->_table)
						->{$this->_return_type()}();
		$this->_qb_reset();

		$this->_temporary_return_type = $this->return_type;

		$row = $this->trigger('after_get', $row);

		$this->_with = array();
		$this->_with_attr = array();
		return $row;
	}

	/**
	 * Fetch an array of records based on an array of primary values.
	public function get_many($values)
	{
		$this->_database->where_in($this->primary_key, $values);

		return $this->get_all();
	}

	/**
	 * Fetch an array of records based on an arbitrary WHERE call.
	public function get_many_by()
	{
		$where = func_get_args();

		$this->_set_where($where);

		return $this->get_all();
	}

	/**
	 * Fetch all the records in the table. Can be used as a generic call
	 * to $this->_database->get() with scoped methods.
	 */
	public function get_all()
	{
		$this->trigger('before_get');

		$this->_where_trashed();

		$this->_qb_do();
		$result = $this->_database->get($this->_table)
						   ->{$this->_return_type(1)}();
		$this->_qb_reset();

		$this->_temporary_return_type = $this->return_type;

		foreach ($result as $key => &$row)
		{
			$row = $this->trigger('after_get', $row, ($key == count($result) - 1));
		}

		$this->_with = array();
		$this->_with_attr = array();
		return $result;
	}

	public function paging($rows_per_page, $total_rows = NULL, $page_number = 1) {
		if( $page_number < 1 ) $page_number = 1;

		$offset = $rows_per_page * ($page_number-1);
		$this->qb_functions[] = array(
				'limit' => array($rows_per_page, $offset)
			);

		return $this;
	}

	/**
	 * Insert a new row into the table. $data should be an associative array
	 * of data to be inserted. Returns newly created ID.
	 */
	public function insert($data, $skip_validation = FALSE)
	{
		if ($skip_validation === FALSE)
		{
			$data = $this->validate($data);
		}

		if ($data !== FALSE)
		{
			$data = $this->trigger('before_create', $data);
			$data = $this->created_at($data);

			$this->_qb_do();
			$this->_database->insert($this->_table, $data);
			$this->_qb_reset();

			$insert_id = $this->_database->insert_id();

			$this->trigger('after_create', $insert_id );

			return $insert_id;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Insert multiple rows into the table. Returns an array of multiple IDs.
	 */

	/*

		// _qb_do, reset 불가해서 사용 금지

	public function insert_many($data, $skip_validation = FALSE)
	{
		$ids = array();

		foreach ($data as $key => $row)
		{
			$ids[] = $this->insert($row, $skip_validation, ($key == count($data) - 1));
		}

		return $ids;
	}
	*/

	/**
	 * Updated a record based on the primary value.
	 */
	public function update($primary_value = NULL, $data = FALSE, $skip_validation = FALSE)
	{
		$trace = debug_backtrace();
		$trace = $trace[0];

		$data = $this->trigger('before_update', $data);
		$data = $this->updated_at($data);

		if ($skip_validation === FALSE)
		{
			$data = $this->validate($data);
		}

		if( $primary_value !== NULL ) {
			$this->where( $this->primary_key, $primary_value );
		}

		if( $data !== FALSE ) {
			$this->set( $data );
		}

		if(	!$this->qb_where_count() ) {
			$error_msg .= "업데이트에는 where 구문이 필요합니다. MY_Model - update()";
			$error_msg .= PHP_EOL.print_r($this->qb_functions->where, true);
			$error_msg .= PHP_EOL.'primary_value : ' . $primary_value;
			$error_msg .= PHP_EOL.'controller : ' . $this->router->class;
			$error_msg .= PHP_EOL.'method : ' . $this->router->method;
			$error_msg .= PHP_EOL.'file : ' . $trace['file'];
			$error_msg .= PHP_EOL.'line : ' . $trace['line'];
			$error_msg .= PHP_EOL.print_r( $this->qb_where_count(), true );
			show_error( $error_msg );
		}

		if ( $this->qb_set_count() )
		{
			$this->_qb_do();
			$result = $this->_database->update($this->_table);
			$this->_qb_reset();

			$this->trigger('after_update', array('data'=>$data, 'result'=>$result));

			return $result;
		}
		else
		{
			$error_msg .= "업데이트에는 set 구문이 필요합니다. MY_Model - update()";
			$error_msg .= PHP_EOL.'controller : ' . $this->router->class;
			$error_msg .= PHP_EOL.'method : ' . $this->router->method;
			$error_msg .= PHP_EOL.'file : ' . $trace['file'];
			$error_msg .= PHP_EOL.'line : ' . $trace['line'];
			$error_msg .= PHP_EOL.'data : ' . print_r( $data, true );
			$error_msg .= PHP_EOL.'set : ' . print_r( $this->qb_set_count(), true );
			show_error( $error_msg );
			return FALSE;
		}
	}

	/**
	 * Update many records, based on an array of primary values.
	 *
	public function update_many($primary_values, $data, $skip_validation = FALSE)
	{
		$data = $this->trigger('before_update', $data);
		$data = $this->updated_at($data);

		if ($skip_validation === FALSE)
		{
			$data = $this->validate($data);
		}

		if ($data !== FALSE)
		{
			$this->_qb_do();
			$result = $this->_database->where_in($this->primary_key, $primary_values)
							   ->set($data)
							   ->update($this->_table);
			$this->_qb_reset();

			$this->trigger('after_update', array('data'=>$data, 'result'=>$result));

			return $result;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Updated a record based on an arbitrary WHERE clause.
	 *
	 * 조건필드, 조건값, 데이터배열
	 * 조건배열, 데이터배열
	 *

	public function update_by()
	{
		$args = func_get_args();
		// kmh_print($args[ count($args)-1 ]);
		$data = array();

		// 마지막 인자가 배열일때만 데이터로 처리
		if( is_array( $args[ count($args)-1 ] ) ) {
			$data = array_pop($args);
		}

		$data = $this->trigger('before_update', $data);
		$data = $this->updated_at($data);

		if ($this->validate($data) !== FALSE)
		{
			$this->_qb_do();

			if( count($args) ) $this->_set_where($args);
			$result = $this->_database->set($data)
							   ->update($this->_table);
			$this->_qb_reset();
			$this->trigger('after_update', array('data'=>$data, 'result'=>$result));

			return $result;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Update all records
	 */
	public function update_all($data)
	{
		$data = $this->trigger('before_update', $data);
		$data = $this->updated_at($data);

		$this->_qb_do();
		$result = $this->_database->set($data)
						   ->update($this->_table);
		$this->_qb_reset();
		$this->trigger('after_update', array('data'=>$data, 'result'=>$result));

		return $result;
	}

	/**
	 * Delete a row from the table by the primary value
	 */

	public function _soft_delete_do() {
		// $this->_qb_do();
		return $this->_database->update($this->_table, array( $this->deleted_at => date('Y-m-d H:i:s') ));
	}

	public function _has_delete_action() {
		if( !empty($this->before_delete) || !empty($this->after_delete)
			|| !empty($this->before_soft_delete) || !empty($this->after_soft_delete)
			)
			return true;
		else
			return false;
	}

	public function force_delete() {
		$this->force_delete = TRUE;
		return $this;
	}

	public function delete($id = NULL)
	{
		$trace = debug_backtrace();
		$trace = $trace[0];

		$this->kmh->log($this->force_delete, 'force_delete 시작');
		$this->kmh->log($this->soft_delete, 'soft_delete 시작');

		// 강제삭제
		if( $this->force_delete == TRUE ) :
			$model_soft_delete = $this->soft_delete;
			$this->soft_delete = FALSE;
		endif;

		$this->kmh->log($this->soft_delete, 'soft_delete 중간');

		if( $id !== NULL )
			$this->where($this->primary_key, $id);

		if(	!$this->qb_where_count() ) {
			$error_msg .= "삭제에는 where 구문이 필요합니다. MY_Model - update()";
			$error_msg .= PHP_EOL.'controller : ' . $this->router->class;
			$error_msg .= PHP_EOL.'method : ' . $this->router->method;
			$error_msg .= PHP_EOL.'file : ' . $trace['file'];
			$error_msg .= PHP_EOL.'line : ' . $trace['line'];
			$error_msg .= PHP_EOL.print_r( $this->qb_where_count(), true );
			show_error( $error_msg );
		}

		// 옵져버 데이터
		$to_observer = array();
		if( $this->_has_delete_action() ) {
			$this->_qb_do();
			$to_observer['data'] = $this->_database->get($this->_table)->result();
		}

		if( $this->soft_delete )
			$this->trigger('before_soft_delete', $to_observer);
		else
			$this->trigger('before_delete', $to_observer);

		if ($this->soft_delete)
		{
			$this->_qb_do();
			$result = $this->_soft_delete_do();
		}
		else
		{
			$this->_qb_do();
			$result = $this->_database->delete($this->_table);
		}

		$this->_qb_reset();

		$this->kmh->log( $this->_database->last_query(), 'after delete'.$_SERVER['REQUEST_URI'] );

		$to_observer['result'] = $result;

		if( $result ) {
			if( $this->soft_delete )
				$this->trigger('after_soft_delete', $to_observer);
			else
				$this->trigger('after_delete', $to_observer);
		}

		// 강제삭제
		if( $this->force_delete == TRUE ) :
			$this->force_delete = FALSE;
			$this->soft_delete = $model_soft_delete;
		endif;

		$this->kmh->log($this->force_delete, 'force_delete 끝');
		$this->kmh->log($this->soft_delete, 'soft_delete 끝');

		return $result;
	}


	/**
	 * Delete a row from the database table by an arbitrary WHERE clause

	public function delete_by()
	{
		$where = func_get_args();

		$to_observer = array();
		if( $this->_has_delete_action() ) {
			$this->_qb_do();
			$this->_set_where($where);
			$to_observer['data'] = $this->_database->get($this->_table)->result();
		}

		if( $this->soft_delete )
			$this->trigger('before_soft_delete', $to_observer);
		else
			$this->trigger('before_delete', $to_observer);

		$this->_set_where($where);

		if ($this->soft_delete)
		{
			$this->_qb_do();
			$result = $this->_soft_delete_do();
		}
		else
		{
			$this->_qb_do();
			$result = $this->_database->delete($this->_table);
		}

		$this->_qb_reset();

		$to_observer['result'] = $result;

		if( $this->soft_delete )
			$this->trigger('after_soft_delete', $to_observer);
		else
			$this->trigger('after_delete', $to_observer);

		return $result;
	}

	/**
	 * Delete many rows from the database table by multiple primary values

	public function delete_many($primary_values)
	{
		$this->where_in($this->primary_key, $primary_values);

		$to_observer = array();
		if( $this->_has_delete_action() ) {
			$this->_qb_do();
			$to_observer['data'][] = $this->_database->get($this->_table)->result();
		}

		if( $this->soft_delete )
			$this->trigger('before_soft_delete', $to_observer);
		else
			$this->trigger('before_delete', $to_observer);


		if ($this->soft_delete)
		{
			$this->_qb_do();
			$result = $this->_soft_delete_do();
		}
		else
		{
			$this->_qb_do();
			$result = $this->_database->delete($this->_table);
		}

		$this->_qb_reset();

		$to_observer['result'] = $result;

		if( $this->soft_delete )
			$this->trigger('after_soft_delete', $to_observer);
		else
			$this->trigger('after_delete', $to_observer);

		return $result;
	}

	// 소프트 딜리트 복구
	// 단일 함수 (연계 안됨)
	public function restore($primary_key_or_array) {
		if( is_array($primary_key_or_array) ) {
			$this->_database->where( $primary_key_or_array );
		} else {
			$this->_database->where( $this->primary_key, $primary_key_or_array );
		}

		$this->_database->set( $this->deleted_at, NULL );
		return $affected_rows = $this->_database->update( $this->_table );
	}

	/**
	 * Truncates the table
	 */
	public function truncate()
	{
		$result = $this->_database->truncate($this->_table);

		return $result;
	}

	/* --------------------------------------------------------------
	 * RELATIONSHIPS
	 * ------------------------------------------------------------ */

	public function with($relationship, $attr = FALSE)
	{
		$this->_with[] = $relationship;
		$this->_with_attr[ $relationship ] = $attr;

		if (!in_array('relate', $this->after_get))
		{
			$this->after_get[] = 'relate';
		}

		return $this;
	}

	public function relate($row)
	{
		if (empty($row))
		{
			return $row;
		}

		foreach ($this->has_one as $key => $value)
		{
			if (is_string($value))
			{
				$relationship = $value;
				$options = array(
					'model' => $value . '_model',
					'primary_key' => $value . '_id',
					'foreign_key' => $this->prefix. $value . '_id',
				);
			}
			else
			{
				$relationship = $key;
				if( isset($value[0]) )	$options['model'] = $value[0];
				if( isset($value[1]) )	$options['primary_key'] = $value[1];
				if( isset($value[2]) )	$options['foreign_key'] = $value[2];
			}

			if (in_array($relationship, $this->_with))
			{
				$this->load->model($options['model'], $relationship . '_model');
				$this->relate_attr( $relationship );

				if (is_object($row))
				{
					$row->{$relationship} = $this->{$relationship . '_model'}->get($row->{$options['foreign_key']});
				}
				else
				{
					$row[$relationship] = $this->{$relationship . '_model'}->get($row[$options['foreign_key']]);
				}
			}
		}

		foreach ($this->has_many as $key => $value)
		{
			if (is_string($value))
			{
				$relationship = $value;
				// $options = array( 'primary_key' => singular($this->_table) . '_id', 'model' => singular($value) . '_model' );
				$options = array( 'primary_key' => ($this->_table) . '_id', 'model' => ($value) . '_model' );
			}
			else
			{
				$relationship = $key;
				if( isset($value[0]) )	$options['model'] = $value[0];
				if( isset($value[1]) )	$options['primary_key'] = $value[1];
				if( isset($value[2]) )	$options['foreign_key'] = $value[2];
			}

			if (in_array($relationship, $this->_with))
			{
				$this->load->model($options['model'], $relationship . '_model');
				$this->relate_attr( $relationship );

				if (is_object($row))
				{
					$row->{$relationship} = $this->{$relationship . '_model'}->get_many_by($options['primary_key'], $row->{$options['foreign_key']});
				}
				else
				{
					$row[$relationship] = $this->{$relationship . '_model'}->get_many_by($options['primary_key'], $row[$options['foreign_key']]);
				}
			}
		}

		return $row;
	}

	public function relate_attr( $relationship ) {
		// 상세
		if( isset($this->_with_attr[$relationship]) ) {
			$attrs = explode('|', $this->_with_attr[$relationship]);

			foreach( (array)$attrs as &$attr ) :
				$attr = trim( $attr );

				// 선택필드
				$keyword = 'fields:';
				if( strpos($attr, $keyword)!==FALSE ) :
					$conditions = trim( str_replace($keyword, '', $attr) );
					$this->{$relationship . '_model'}->select( $conditions );
				endif;

				// 정렬
				// order_by: test_id ASC, test_order DESC
				$keyword = 'order_by:';
				if( strpos($attr, $keyword)!==FALSE ) :
					$conditions = str_replace($keyword, '', $attr);
					$conditions = explode(',', $conditions);

					foreach( (array) $conditions as $condition ) :
						$row = explode(' ', trim($condition) );
						$this->{$relationship . '_model'}->order_by( trim($row[0]), trim($row[1]) );
					endforeach;

				endif;
			endforeach;
		}
	}


	/* --------------------------------------------------------------
	 * UTILITY METHODS
	 * ------------------------------------------------------------ */

	/**
	 * Retrieve and generate a form_dropdown friendly array
	 */
	function dropdown()
	{
		$args = func_get_args();

		if(count($args) == 2)
		{
			list($key, $value) = $args;
		}
		else
		{
			$key = $this->primary_key;
			$value = $args[0];
		}

		$this->trigger('before_dropdown', array( $key, $value ));

		$this->_where_trashed();

		$this->_qb_do();
		$result = $this->_database->select(array($key, $value))
						   ->get($this->_table)
						   ->result();
		$this->_qb_reset();

		$options = array();

		foreach ($result as $row)
		{
			$options[$row->{$key}] = $row->{$value};
		}

		$options = $this->trigger('after_dropdown', $options);

		return $options;
	}

	/**
	 * Fetch a count of rows based on an arbitrary WHERE call.
	 *
	 * qb_function 으로 읽는다
	 */
	public function count_by( $primary_value = null )
	{
		$this->_where_trashed();

		if( $primary_key!==null )
			$this->where( $this->primary_key, $primary_value );

		$this->_qb_do();
		$result = $this->_database->count_all_results($this->_table);
		$this->_qb_reset();

		return $result;
	}

	/**
	 * Fetch a total count of rows, disregarding any previous conditions
	 */
	public function count_all()
	{
		$this->_where_trashed();

		$this->_qb_do();
		$result = $this->_database->count_all($this->_table);
		$this->_qb_reset();

		return $result;
	}

	/**
	 * Tell the class to skip the insert validation
	 */
	public function skip_validation()
	{
		$this->skip_validation = TRUE;
		return $this;
	}

	/**
	 * Get the skip validation status
	 */
	public function get_skip_validation()
	{
		return $this->skip_validation;
	}

	/**
	 * Return the next auto increment of the table. Only tested on MySQL.
	 */
	public function get_next_id()
	{
		return (int) $this->_database->select('AUTO_INCREMENT')
			->from('information_schema.TABLES')
			->where('TABLE_NAME', $this->_table)
			->where('TABLE_SCHEMA', $this->_database->database)->get()->row()->AUTO_INCREMENT;
	}

	/**
	 * Getter for the table name
	 */
	public function table()
	{
		return $this->_table;
	}

	/* --------------------------------------------------------------
	 * GLOBAL SCOPES
	 * ------------------------------------------------------------ */

	/**
	 * Return the next call as an array rather than an object
	 */
	public function as_array()
	{
		$this->_temporary_return_type = 'array';
		return $this;
	}

	/**
	 * Return the next call as an object rather than an array
	 */
	public function as_object()
	{
		$this->_temporary_return_type = 'object';
		return $this;
	}

	/**
	 * Don't care about soft deleted rows on the next call
	 */
	public function with_deleted()
	{
		$this->_temporary_with_deleted = TRUE;
		return $this;
	}

	/**
	 * Only get deleted rows on the next call
	 */
	public function only_deleted()
	{
		$this->_temporary_only_deleted = TRUE;
		return $this;
	}

	public function _where_trashed() {
		if( $this->soft_delete ) {
			if( $this->_temporary_only_deleted === TRUE )
				$this->where($this->deleted_at.' !=', null);
			elseif( $this->_temporary_with_deleted !== TRUE )
				$this->where($this->deleted_at, null);
		}
	}

	/* --------------------------------------------------------------
	 * OBSERVERS
	 * ------------------------------------------------------------ */

	/**
	 * MySQL DATETIME created_at and updated_at
	 */
	public function created_at($row)
	{
		if( $this->created ) {
			if (is_object($row))
			{
				$row->{$this->created_at} = date('Y-m-d H:i:s');
			}
			else
			{
				$row[$this->created_at] = date('Y-m-d H:i:s');
			}
		}

		return $row;
	}

	public function updated_at($row)
	{
		if( $this->updated ) {
			if (is_object($row))
			{
				$row->{$this->updated_at} = date('Y-m-d H:i:s');
			}
			else
			{
				$row[$this->updated_at] = date('Y-m-d H:i:s');
			}
		}

		return $row;
	}

	/**
	 * Serialises data for you automatically, allowing you to pass
	 * through objects and let it handle the serialisation in the background
	 */
	public function serialize($row)
	{
		foreach ($this->callback_parameters as $column)
		{
			$row[$column] = serialize($row[$column]);
		}

		return $row;
	}

	public function unserialize($row)
	{
		foreach ($this->callback_parameters as $column)
		{
			if (is_array($row))
			{
				$row[$column] = unserialize($row[$column]);
			}
			else
			{
				$row->$column = unserialize($row->$column);
			}
		}

		return $row;
	}

	/**
	 * Protect attributes by removing them from $row array
	 */
	public function protect_attributes($row)
	{
		foreach ($this->protected as $attr)
		{
			if (is_object($row))
			{
				unset($row->$attr);
			}
			else
			{
				unset($row[$attr]);
			}
		}

		return $row;
	}

	/*
		쿼리 빌더 메소드 공유 (변수에 담아 두번 돌린다)
	 */

	protected function _qb_do() {
		foreach( (array) $this->qb_functions as $function_group ) :
			foreach( (array) $function_group as $function => $args ) :
				switch (count($args)) {
					case 0:
						$this->_database->{$function}();
						break;
					case 1:
						$this->_database->{$function}( $args[0] );
						break;
					case 2:
						$this->_database->{$function}( $args[0], $args[1] );
						break;
					case 3:
						$this->_database->{$function}( $args[0], $args[1], $args[2] );
						break;
					case 4:
						$this->_database->{$function}( $args[0], $args[1], $args[2], $args[3] );
						break;
					case 5:
						$this->_database->{$function}( $args[0], $args[1], $args[2], $args[3], $args[4] );
						break;
					default:
						$this->kmh->log( $function_group, 'qb_do 에러' );
						break;
				}
				// $this->kmh->log( $function_group, 'qb_do 실행' );
			endforeach;
		endforeach;
	}

	protected function _qb_reset( $force_reset = FALSE ) {
		if( !$this->qb_cache || $force_reset ) {
			$this->qb_functions = array();
			$this->qb_cache = FALSE;
		}
	}

	/*

		쿼리빌더 직접 실행하는 방법으로 교체

	// 쿼리빌더를 직접 사용했을 경우, 해당 where 를 가져와서 현재 where 에 합친다
	// _database 에 입력하는 순간 처리 되므로, 두번 중복됨 --.
	protected function get_query_builder_where() {
		$qb_wheres = getProtectedMember( $this->_database, 'qb_where' );
		$this->_database->reset_query();

		foreach( (array) $qb_wheres as $where ) :
			$where_count = count( $where );
			if( $where_count ) :
				$new_where = array();
				foreach( (array) $where as $key => $row ) :
					$new_where[] = $row;
				endforeach;
				// kmh_print( $new_where );
				$this->qb_functions->where[] = $new_where;
			endif;
		endforeach;
	}
	*/

	public function qb_where_count() {
		$where_count = 0;
		foreach( (array) $this->qb_functions as $function_group ) :
			foreach( (array) $function_group as $function => $args ) :
				if(
					strpos($function, 'where')!==FALSE ||
					strpos($function, 'like')!==FALSE
					)
					$where_count++;
			endforeach;
		endforeach;

		return $where_count;
	}

	public function qb_set_count() {
		$set_count = 0;
		foreach( (array) $this->qb_functions as $function_group ) :
			foreach( (array) $function_group as $function => $args ) :
				if( strpos($function, 'set')!==FALSE )
					$set_count++;
			endforeach;
		endforeach;

		return $set_count;
	}

	// 쿼리빌더 펑션들 - 추후 인보크로 교체 고려

		public function group_start() {
			$args = func_get_args();
			if( !count($args) )	$args = array();
			$this->qb_functions[] = array( __FUNCTION__ => $args );
			return $this;
		}

		public function group_end() {
			$args = func_get_args();
			if( !count($args) )	$args = array();
			$this->qb_functions[] = array( __FUNCTION__ => $args );
			return $this;
		}

		public function select() {
			$args = func_get_args();
			$this->qb_functions[] = array( __FUNCTION__ => $args );
			return $this;
		}

		public function set() {
			$args = func_get_args();
			$this->qb_functions[] = array( __FUNCTION__ => $args );
			return $this;
		}

		public function join() {
			$args = func_get_args();
			$this->qb_functions[] = array( __FUNCTION__ => $args );
			return $this;
		}

		public function like() {
			$args = func_get_args();
			$this->qb_functions[] = array( __FUNCTION__ => $args );
			return $this;
		}

		public function or_like() {
			$args = func_get_args();
			$this->qb_functions[] = array( __FUNCTION__ => $args );
			return $this;
		}

		public function where() {
			$args = func_get_args();
			$this->qb_functions[] = array( __FUNCTION__ => $args );
			return $this;
		}

		public function or_where() {
			$args = func_get_args();
			$this->qb_functions[] = array( __FUNCTION__ => $args );
			return $this;
		}

		public function where_in() {
			$args = func_get_args();
			$this->qb_functions[] = array( __FUNCTION__ => $args );
			return $this;
		}

		public function limit()
		{
			$args = func_get_args();
			$this->qb_functions[] = array( __FUNCTION__ => $args );
			return $this;
		}

		public function order_by()
		{
			$args = func_get_args();
			$this->qb_functions[] = array( __FUNCTION__ => $args );
			return $this;
		}



	/* --------------------------------------------------------------
	 * QUERY BUILDER DIRECT ACCESS METHODS
	 * ------------------------------------------------------------ */


	/* --------------------------------------------------------------
	 * INTERNAL METHODS
	 * ------------------------------------------------------------ */

	/**
	 * Trigger an event and call its observers. Pass through the event name
	 * (which looks for an instance variable $this->event_name), an array of
	 * parameters to pass through and an optional 'last in interation' boolean
	 *
	 * $data는 배열로
	 */
	public function trigger($event, $data = FALSE, $last = TRUE)
	{
		switch ($event) {
			// case 'before_delete':
				// $data = (array)$data;
				// break;
		}

		if (isset($this->$event) && is_array($this->$event))
		{
			foreach ($this->$event as $method)
			{
				if (strpos($method, '('))
				{
					preg_match('/([a-zA-Z0-9\_\-]+)(\(([a-zA-Z0-9\_\-\., ]+)\))?/', $method, $matches);

					$method = $matches[1];
					$this->callback_parameters = explode(',', $matches[3]);
				}

				$data = call_user_func_array(array($this, $method), array($data, $last));
			}
		}

		return $data;
	}

	/**
	 * Run validation on the passed data
	 */
	public function validate($data)
	{
		if($this->skip_validation)
		{
			return $data;
		}

		if(!empty($this->validate))
		{
			foreach($data as $key => $val)
			{
				$_POST[$key] = $val;
			}

			$this->load->library('form_validation');

			if(is_array($this->validate))
			{
				$this->form_validation->set_rules($this->validate);

				if ($this->form_validation->run() === TRUE)
				{
					return $data;
				}
				else
				{
					return FALSE;
				}
			}
			else
			{
				if ($this->form_validation->run($this->validate) === TRUE)
				{
					return $data;
				}
				else
				{
					return FALSE;
				}
			}
		}
		else
		{
			return $data;
		}
	}

	/**
	 * Guess the table name by pluralising the model name
	 */
	private function _fetch_table()
	{
		if ($this->_table == NULL)
		{
			$this->_table = (preg_replace('/(_m|_model)?$/', '', strtolower(get_class($this))));
		}
	}

	/**
	 * Guess the primary key for current table
	 */
	private function _fetch_primary_key()
	{
		if($this->primary_key == NULl)
		{
			$this->primary_key = $this->_database->query("SHOW KEYS FROM `".$this->_table."` WHERE Key_name = 'PRIMARY'")->row()->Column_name;
		}
	}

	/**
	 * Set WHERE parameters, cleverly
	 */
	protected function _set_where($params)
	{
		// kmh_print($params);
		if (count($params) == 1 && is_array($params[0]))
		{
			foreach ($params[0] as $field => $filter)
			{
				if (is_array($filter))
				{
					$this->_database->where_in($field, $filter);
				}
				else
				{
					if (is_int($field))
					{
						$this->_database->where($filter);
					}
					else
					{
						$this->_database->where($field, $filter);
					}
				}
			}
		}
		else if (count($params) == 1)
		{
			$this->_database->where($params[0]);
		}
		else if(count($params) == 2)
		{
			if (is_array($params[1]))
			{
				$this->_database->where_in($params[0], $params[1]);
			}
			else
			{
				$this->_database->where($params[0], $params[1]);
			}
		}
		else if(count($params) == 3)
		{
			$this->_database->where($params[0], $params[1], $params[2]);
		}
		else
		{
			if (is_array($params[1]))
			{
				$this->_database->where_in($params[0], $params[1]);
			}
			else
			{
				$this->_database->where($params[0], $params[1]);
			}
		}
	}

	/**
	 * Return the method name for the current return type
	 */
	protected function _return_type($multi = FALSE)
	{
		$method = ($multi) ? 'result' : 'row';
		return $this->_temporary_return_type == 'array' ? $method . '_array' : $method;
	}
}
