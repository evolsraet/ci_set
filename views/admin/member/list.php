<?
	use Phpform\Phpform;
	$form = new Phpform();

	$list = $this->member_model->get_list();
?>

<div class="member_list">

	<!-- 검색 -->
	<form class="admin_table_search_form" name="admin_table_search_form" method="get">
		<input type="hidden" name="order_field" value="<?=$_GET['order_field']?>">
		<input type="hidden" name="order_value" value="<?=$_GET['order_value']?>">
	
		<div class="row">
			<div class="col-sm-6 margin-bottom-10">
				<div class="row">

					<div class="col-sm-4">
						<?
							// echo $this->kmh
							// 	->set_array(mb_active_state())
							// 	->as_select(
							// 		'mb_active_state',
							// 		$_GET['mb_active_state'],
							// 		$class='form-control',
							// 		$default_text = '활성상태'
							// 	);
						?>
					</div>
				</div>			
			</div>
			<div class="col-sm-6 margin-bottom-10">
				<!-- 검색어 -->
				<div class="input-group">
					<input type="text" 
						class="form-control" 
						name="search_text" 
						placeholder="검색 (ID, 이름)"
						value="<?=$_GET['search_text']?>"
					>
					<span class="input-group-btn">
						<button type="submit" class="btn btn-primary"><i class="icon wb-search" aria-hidden="true"></i></button>
						<a href="<?=$_SERVER['REDIRECT_URL']?>" class="btn btn-default"><i class="icon wb-refresh" aria-hidden="true"></i></a>
					</span>
				</div>						

			</div>
		</div>
	</form>

	<div class="row ">
		<div class="col-sm-6 margin-bottom-20">
			<!-- <button class="open_write_form btn btn-success" type="button">등록</button> -->
		</div>
		<div class="col-sm-6">
			<p class="text-right margin-bottom-10">
				총 <?=number_format($list['total'])?>건
			</p>			
		</div>
	</div>

	<!-- 리스트 -->
	<table class="admin_table table table-bordered table-striped table-hover table-condensed table_vertical_middle">
		<thead>
			<tr>
				<th data-order_field="mb_email">이메일</th>
				<th data-order_field="mb_display">닉네임</th>
				<th data-order_field="mb_created_at">가입일</th>
				<th data-order_field="mb_name">성명</th>
				<th data-order_field="mb_mobile">연락처</th>
				<th>관리</th>
			</tr>
		</thead>
		<tbody>
			<? foreach( (array) $list['list'] as $key => $row ) : ?>
			<tr>
				<td><?=$row->mb_email?></td>
				<td><?=$row->mb_display?></td>
				<td><?=$row->mb_created_at?></td>
				<td><?=$row->mb_name?></td>
				<td><?=$row->mb_mobile?></td>
				<td class="text-center">
					<button type="button"
						class="open_write_form btn btn-sm btn-info"
						data-mb_id="<?=$row->mb_id?>"
						>
						수정
					</button>
				</td>
			</tr>
			<? endforeach; ?>
		</tbody>
	</table>

	<? if( !count( $list['list'] ) ) : ?>
	<div class="text-center">
		내역이 없습니다.
	</div>	
	<? endif; ?>
	
	<!-- 
	<div class="batch_works">
		<div class="row">
			<div class="col-xs-3">
				<select
					id="batch_status"
					class="form-control"
					>
					<option value="">선택 상태 변경</option>
					<? foreach( (array)mb_status() as $key => $row ) : ?>
						<option value="<?=$key?>"><?=$row?></option>
					<? endforeach; ?>
				</select>				
			</div>

		</div>
	</div>
	 -->

	<div class="text-center">
		<?=$list['pagination']?>
	</div>

	<!-- EXCEL -->
		<div class="row">
			<div class="col-xs-6">
				<a href="<?=$this->baseuri?>excel_download" target="kmh_hidden_frame" class="btn btn-default">
					<i class="fa fa-download"></i>
					엑셀 다운로드
				</a>		
			</div> <!-- col -->
		</div> <!-- row -->
	<!-- EXCEL -->

</div>	<!-- page_wrap -->

<!-- 모달에서 사용하기 위해 미리 부름 -->
<? // $this->assets->load_js('http://dmaps.daum.net/map_js_init/postcode.v2.js', false); ?>

<script>
	// 등록 폼 열기
		$(".open_write_form").click(function(){
			var url = '<?=$this->baseuri?>write_form/';
			if( $(this).attr('data-mb_id') )
				url += $(this).attr('data-mb_id');

			$("#kmh_modal_lg .modal-content").load(url, function(){
				$("#kmh_modal_lg").modal();
			});
		});

	$(function(){
		$("#search_mb_biz_id").select2({width: '100%'});
	});

	// $(function(){
	// 	// 타입 버튼
	// 	$(".admin_table_search_form select").change(function(e){
	// 		$(".admin_table_search_form").submit();
	// 	});
	// });
		
</script>