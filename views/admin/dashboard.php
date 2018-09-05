<?
	$ci =& get_instance();
	$ci->load->model('member_model');
	$ci->load->model('post_model');
	$ci->load->model('comment_model');
?>

<!-- Small boxes (Stat box) -->
<div class="row">
	<div class="col-lg-3 col-xs-6">
		<!-- small box -->
		<div class="small-box bg-aqua">
			<div class="inner">
				<h3>
					<?=number_format( $ci->member_model->count_by() )?>
					<sup>명</sup>
				</h3>
				<p>Members</p>
			</div>
			<div class="icon">
				<i class="fa fa-users"></i>
			</div>
			<a href="#" class="small-box-footer">
				More info <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
	<!-- ./col -->
	<div class="col-lg-3 col-xs-6">
		<!-- small box -->
		<div class="small-box bg-green">
			<div class="inner">
				<h3><?=number_format( $ci->post_model->get_board_count() )?> <sup>개</sup></h3>

				<p>Boards</p>
			</div>
			<div class="icon">
				<i class="fa fa-flag-o"></i>
			</div>
			<a href="/admin/setting/board" class="small-box-footer">
				More info <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
	<!-- ./col -->
	<div class="col-lg-3 col-xs-6">
		<!-- small box -->
		<div class="small-box bg-yellow">
			<div class="inner">
				<h3><?=number_format( $ci->post_model->count_by() )?> <sup>건</sup></h3>

				<p>Posts</p>
			</div>
			<div class="icon">
				<i class="fa fa-files-o"></i>
			</div>
			<a href="#" class="small-box-footer">
				More info <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
	<!-- ./col -->
	<div class="col-lg-3 col-xs-6">
		<!-- small box -->
		<div class="small-box bg-red">
			<div class="inner">
				<h3><?=number_format( $ci->comment_model->count_by() )?> <sup>건</sup></h3>

				<p>Comments</p>
			</div>
			<div class="icon">
				<i class="fa fa-comments-o"></i>
			</div>
			<a href="#" class="small-box-footer">
				More info <i class="fa fa-arrow-circle-right"></i>
			</a>
		</div>
	</div>
	<!-- ./col -->
</div>
<!-- /.row -->


<div class="row">
	<div class="col-md-12">
		<div class="box box-warning">
			<div class="box-header with-border">
				<h3 class="box-title">주간 액티비티</h3>

				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
				</div>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				<div class="chart">
					<canvas id="lineChart" style="height:250px"></canvas>
				</div>
			</div>
			<!-- ./box-body -->

		</div>
		<!-- /.box -->
	</div>
	<!-- /.col -->
</div>
<!-- /.row -->


<!-- <script src="<?=TPATH?>bower_components/chart.js/Chart.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
<script>
		var config = {
			type: 'line',
			data: {
				labels: <?=$activity_key?>,
				datasets: [{
					label: '액티비티 카운트',
					backgroundColor: '#dd4b39',
					borderColor: '#dd4b39',
					data: <?=$activity_count?>,
					fill: false,
				}]
			},
			options: {
				responsive: true,
				tooltips: {
					mode: 'index',
					intersect: false,
				},
				hover: {
					mode: 'nearest',
					intersect: true
				},
				legend: {
					display: false
				},
				scales: {
					xAxes: [{
						display: true,
						// scaleLabel: {
						// 	display: true,
						// 	labelString: '날짜'
						// }
					}],
					yAxes: [{
						display: true,
					}]
				}
			}
		};

		window.onload = function() {
			var ctx = document.getElementById('lineChart').getContext('2d');
			window.myLine = new Chart(ctx, config);
		};
</script>