<?
use Hashids\Hashids;
use Phpform\Phpform;

$form = new Phpform();

$hashids = new Hashids();

$text = $_GET['text'];

$id = $hashids->encode( $text );
$numbers = $hashids->decode($text);

kmh_print( $id );
kmh_print( $numbers );
?>

page name : home


<?
	$latest = array(
		'where' => 'news'
	);
?>
<div class="row">
	<div class="col-md-4">
		<?=$this->boards->latest($latest)?>
	</div>
</div>



<?
	$form_config =
	$form->open('post_write_form', $form_action, $form_config );
?>
	<input type="text" name="test" value="<?=$_POST['test']?>">
	<button type="submit">sm</button>
<? $form->close(); ?>

<?


$file = 'somefile.png';
echo $file.' is has a mime type of '.get_mime_by_extension($file);

	// $this->db->where_in('file_id', array(19) );
	// $this->kmh->log( get_protected_member($this->db, 'qb_where'), 'get_protected_member' );


	// $image = "editor/201808/5911d64a2ac90fa7a5d08f427fbd0ce3.jpg";
	// $less = 'news/201808/4c4f89ba80314e5819e29bc6d8f607d7.less';

	// $image_path = $this->config->item('file_path').$image;
	// kmh_print( $this->files->image_resize($image_path, 500, 500) );
	// kmh_print( '====== ');

	/*
		// 쿼리 복사 (유지) 테스트
		// 클론이 프로파일러에게 안간다 프로파일러에 안뜬다 --...
		$this->db->like('test_deleted_at', '2018-08');
		$this->db->where('test_id_copy', null);
		$query = $this->db->from('test');

		// 복사
		$this->now_run_a = clone $query;
		$this->now_run_b = clone $query;

		kmh_print( $query->get()->num_rows() ); // 프로파일러에게 간다

		// now_run_a
		kmh_print( $this->now_run_a->get()->num_rows() );
		kmh_print( $this->now_run_a->last_query() );

		// now_run_b
		$this->now_run_b->set('test_varchar','updated by no');
		kmh_print( $this->now_run_b->update() );
		kmh_print( $this->now_run_b->last_query() );
	*/


	/*
		// 프로파일러 테스트

	kmh_print( 'START' );
    foreach (get_object_vars($this) as $CI_object) {
    	if( is_object($CI_object) && strpos(get_class($CI_object), 'CI_DB') ) {
			kmh_print( get_class($CI_object) );
    	}

        if (is_object($CI_object)
            && is_subclass_of(get_class($CI_object), 'CI_DB')
        ) {
            $dbs[] = $CI_object;
        }
    }
	*/

    // $this->test_model->test_qb();

	$update_data = array();
	$update_data['test_id_copy'] = null;
	$update_data['test_varchar'] = 'id + 5';
	$where = array();
	$blank = false;
	// $where['test_id'] = 2;



	// $result =  $this->test_model->insert( $update_data );
	// $result =  $this->test_model->insert_many( array($update_data, $update_data) );
	// kmh_print( $this->db->last_query() );
	// kmh_print( $result );

	// $result =  $this->test_model
	// 						->set('test_varchar', 'test_id + 1', false);
	// $result =  $this->test_model
	// 						->update(1, $update_data);

	// kmh_print( $this->db->last_query() );
	// kmh_print( $result );

	// $result =  $this->test_model
	// 						->where('test_updated_at', null)
	// 						->set('test_id_copy', 1)
	// 						->get_all();
	//
							// ->delete_by( array('test_id_copy'=>'3', 'test_created_at'=>null) );
							// ->delete_by( 'test_id_copy',3 );
							// ->delete_many( array(14, 1, 5) );

	// kmh_print( $this->db->last_query() );
	// kmh_print( $result );

	// $result =  $this->test_model
	// 						->with_deleted()
	// 						->get(2);

	// kmh_print( $this->db->last_query() );
	// kmh_print( $result );


	// $result = $this->db
	// 				->set('id_copy',10103)
	// 				->where('id', $blank)
	// 				->update('test');

	// kmh_print( $this->db->last_query() );
	// kmh_print( $result );

	/*

	obsever TEST

	before_create	- 데이터배열 (many-개별)
	after_create	- 인서트 아이디	(many-개별)
	before_update - 데이터배열 (many-개별)
	after_update - 	[0]데이터배열, [1] ? 성공여부?
	before_get 	? 아무것도 안옴
	after_get	리딩된 값 (여러개일때 개별)
	before_delete - 키 값	by 조건배열   many 키값 배열
	after_delete 성공여부
	*/
?>

<? // $this->console->log_memory(); ?>
<? // $this->console->log('test'); ?>
<? // $this->console->log('test2'); ?>
<? // $this->console->log_memory( $this ); ?>

<? if( !empty( $data ) ) : // $data ?>
	<? foreach ( $data as $key => $row ) : ?>
		<div>아이템 <?=$key?></div>
	<? endforeach; ?>
<? else : 	// $data ?>
		<div>아이템이 없습니다.</div>
<? endif; 	// $data ?>