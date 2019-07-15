<!-- VUE 로 이전개발 - 개발되지 않은 페이지 -->

<? $this->assets->load_js( VIEWPATH . 'mall/assets/cart.js' ); ?>

<!-- login -->
<? if( !$this->members->is_login() ) : ?>
	<div class="alert alert-info text-center">
		비회원의 상태의 장바구니는 최대
		<?=$this->config->item('sess_expiration') / 60 / 60 / 24?>일
		동안만 유지됩니다.
	</div>
<? endif; ?>
<!-- End Of login -->

<div id="cart_page">
	<table class="table table-responsive table_vertical_middle">
		<thead>
			<tr>
				<th class="text-center"></th>
				<th colspan="2" class="text-center">제품</th>
				<th class="text-center">수량</th>
				<th class="text-center">금액</th>
			</tr>
		</thead>
		<tbody>
			<? foreach( (array) $carts as $cart_key => $cart ) : ?>
				<? include( VIEWPATH . 'mall/module/cart_item.php' ); ?>
			<? endforeach; ?>
		</tbody>
	</table>

	<div class="well">
		<div class="row">
			<div class="col-md-4">
				총 금액
			</div>
			<div class="col-md-4 calculate">
				제품금액 (
				<span class="cart_price"></span>
				)원 +
				배송비 (
				<span class="deli_price"></span>
				) 원
			</div>
			<div class="col-md-4 text-right total_price">
				<span class="price"></span> 원
			</div>
		</div>
	</div>
</div>

<script>
	$(function(){
		// // 스피너 change 이벤트 적용
		// $(document).on('click', ".spinnerUi-control > span", function(){
		// 	$(this).parent().siblings('input.asSpinner').change();
		// });

		// $(".cart_item_count").change(function(event) {
		// 	$(this).parents('td').siblings('.price_td');
		// });
	});
</script>