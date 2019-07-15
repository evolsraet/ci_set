<?
	// $this->assets->load_css(LIB . 'remark/vendor/chartist-js/chartist.css');
	// $this->assets->load_css(LIB . 'remark/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.css');
	// $this->assets->load_css('https://cdnjs.cloudflare.com/ajax/libs/chartist/0.11.0/chartist.min.css');

	// $this->assets->load_js(LIB . 'remark/vendor/chartist-js/chartist.js');
	// $this->assets->load_js(LIB . 'remark/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.min.js');
	// $this->assets->load_js('https://cdnjs.cloudflare.com/ajax/libs/chartist/0.11.0/chartist.min.js');
	$this->assets->load_js('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js');
?>

<form id="stat_form" class="form-inline" method="get">
	<div class="row">
		<div class="col-md-6 col-xs-12 margin-bottom-30">
			<div class="input-daterange" data-plugin="datepicker" data-date-format="yyyy-mm-dd">
				<div class="input-group">
					<span class="input-group-addon">
						<i class="icon wb-calendar" aria-hidden="true"></i>
					</span>
					<input type="text"
						class="form-control" 
						name="start"
						placeholder="시작일"
						value="<?=$_GET['start']?>"
						required
						>
				</div>
				<div class="input-group">
					<span class="input-group-addon">to</span>
					<input type="text"
						class="form-control" 
						name="end"
						placeholder="종료일"
						value="<?=$_GET['end']?>"
						required
						>
				</div>
			</div>
		</div>
		<div class="col-md-6 col-xs-12 margin-bottom-30 text-right">
			<div id="stat_sort" class="btn-group">
				<input type="hidden" name="sort" value="<?=$_GET['sort']?>">
				<button type="button" class="btn btn-default <?=is_active($_GET['sort'], 'year')?>" data-sort="year">년도</button>
				<button type="button" class="btn btn-default <?=is_active($_GET['sort'], 'month')?>" data-sort="month">월별</button>
				<button type="button" class="btn btn-default <?=is_active($_GET['sort'], 'week')?>" data-sort="week">주별</button>
				<button type="button" class="btn btn-default <?=is_active($_GET['sort'], 'day')?>" data-sort="day">일별</button>
			</div>	
		</div>
	</div>
</form>

<canvas id="order_stat_chart" class="margin-bottom-30" width="100%"></canvas>

<table id="stat_table" class="table table-hover table-striped">
	<thead>
		<tr>
			<th class="text-center" data-sortable="true">날짜</th>
			<th class="text-center" data-sortable="true">매출액</th>
			<th class="text-center" data-sortable="true">총 주문수</th>
			<th class="text-center" data-sortable="true">완료 주문수</th>
		</tr>
	</thead>
	<tbody>
		<? foreach( (array) $stat as $key => $row ) : ?>
			<tr>
				<td class="text-center"><?=$row->date?></td>
				<td class="text-right"><?=number_format($row->price)?></td>
				<td class="text-right"><?=number_format($row->order_cnt)?></td>
				<td class="text-right"><?=number_format($row->complete_cnt)?></td>
			</tr>
		<? endforeach; ?>
	</tbody>
</table>

<? if( $stat ) : ?>
<!-- 차트 -->
<script>
	$(function(){
		var ctx = document.getElementById('order_stat_chart');
		var order_stat_chart = new Chart(ctx, {
			type: 'line',
			data: {
				labels: [
					<? foreach( (array) $stat as $key => $row ) : ?>
						'<?=$row->date?>',
					<? endforeach; ?>
				],
				datasets: [{
					label: '매출액',
					data: [
						<? foreach( (array) $stat as $key => $row ) : ?>
							<?=$row->price?>,
						<? endforeach; ?>					
					],
					borderColor: 'rgba(255, 99, 132, 1)',
					fill: false,
					borderWidth: 1
				}]
			},
			options: {
				tooltips: {
					callbacks: {
						label: function (tooltipItem, data) {
							// 툴팁 콤마
							var tooltipValue = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
							return parseInt(tooltipValue).toLocaleString();
						}
					}
				},				
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero: true,
							userCallback: function(value, index, values) {
								// 라벨 콤마
								return parseInt(value).toLocaleString();
							}							
						}
					}]
				}
			}
		});
	});
</script>
<!-- 차트 -->
<? endif; ?>

<script>
	$(function(){
		$('#stat_table').bootstrapTable({
		});

		$("#stat_sort button").click(function(event) {
			$("#stat_form [name='sort']").val( $(this).attr('data-sort') );
			$("#stat_form").submit();
		});
	});	
</script>