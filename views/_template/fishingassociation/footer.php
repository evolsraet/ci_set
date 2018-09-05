<footer id="footer">
	<div class="footer_link">
		<div class="container">
			<ul class="unstyled">
				<li><a href="#">연합회 소개</a></li>
				<li><a href="#">이용약관</a></li>
				<li><a href="#">개인정보처리방침</a></li>
			</ul>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="span3 text-center">
				<img src="<?=TIMG?>/logo_footer.png" alt="">
			</div>
			<div class="span8 offset1">
				<p>
					주소 :  경기 안산시 단원구 대부남동 11-5   Tel : 032-886-1677<br>
					상호 : 전국유료바다낚시터협회    대표자 : 홍길동<br><br>

					COPYRIGHT(C) 2018  SEA FISHING FEDERATION. ALL RIGHTS RESERVED.
				</p>
			</div>

		</div>
	</div>
</footer>


<!-- 외부로그인 -->
<div id="login_modal" class="modal hide fade">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<i class="head_icon icon-unlock-alt"></i>
		<h3>로그인</h3>
		<p class="muted">로그인 페이지입니다.</p>
	</div>
	<div class="modal-body text-center">
			<input name="o_MB_ID" id="o_MB_ID" class="input-block-level" type="text" value="" placeholder="아이디를 입력하세요">
			<input name="o_MB_PW" id="o_MB_PW" class="input-block-level" type="password" value="" placeholder="암호를 입력하세요">
	</div>
	<div class="modal-footer">
		<button type="button" id="login_btn" onclick="o_login_check(this)" class="btn btn-success" data-loading-text="loading">
			로그인
		</button>
	</div>
</div>
<script type="text/javascript">
	function o_login() {
		$("#login_modal").modal('toggle');
	}

	$("[name='o_MB_PW']").bind('keyup',function(e){
	     if( e.which == 13){
	        o_login_check();
	    }
	 });

	function o_login_check() {
		// alert( getCookie('csrf_cookie_name') );
			$.ajax({ // json type
				type : "POST",
				url : '/member/login_act/',
				data : { "<?=$this->security->get_csrf_token_name()?>" : "<?=$this->security->get_csrf_hash()?>", MB_ID : $('#o_MB_ID').val(), MB_PW : $('#o_MB_PW').val() },
				dataType : "json",
				cache : false,
				beforeSend: function(){
					$("#login_btn").button('loading');
				},
				error : function(xhr, textStatus, errorThrown ) {
					alert('AJAX 통신 중 에러가 발생했습니다.'+'\n xhr : '+JSON.stringify(xhr)+'\n status : '+textStatus+'\n error : '+errorThrown);
					$("#login_btn").button('reset');

				},
				success : function(d) {
					if( d.status == "ok" ) {
						location.reload();
					} else {
						alert(d.msg);
						if( d.status == "id_fail" )			$('#o_MB_ID').focus();
						else if( d.status == "pw_fail" )	$('#o_MB_PW').focus();
						$("#login_btn").button('reset');
					}
				}
			});
	}
</script>
<!-- 외부로그인 -->


<div id="kmh_ajax_loading" style="display:none">
	<div style="position: relative; width:100%; height:100%; text-align:center; padding: 20px 0">
		<img src="<?=base_url('/resources/img/common/ajax_loader_gray_64.gif')?>" style="position: relative; margin:0 auto; vertical-align:middle;" alt="loading">
	</div>
</div>
<div id="kmh_ajax_div" style="display:none"></div>
<iframe id="kmh_hidden_frame" name="kmh_hidden_frame" style="border:1px solid #ccc; width:90%; display: none"></iframe>

