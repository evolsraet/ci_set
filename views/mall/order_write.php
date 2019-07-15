<?
	use Phpform\Phpform;
	$phpform_config = array();
	if( $form_view_mode ) $phpform_config['view_mode'] = true;
	$form = new Phpform( $phpform_config );

	// 주문서 초안시 회원정보에서 가져오기
	if( !$order_id && $this->members->is_login() ) :
		$view = new stdClass;

		$view->order_name = $this->logined->mb_name;
		$view->order_tel = $this->logined->mb_mobile;

		$view->order_post = $this->logined->mb_post;
		$view->order_addr1 = $this->logined->mb_addr1;
		$view->order_addr2 = $this->logined->mb_addr2;
	endif;
?>

<div id="order">
<?
	$form_action = "{$this->mall_base_url}/order_{$order_mode}_act";
	$form_config = array(
		'class' => 'form-horizontal',
		'ajax_before'=>"order_write_before",
		'ajax_after'=>"order_write_success",
	);

	// 폼 오픈
	$form->open('order_write_form', $form_action, $form_config );
	$form->input('','hidden','_cart_checked',$_POST['cart_checked']);
?>

	<div class="panel panel-bordered">
		<div class="panel-heading">
			<div class="panel-title">
				<h3>
					주문 정보
					<? if( $order_id ) : ?>
						<small>#<?=$order_id?></small>
					<? endif; ?>
				</h3>
				<p>
					<? if( $order_id ) : ?>
						<?=order_status($view->order_status);?>
			            <span class="label label-default label-outline margin-right-5">
			                <i class="fa fa-clock-o"></i> <?=get_datetime($view->order_created_at)?>
			            </span>
					<? endif; ?>
				</p>
			</div>
		</div>
		<div class="panel-body">
			<? if( $order_id ) : ?>
				<div class="form-group">
					<label class="control-label col-sm-12 col-md-2 text-left">
						주문서 번호
					</label>
					<div class="col-sm-12 col-md-10 view_mode">
						#<?=$view->order_id?>
						<? $form->input('','hidden','order_id',$order_id); ?>
						<? if( $this->members->is_admin() && $view->order_mb_id) : ?>
							<span class="label label-primary label-outline">
								@<?=$view->{ $this->members->auth_field }?>
							</span>
						<? endif; ?>
					</div>
				</div>
			<? endif; ?>

			<?
				if( $this->members->is_admin() ) :
					$form->radio('주문서 상태',order_status(),'order_status',$view->order_status, array('required'=>'required'));
				endif;
			?>
			<?
				$form->input(
					'주문자 성명',
					'text',
					'order_name',
					$view->order_name,
					array('required'=>'required')
					);
				$form->input('주문자 연락처',
					'text',
					'order_tel',
					$view->order_tel,
					array('required'=>'required')
					);
			?>
			<div class="form-group ">
				<label for="order_post"
					class="control-label col-sm-12 col-md-2 text-left"
					>
					주소
				</label>
				<div class="col-sm-12 col-md-10 ">
					<div class="form-inline margin-bottom-5">
						<div class="input-group">
							<? $form->input('우편번호','text','order_post',$view->order_post,array('required'=>"required"),false); ?>
							<? if( !$form_view_mode ) : ?>
								<span class="input-group-btn">
									<button
										type="button"
										class="btn btn-info open_order_postcode"
										>
										&nbsp;<i class="fa fa-map-pin"></i>&nbsp;
									</button>
								</span> <!-- btn -->
							<? endif; ?>
						</div> <!-- inputgroup -->
					</div>	<!-- form-inline -->

					<? if( !$form_view_mode ) : ?>
						<!-- 다음 우편번호 -->
							<?
								$postcode_id = 'order_postcode';
								$postcode_post = 'order_post';
								$postcode_addr1 = 'order_addr1';
								$postcode_addr2 = 'order_addr2';
								include(MODULEPATH . 'daum_postcode.php');
							?>
						<!-- // 다음 우편번호 -->
					<? endif; ?>

					<? $addr_attr = array('required'=>'required', 'class'=>'margin-bottom-5');?>
					<? $form->input('기본주소','text','order_addr1',$view->order_addr1,$addr_attr,false); ?>
					<? $form->input('상세주소','text','order_addr2',$view->order_addr2,$addr_attr,false); ?>
				</div> <!-- col -->
			</div> <!-- form-group -->

			<?
				$form->textarea(
					'요청사항',
					3,
					'order_memo',
					$form_view_mode ? nl2br($view->order_memo) : $view->order_memo
				);
			?>

			<!-- 포인트 사용 -->
			<? if( $order_mode == 'write' && $this->members->is_login() ) : ?>
				<?
					$this->members->update_login_info();
					$form->input('포인트 사용', 'number', 'order_point_use', 0,
								array(
									'max'=>$this->logined->mb_point,
									'min'=>0
								)
							);
				?>
				<p class="help-block">* 사용가능 포인트 : <?=number_format($this->logined->mb_point)?></p>
			<? elseif( $order_mode == 'write' && !$this->members->is_login() ) : ?>
				<div class="alert alert-info text-center">
					회원가입시 정보입력 및 포인트 혜택이 있습니다.
				</div>
			<? endif; ?>
			<!-- End Of 포인트 사용 -->

			<!-- 관리자 -->
			<? if( $this->members->is_admin() ) : ?>
				<? $form->textarea('관리자 전용 메모',3,'order_admin_memo',$form_view_mode ? nl2br($view->order_admin_memo) : $view->order_admin_memo); ?>
				<? $form->input('관리자 조정 금액','text','order_admin_price',$view->order_admin_price); ?>
			<? endif; ?>
			<!-- End Of 관리자 -->
		</div>
	</div>

	<div class="panel panel-bordered">
		<div class="panel-heading">
			<h3 class="panel-title">
				제품정보
			</h3>
		</div>
		<div class="panel-body">
			<?
				include(VIEWPATH . 'mall/modules/order_product.php');
			?>
		</div>
	</div>

	<div class="text-center">
		<? if( $order_mode == 'write' ) : ?>
			<button type="submit" class="btn btn-lg btn-primary">주문하기</button>
		<? elseif( $view->order_id && !$form_view_mode ) : ?>
			<button type="submit" class="btn btn-lg btn-primary">수정</button>
			<? if( $this->members->is_admin() ) : ?>
				<button type="button" class="order_delete btn btn-lg btn-danger"
					data-order_id="<?=$view->order_id?>"
					>
					주문서 삭제
				</button>
			<? endif; ?>
		<? endif; ?>

		<? if( $view->order_status == '100_ask' && !$this->members->is_admin() ) : ?>
			<? if( $form_view_mode ) : ?>
				<a href="<?=$this->mall_base_url?>/order_write/<?=$view->order_id?>" class="btn btn-lg btn-primary">정보수정</a>
			<? endif; ?>
			<button type="button" class="order_cancel btn btn-lg btn-danger" data-order_id="<?=$view->order_id?>">
				주문 취소
			</button>
		<? endif; ?>

		<!-- 뷰모드 전용 -->
		<? if( $form_view_mode ) : ?>
			<button type="button" class="btn btn-lg btn-default" onclick="printJS('order', 'html');">
				프린트
			</button>
		<? endif; ?>
		<!-- End Of 뷰모드 전용 -->

		<a href="<?=$this->mall_base_url?>/order" class="btn btn-lg btn-default">목록</a>

		<!-- End Of ifelse -->
	</div>

