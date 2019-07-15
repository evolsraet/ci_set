<?
	$no_cart_mode = $order_id ? true : false;
	// ifelse
	if( $no_cart_mode ) :
		$carts = $view->order_product;
		$deli_price = $view->order_deli_price;
		$pd_price = $view->order_pd_price;
		if( $view->order_admin_price )	$order_admin_price = $view->order_admin_price;
		$total_price = $view->order_total_price;
	else :
		$this->cart_model->carts = $carts;
		$deli_price = $this->cart_model->deli_price();
		$pd_price = $this->cart_model->pd_price();
		if( $view->order_admin_price )	$order_admin_price = $view->order_admin_price;
		$total_price = $deli_price + $pd_price + $view->order_admin_price;
	endif;
	// End of ifelse
?>
<div class="table-responsive">
	<table class="table table-hover table-striped table_vertical_middle">
		<thead>
			<th class="text-center">#</th>
			<th class="text-center">제품 / 옵션</th>
			<th class="text-center">단가</th>
			<th class="text-center">수량</th>
			<th class="text-center">계</th>
		</thead>
		<tbody>
			<? foreach( (array) $carts as $key => $cart ) : ?>
				<?
					if( $no_cart_mode ) :
						$cart_info = &$cart;
						$product_info = &$cart;
					else :
						$cart_info = &$cart->cart_item;
						$product_info = &$cart->product;
					endif;
				?>
				<tr>
					<td class="text-center"><?=$key + 1?></td>
					<td>
		                <a href="/mall/view/<?=$product_info->pd_id?>"
		                	class="btn btn-link"
		                    data-toggle="tooltip"
		                    title="상품으로 이동"
		                    >
		                    <small>
		                        <i class="fa fa-link"></i>
		                    </small>
		                </a>
						<?=$product_info->pd_name?>
						<? foreach( (array) $product_info->op_options as $option ) : ?>
							<small>
								/ <?=$option->ot_type?> : <?=$option->ot_name?>
							</small>
						<? endforeach; ?>
					</td>
					<td class="text-right">
						<?=number_format($cart_info->op_price_one)?> 원
					</td>
					<td class="text-right">
						<?=number_format($cart_info->op_count)?> EA
					</td>
					<td class="text-right">
						<?=number_format($cart_info->op_price_one* $cart_info->op_count)?> 원
					</td>
				</tr>
			<? endforeach; ?>
		</tbody>
	</table>
</div>

<hr>
<div class="text-right">
	<p>
		제품가격 <span class="h4 margin-left-20"><?=number_format($pd_price)?></span> 원
	</p>
	<p>
		배송비 <span class="h4 margin-left-20"><?=number_format($deli_price)?></span> 원
	</p>
	<? if( $view->order_point_use ) : ?>
		<p>
			포인트 사용 <span class="h4 margin-left-20">-<?=number_format($view->order_point_use)?></span> 원
		</p>
	<? endif; ?>
	<? if( $view->order_admin_price ) : ?>
		<p>
			관리자 조정 금액 <span class="h4 margin-left-20"><?=number_format($view->order_admin_price)?></span> 원
		</p>
	<? endif; ?>
	<p>
		최종 결제 금액 <span class="h3 margin-left-20"><?=number_format($total_price)?></span> 원
	</p>
</div>
