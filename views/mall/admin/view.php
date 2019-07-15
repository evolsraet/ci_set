<?
	// 게시판 글쓰기용 폼 아디는 post_write_form 로 고정한다.
	use Phpform\Phpform;
	$form = new Phpform();
	$categories = $this->category_model->where('cate_depth', 2)->get_all();
	$categories = as_simple_array($categories, 'cate_id', 'cate_fullname');
?>

<!-- 삭제된 -->
<? if( $view->pd_deleted_at ) : ?>
	<div class="alert alert-danger text-center">
		<strong>삭제된 제품입니다.</strong>
	</div>
<? endif; ?>
<!-- End Of 삭제된 -->

<?
	if( $is_update ) :
		$form_action = '/admin/product/update_act/'.$pd_id;
	else :
		$form_action = '/admin/product/write_act';
	endif;

	$form_config = array(
		'class'=>'form-horizontal',
		'ajax_before'=>'write_before',
		'ajax_after'=>'write_success',
	);
	// 폼 오픈
	$form->open('write_form', $form_action, $form_config );

	// 파일 임시 저장용 코드 (게시글 작성시 필수)
	$form->input('파일 저장 코드','hidden', '_post_files_code',
		$this->files->make_tmp_code( 'product' )
	);

	// 업데이트 용
	$form->input('','hidden', 'is_recovery', '');
	$form->input('','hidden', 'pd_id', $view->pd_id);

	// 폼 요소
	$form->input('제품명','text', 'pd_name', $view->pd_name, array('required'=>'required') );
	$form->select('카테고리',
		$categories,
		'pd_cate_id',
		$view->pd_cate_id,
		array('required'=>'required', 'data-plugin'=>'select2')
	);
	$form->radio('사용여부',array(1=>'Y',0=>'N'),'pd_use',$view->pd_use);
	$form->input('기본가격','number', 'pd_price', $view->pd_price);
	$form->input('최소수량','number', 'pd_min', $view->pd_min);

	// 본문
	$form->textarea('상세설명', 10, 'pd_detail', $view->pd_detail, array('placeholder'=>"상세설명") );

?>
<div class="form-group">
	<label class="control-label col-sm-12 col-md-2 control-label text-left">
		대표이미지
	</label>
	<div class="col-sm-12 col-md-10 ">
		<div id="old_files">
			<!-- 파일 -->
			<section class="board_file">
				<ul class="list-unstyled">
				<? foreach( fe($view->file) as $key => $row ) : ?>
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
	</div>
</div>
<div class="form-group">
	<label class="control-label col-sm-12 col-md-2 control-label text-left">
		옵션
	</label>
	<div class="col-sm-12 col-md-10 ">
		<!-- ifelse -->
		<? if( $is_update ) : ?>
			<? include(VIEWPATH . 'mall/modules/admin/option.php'); ?>
		<? else : ?>
			<p class="help-block">* 옵션은 수정 페이지에서만 가능합니다.</p>
		<? endif; ?>
		<!-- End Of ifelse -->
	</div>
</div>

<hr>

<div class="button_wrap text-right">
	<? $form->button("확인","submit",array('class'=>'btn-success')); ?>
	<a href="/admin/product" class="btn btn-default">목록</a>
	<? if( $is_update ) : ?>
		<? if( $view->pd_deleted_at ) : ?>
			<? $form->button("복구","button",array('class'=>'btn-info btn_recover')); ?>
		<? else: ?>
			<? $form->button("삭제","button",array('class'=>'btn-danger btn_del')); ?>
		<? endif; ?>
	<? endif; ?>
</div>

<? $form->close(); ?>

<script>
	function write_before() {
		if( chkForm('write_form') ) return true
	}

	function write_success(response, btn) {
		if( response.status == undefined ) {
			alert('통신에러');
		} else if( response.status == 'ok' ) {
			<? if( $is_update ) : ?>
				location.reload();
			<? else : ?>
				location.href = '/admin/product/view/'+response.id;
			<? endif; ?>
		} else {
			alert( response.msg );
			$(btn).button('reset');
		}
	}

	$(function(){
		// 에디터 초기화
	  	$('#pd_detail').summernote({
	  		lang: 'ko-KR',
	  		height: 300,
			callbacks: {
				onImageUpload: function(files, editor, welEditable) {
		            for (var i = files.length - 1; i >= 0; i--) {
		            	send_file('product', files[i], this);
		            }
		        }
			}
	  	});

	  	$("#write_form .btn_recover").click(function(event) {
	  		$("#is_recovery").val('ok');
	  		$("#write_form").submit();
	  	});

	  	$("#write_form .btn_del").click(function(event) {
			url = '/admin/product/delete_act/<?=$pd_id?>';
			btn = $(this);

			$.ajax({
				url: url,
				type: 'post',
				dataType: 'json',
				data: { '<?=$this->security->get_csrf_token_name()?>' : '<?=$this->security->get_csrf_hash()?>' },
				beforeSend : function() {
					$(btn).button('loading');
				},
				always : function() {
					$(btn).button('reset');
				},
				error : function(request ,status, error) {
					alert('AJAX 통신 중 에러가 발생했습니다.');
					console.log( request.responseText );
				},
				success : function(response, status, request) {
					if( response.status == 'ok' ) {
						location.href = '/admin/product/list'
					} else {
						alert(response.msg);
					}
				}
			});
	  	});

	  	// 서밋 시 에디터 코드로 변환
		$("#write_form").submit(function(){
			$('#pd_detail').html( $('#pd_detail').summernote('code') );
		});
	});
</script>