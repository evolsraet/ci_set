<?
	// 게시판 글쓰기용 폼 아디는 post_write_form 로 고정한다.
	use Phpform\Phpform;
	$form = new Phpform();
?>



<?
	if( $this->is_update ) :
		$form_action = $board_base.'update_act/'.$this->post_id;
	else :
		$form_action = $board_base.'write_act';
	endif;

	$form_config = array(
		// 'class'=>'form-horizontal',
		'ajax_before'=>'post_write_before',
		'ajax_after'=>'post_write_success',
	);
	// 폼 오픈
	$form->open('post_write_form', $form_action, $form_config );

	// 파일 임시 저장용 코드 (게시글 작성시 필수)
	$form->input('파일 저장 코드','hidden', '_post_files_code',
		$this->files->make_tmp_code( $this->board_id )
	);

	// 업데이트
	$form->input('','hidden', 'post_id', $update->post_id);
	// 부모글
	$form->input('','hidden', 'post_parent',
		$update->post_parent ? $update->post_parent : $reply->post_id
	);

	$form->input('제목','text', 'post_title', $update->post_title, array('required'=>'required') );

	if( $board_info->board_use_secret == 1 )
		$form->checkbox('비밀글', $update->post_is_secret, 'post_is_secret', true, array('without_label'=>true) );

	if( is_board_admin() )
		$form->checkbox('공지사항', $update->post_is_notice, 'post_is_notice', true, array('without_label'=>true) );

	// 답글 작성시거나 이미 답글일때
	if( $this->method != 'reply' && !$update->post_parent ) {
		$form->radio('카테고리',
				get_category($board_info, null),
				'post_category',
				$update->post_category ? $update->post_category : $this->input->get('category'),
				array(
					'without_label'=>true,
					'with_total'=>'선택안함'
				)
			);
	}

	if( !$this->members->is_login() ) :
		$form->input('작성자','text', 'post_writer', $update->post_writer);
		$form->input('비밀번호','password', 'post_password', $update->post_password);
	endif;

	// 본문
	$form->textarea('', 10, 'post_content', $update->post_content, array('placeholder'=>"본문") );

?>

<div id="old_files">
	<!-- 파일 -->
	<section class="board_file">
		<ul class="list-unstyled">
		<? foreach( fe($update->file) as $key => $row ) : ?>
			<li>
				<a href="javascript:download(<?=$row->file_id?>)">
					<i class="fa fa-save"></i>
					<?=$row->file_name?>
				</a>
				<label for="checkbox-inline">
					<input type="checkbox" name="file_delete[]" value="<?=$row->file_id?>">
					삭제
				</label>
			</li>
		<? endforeach; ?>
		</ul>
	</section>
</div>

<div id="post_files">
	<?
		$form->button('<i class="fa fa-save"></i> 파일 추가',"button",
			array(
				'class'=>'btn-default add_file_button',
				'onclick'=>"add_file('_post_files', '#post_files')"
			)
		);
	?>
</div>

<?=reCAPTCHA( $this->config->item('recaptcha_sitekey') )?>

<!-- 버튼 -->
<hr>
<div class="button_wrap">
<?
	echo board_btn('list').PHP_EOL;
	$form->button("전송","submit",array('class'=>'btn-success'));
?>
</div>

<? $form->close(); ?>

<!-- kmh form -->

<script>
	function post_write_before() {
		return true;
	}

	function post_write_success(response, btn) {
		console.log( 'post_write_success' );
		console.log( response );
		if( response.status == undefined ) {
			console.log( response );
			alert('통신에러');
		} else if( response.status == 'ok' ) {
			// $(btn).button('reset');
			location.href = '<?=$board_base?>view/'+response.id;
		} else {
			alert( response.msg );
			$(btn).button('reset');
		}
	}
</script>