<? $form->close(); ?>
</div>

<script>
	$(".order_cancel").click(function(event) {
		if( !confirm('정말 주문을 취소하시겠습니까?') ) return false;

		btn = $(this);
		url = '<?=$this->mall_base_url?>/order_cancel/' + $(btn).attr('data-order_id');

		$.ajax({
			url: url,
			type: 'post',
			dataType: 'json',
			data: { '<?=$this->security->get_csrf_token_name()?>' : '<?=$this->security->get_csrf_hash()?>' },
			beforeSend : function() {
				$(btn).button('loading');
			},
			error : function(request ,status, error) {
				alert('AJAX 통신 중 에러가 발생했습니다.');
				console.log( request.responseText );
				$(btn).button('reset');
			},
			success : function(response, status, request) {
				if( response.status == 'ok' ) {
					swal({
						type: 'success',
						title: response.msg,
					}, function(){
						location.href='<?=$this->mall_base_url?>/order';
					});
				} else {
					alert(response.msg);
					$(btn).button('reset');
				}
			}
		});
	});

	<? if( $this->members->is_admin() ) : ?>
	// 관리자 전용
		$(".order_delete").click(function(event) {
			if( !confirm("주문서 삭제는 주문과 관련된 모든 내용이 삭제됩니다.\n삭제하시겠습니까?") ) return false;
			if( !confirm("이 작업은 취소 할 수 없습니다.\n정말 주문을 삭제하시겠습니까?") ) return false;

			btn = $(this);
			url = '<?=$this->mall_base_url?>/order_delete/' + $(btn).attr('data-order_id');

			$.ajax({
				url: url,
				type: 'post',
				dataType: 'json',
				data: { '<?=$this->security->get_csrf_token_name()?>' : '<?=$this->security->get_csrf_hash()?>' },
				beforeSend : function() {
					$(btn).button('loading');
				},
				error : function(request ,status, error) {
					alert('AJAX 통신 중 에러가 발생했습니다.');
					console.log( request.responseText );
					$(btn).button('reset');
				},
				success : function(response, status, request) {
					if( response.status == 'ok' ) {
						swal({
							type: 'success',
							title: response.msg,
						}, function(){
							location.href='<?=$this->mall_base_url?>/order';
						});
					} else {
						alert(response.msg);
						$(btn).button('reset');
					}
				}
			});
		});
	// 관리자 전용		
	<? endif; ?>
	
	function order_write_before() {
		if( !chkForm('order_write_form') ) {
			console.log('chkForm : ' + chkForm('order_write_form'));
			return false;
		}
		else return true;
	}

	function order_write_success(response, btn) {
		if( response.status == undefined ) {
			console.log( response );
			alert('통신에러');
		} else if( response.status == 'ok' ) {
			swal({
				type: 'success',
				title: response.msg,
			}, function(){
				location.href = response.url;
			});
		} else {
			swal({
				type: 'error',
				title: response.msg
			});
			$(btn).button('reset');
		}
	}
</script>