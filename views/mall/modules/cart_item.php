<!-- VUE 로 이전개발 - 개발되지 않은 페이지 -->


<? // kmh_print($cart->cart_item); ?>

<tr>
	<td>
		<!-- 선택 -->
	</td>
	<td class="text-center">
		<img class="제품 이미지" src="<?=$this->files->front_image('product', $cart->product->pd_id, 'pd_img', 500, 400)?>"
			style="max-width: 200px"
			>
	</td>
	<td class="product_info">
		<h4><?=$cart->product->pd_name?></h4>

		<? foreach( (array) $cart->options as $option ) : ?>
			<span class="label label-primary"><?=$option->ot_type?> : <?=$option->ot_name?></span>
		<? endforeach; ?>

		<span class="label label-default label-outline">
			<i class="fa fa-clock-o"></i> <?=$cart->cart_created_at?>
		</span>
		<!-- ifelse -->
		<? if( !$cart->cart_mb_id ) : ?>
			&nbsp;
			<span class="label label-info label-outline">비회원 장바구니</span>
		<? endif; ?>
		<!-- End Of ifelse -->
	</td>
	<td>
		<input
			type="text"
			name="cart_item_count_<?=$cart->cart_id?>"
			id="cart_item_count_<?=$cart->cart_id?>"
			class="form-control asSpinner cart_item_count"
			value="<?=$cart->cart_item->op_count?>"
			data-cart_id="<?=$cart->cart_id?>"
			data-plugin="asSpinner"
			data-min="<?=$cart->product->pd_min?>"
			data-looping="false"
			>
	</td>
	<td class="price_td text-right">
		<span id="price" data-op_price="<?=$cart->cart_item->op_price_one?>">
			<?=number_format($cart->cart_item->op_price)?>
		</span>
		원
	</td>
</tr>