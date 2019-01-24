<?
	use Phpform\Phpform;
	$form = new Phpform();

	$form_action = '';
	$form_config = array(
		'class'=>'form-horizontal',
		'ajax_before'=>'add_point_before',
		'ajax_after'=>'add_point_success',
	);
?>

<form method="GET">
	<div class="row margin-bottom-30">
		<div class="col-xs-6 col-md-8">
			<? if( $this->members->is_admin() ) : ?>
			<button type="button" class="btn btn-primary add_point_btn">
				추가
			</button>
			<? endif; ?>
		</div>

		<div class="col-xs-6 col-md-4">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Search" name="search" value="<?=$this->input->get('search')?>">
				<div class="input-group-btn">
					<button class="btn btn-primary" type="submit">
						<i class="fa fa-search"></i>
					</button>
				</div>
			</div>
		</div>
	</div>
</form>

<div class="table-responsive">
	<table class="table table-bordered table-hover">
		<thead>
			<tr>
				<th class="text-center">일시</th>
				<th class="text-center">계정</th>
				<th class="text-center">포인트</th>
				<th class="text-center">잔여포인트</th>
				<th class="text-center">내용</th>
			</tr>
		</thead>
		<tbody>
			<? foreach( (array) $list as $key => $row ) : ?>
				<tr>
					<td class="text-center"><?=get_datetime( $row->pt_created_at );?></td>
					<td class="text-center"><?=$row->{$this->members->auth_field}?></td>
					<td class="text-right">
						<?
							// ifelse
							if( $row->pt_amount > 0 ) :
								$class="primary";
							else :
								$class="danger";
							endif;
							// End of ifelse
						?>
						<span class="text-<?=$class?>">
							<?=number_format($row->pt_amount)?>
						</span>
					</td>
					<td class="text-right">
						<?=number_format($row->pt_left_point)?>
					</td>
					<td class="text-center">
						<?=$row->pt_desc?>
					</td>
				</tr>
			<? endforeach; ?>
		</tbody>
	</table>
</div>

<div class="text-center">
	<?=$pagination?>
</div>

<? if( $this->members->is_admin() ) : ?>
<div id="add_point_modal" class="modal fade" role="dialog">

	<? $form->open('add_point_form', $form_action, $form_config ); ?>

	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button"
					class="close"
					data-dismiss="modal"
					aria-label="Close"
					>
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">포인트 추가</h4>
			</div>
			<div class="modal-body">
				<?
					$form->select(
						'계정',
						$members,
						'pt_mb_id',
						'',
						array('required'=>'required', 'data-plugin'=>'select2')
					);
						// array('required'=>'required', 'data-plugin'=>'select2')
						// array('required'=>'required', 'class'=>'select2_plugin')

					$form->input('포인트','number', 'pt_amount', '', array('required'=>'required') );
					$form->input('설명','text', 'pt_desc', '관리자 추가 포인트');
				?>
			</div>
			<div class="modal-footer text-center">
				<button type="submit" class="btn btn-primary">확인</button>
			</div>
		</div>
	</div>

	<? $form->close(); ?>

</div>

<script>
	function add_point_before() {
		if( !chkForm('add_point_form') )	return false;
		else 								return true;
	}

	function add_point_success(response, btn) {
		// console.log( response );
		if( response.status == undefined ) {
			console.log( response );
			alert('통신에러');
			$(btn).button('reset');
		} else if( response.status == 'ok' ) {
			location.reload();
		} else {
			alert( response.msg );
			$(btn).button('reset');
		}
	}

	$(document).ready(function() {
		$(".add_point_btn").click(function(event) {
			$("#add_point_modal").modal();
		});
	});
</script>
<? endif;