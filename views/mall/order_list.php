<?
	use Phpform\Phpform;
	$form = new Phpform();
?>

<div id="order_list">

	<? if( !$this->members->is_login() ) : ?>
		<div class="well text-center">
			<h4>비회원 주문 검색</h4>
			<hr>
			<?
				$form_config = array(
					'class' => 'form-inline',
					'method' => 'GET',
				);

				// 폼 오픈
				$form->open('order_write_form', $form_action, $form_config );
				$form->input('이름','text','guest_order_search_1',$_SESSION['guest_order_search_1']);
				$form->input('전화번호','text','guest_order_search_2',$_SESSION['guest_order_search_2'],
							array('placeholder'=>'숫자만 입력하세요')
						);
				$form->button('검색', 'submit', array('class'=>'btn btn-primary'));
				$form->close();
			?>
		</div>
	<? endif; ?>

	<table id="order_list"
		class="table_vertical_middle"
		data-toggle="table"
		data-mobile-responsive="true"
		data-pagination="true"
		data-search="true"
		>
		<thead>
			<tr>
				<th data-sortable="true" class="text-center">주문서 번호</th>
				<th data-sortable="true" class="text-center">회원정보</th>
				<th data-sortable="true" class="text-center">주문일시</th>
				<th data-sortable="true" class="text-center">주문정보</th>
				<th data-sortable="true" class="text-center">총 결제금액</th>
				<th data-sortable="true" class="text-center">상태</th>
			</tr>
		</thead>
		<tbody>
		<? foreach( (array) $list as $key => $item ) : ?>
			<tr>
				<td class="sortable text-center">
					<?
						$url = "/{$this->uri->segment(1)}/";
						if( $this->members->is_admin() )
							$url .=	'order_write/';
						else
							$url .=	'order/';
						$url .=	$item->order_id;
					?>
					<a href="<?=$url?>">
						#<?=$item->order_id?>
					</a>
				</td>
				<td class="sortable text-center">
					<? if( $item->order_mb_id) : ?>
						<span class="label label-primary label-outline">
							@<?=$item->{ $this->members->auth_field }?>
						</span>
					<? endif; ?>
				</td>
				<td class="sortable text-center">
		            <span class="label label-default label-outline margin-right-5">
		                <i class="fa fa-clock-o"></i> <?=get_datetime($item->order_created_at)?>
		            </span>
				</td>
				<td class="sortable text-center">
					<? $item_product_count = count($item->order_product) - 1; ?>
					<?=$item->order_product[0]->pd_name?>
					<? if( $item_product_count ) : ?>
					 	외 <?=$item_product_count?> 건
					<? endif; ?>
				</td>
				<td class="sortable text-right">
					<?=number_format($item->order_total_price)?> 원
				</td>
				<td class="sortable text-center">
					<?=order_status($item->order_status)?>
				</td>
			</tr>
		<? endforeach; ?>
		</tbody>
	</table>

</div>