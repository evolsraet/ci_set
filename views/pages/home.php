<div class="alert alert-info text-center">
	welcome HOME
</div>

<script>
	$(function(){
		if (!!window.EventSource) {
			var ev_source = new EventSource('/sse/test');
			toastr.success('SSE on');

			ev_source.addEventListener('message', function(e) {
				if( e.data != 'null' ) {
					data = JSON.parse(e.data);
					console.log( data );
					toastr.info(data.test_varchar);
				}
			}, false);

			ev_source.addEventListener('open', function(e) {
				// 매 연결마다
				// toastr.success('SSE open');
			}, false);

			ev_source.addEventListener('error', function(e) {
				if (e.readyState == EventSource.CLOSED) {
					toastr.error('SSE 에러');
			    	// Connection was closed.
				}
			}, false);

			  ev_source.onerror = function() {
			    console.log("EventSource failed.");
				toastr.error('SSE error');
			  };			
		} else {
			toastr.error('SSE off');
		}
	});
</script>