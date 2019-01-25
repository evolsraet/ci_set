	<!-- KMH common elements  -->
	<div id="kmh_ajax_loading" style="display:none">
		<div style="position: relative; width:100%; height:100%; text-align:center; padding: 20px 0">
			<img src="<?=IMG?>common/ajax_loader_gray_64.gif" style="position: relative; margin:0 auto; vertical-align:middle;" alt="loading">
		</div>
	</div>
	<div id="kmh_ajax_div" style="display:none"></div>
	<iframe id="kmh_hidden_frame" name="kmh_hidden_frame" style="border:1px solid #ccc; width:90%; display: none"></iframe>
	<!-- BS3 modal -->
	<div id="kmh_modal" class="modal fade" tabindex="-1" role="dialog">
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
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body"></div>
				<div class="modal-footer"></div>
			</div>
		</div>
	</div>
	<!-- KMH common elements  -->

	<!-- 애널리틱스 -->
	<? if( $this->uri->segment(1) != 'admin' && $this->config->item('google_analytics') ) : ?>
		<script async src="https://www.googletagmanager.com/gtag/js?id=<?=$this->config->item('google_analytics')?>"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());

			gtag('config', '<?=$this->config->item('google_analytics')?>');
		</script>
		<!-- Global site tag (gtag.js) - Google Analytics -->
	<? endif; ?>
	<!-- End Of 애널리틱 -->
