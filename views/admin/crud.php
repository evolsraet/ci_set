<? foreach ($output->css_files as $key => $row) : ?>
	<link rel="stylesheet" href="<?=$row?>">
<? endforeach; ?>

<div class="panel">
	<!--
	<div class="panel-heading">
		<h3 class="panel-title">Panel Heading</h3>
	</div>
	 -->
	<div class="panel-body">
		<?=$output->output?>
	</div>
</div>




<? foreach ($output->js_files as $key => $row) : ?>
	<script src="<?=$row?>"></script>
<? endforeach; ?>

<? if ($this->uri->segment(2)=='sms_') : ?>
	<hr>
	<h3>전체회원 문자발송</h3>
	<div class="input-append">
		<input type="text" class="span6 input-block-level" id="sms_msg" placeholder="내용을 입력하세요.">
		<button type="button" class="send_all btn" data-loading-text="발송">발송</button>
	</div>
<? elseif($this->uri->segment(2)=='member_') : ?>
	<hr>
	<h3>전체회원 쪽지발송</h3>
	<div class="input-append">
		<input type="text" class="span6 input-block-level" id="sms_msg" placeholder="내용을 입력하세요.">
		<button type="button" class="send_all_msg btn" data-loading-text="발송">발송</button>
	</div>
<? endif; ?>


<script type="text/javascript">
$(document).ready(function() {
	$(".send_all").click(function(event) {
		url = '/someting/send_all';
		btn = $(this);

		$.ajax({
			url: url,
			type: 'post',
			dataType: 'json',
			data: { '<?=$this->security->get_csrf_token_name()?>' : '<?=$this->security->get_csrf_hash()?>', msg : $("#sms_msg").val() },
			beforeSend : function() {
				$(btn).button('loading');
			},
			complete : function() {
				$(btn).button('reset');
			},
			error : function(request ,status, error) {
				alert('AJAX 통신 중 에러가 발생했습니다.');
				console.log( request.responseText );
			},
			success : function(response, status, request) {
				if( response.status == 'ok' ) {
					alert(response.msg);
				} else {
					alert(response.msg);
				}
			}
		});
	});
	$(".send_all_msg").click(function(event) {
		url = '/chat/send_all_msg';
		btn = $(this);

		$.ajax({
			url: url,
			type: 'post',
			dataType: 'json',
			data: { '<?=$this->security->get_csrf_token_name()?>' : '<?=$this->security->get_csrf_hash()?>', msg : $("#sms_msg").val() },
			beforeSend : function() {
				$(btn).button('loading');
			},
			complete : function() {
				$(btn).button('reset');
			},
			error : function(request ,status, error) {
				alert('AJAX 통신 중 에러가 발생했습니다.');
				console.log( request.responseText );
			},
			success : function(response, status, request) {
				if( response.status == 'ok' ) {
					alert(response.msg);
				} else {
					alert(response.msg);
				}
			}
		});
	});
});
</script>
