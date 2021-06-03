<?
	// 아래 변수 필수
	// $postcode_id = $postcode_id ? $postcode_id : 'daum_postcode';
	// $postcode_post = '';
	// $postcode_addr1 = '';
	// $postcode_addr2 = '';
?>

<? $this->assets->load_js('//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js', false); ?>
<div id="<?=$postcode_id?>_wrap" class="daum_postcode_wrap">
	<img src="//t1.daumcdn.net/localimg/localimages/07/postcode/320/close.png"
		class="foldDaumPostcode"
		alt="접기 버튼"
		>
</div>

<script>
	// 우편번호
	$(function(){
		var <?=$postcode_id?>_wrap = document.getElementById('<?=$postcode_id?>_wrap');

		$("#<?=$postcode_id?>_wrap .foldDaumPostcode").click(function(event) {
			$(<?=$postcode_id?>_wrap).hide();
		});

		$(".open_<?=$postcode_id?>").click(function(event) {
			me = $(this);

	        var currentScroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);
	        new daum.Postcode({
	            oncomplete: function(data) {
	            	// console.log( data );

	                var fullAddr = data.roadAddress; // 최종 주소 변수
	                var extraAddr = ''; // 조합형 주소 변수
	                if(data.addressType === 'R'){
	                    if(data.bname !== ''){
	                        extraAddr += data.bname;
	                    }
	                    if(data.buildingName !== ''){
	                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
	                    }
	                    fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '') + ' ';
	                }
	                $("#<?=$postcode_post?>").val( data.zonecode );
	                $("#<?=$postcode_addr1?>").val( fullAddr );
	            	$("#<?=$postcode_addr2?>").focus();

	                $("#<?=$postcode_id?>_wrap .foldDaumPostcode").click()
	                document.body.scrollTop = currentScroll;

	            },
	            onresize : function(size) {
	                <?=$postcode_id?>_wrap.style.height = size.height+'px';
	            },
	            width : '100%',
	            height : '100%'
	        }).embed(<?=$postcode_id?>_wrap);
	        <?=$postcode_id?>_wrap.style.display = 'block';
		});

		/*
		function foldDaumPostcode() {
			$(<?=$postcode_id?>_wrap).hide();
		}

		function open_<?=$postcode_id?>(me) {
	        var currentScroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);
	        new daum.Postcode({
	            oncomplete: function(data) {
	            	console.log( data );
	                var fullAddr = data.roadAddress; // 최종 주소 변수
	                var extraAddr = ''; // 조합형 주소 변수
	                if(data.addressType === 'R'){
	                    if(data.bname !== ''){
	                        extraAddr += data.bname;
	                    }
	                    if(data.buildingName !== ''){
	                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
	                    }
	                    fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '') + ' ';
	                }
	                $("#<?=postcode_post?>").val( data.zonecode );
	                $("#<?=postcode_addr1?>").val( fullAddr );
	            	$("#<?=postcode_addr2?>").focus();

	                $("#<?=$postcode_id?>_wrap .foldDaumPostcode").click()
	                document.body.scrollTop = currentScroll;

	            },
	            onresize : function(size) {
	                <?=$postcode_id?>_wrap.style.height = size.height+'px';
	            },
	            width : '100%',
	            height : '100%'
	        }).embed(<?=$postcode_id?>_wrap);
	        <?=$postcode_id?>_wrap.style.display = 'block';
		}
		*/
	});
</script>