<?
	use Phpform\Phpform;
	$form = new Phpform();

	$files = $this->file_model
				->where('file_rel_type','product')
				->where('file_rel_id', $this->uri->segment(3))
				->where('file_rel_desc', 'pd_img')
				->get_all();

	$options = $this->option_model->get_by_type($view->pd_id);
?>

<input type="hidden" id="board_base" value="<?=$this->board_base?>">

<div id="product_view">
	<div class="row margin-top-10">
		<div class="col-md-6">
			<!-- 이미지 유무 -->
			<? if( count($files) ) : ?>
				<div class="pd_image_slider">
				<? foreach( (array) $files as $key => $file ) : ?>
					<div>
						<img src="<?=$file->web_path?>" alt="image: <?=$file->file_name?>" style="margin: 0 auto;">
					</div>
				<? endforeach; ?>
				</div>
			<? else : ?>
				<img src="<?=$this->files->no_image(500,500)?>" alt="no image">
			<? endif; ?>
			<!-- End Of 이미지 유무 -->
			<script>
				$(function(){
					$('.pd_image_slider').slick({
						dots: true,
						speed: 500,
						infinite: true,
						slide: 'div',
						cssEase: 'linear'
					});

				})
			</script>
		</div> <!-- colmd4 -->
		<div class="col-md-6">
			<?
				$form_action = '/mall/begin_order';
				$form_config = array(
					'ajax_before'=>'begin_order_before',
					'ajax_after'=>'begin_order_success',
				);
				// 폼 오픈
				$form->open('begin_order_form', $form_action, $form_config );
				$form->input('','hidden', 'pd_id', $view->pd_id);
				$form->input('','hidden', '_options', '');
				$form->input('','hidden', '_order_type', '');
			?>
			<span class="label label-primary"><?=$view->cate_fullname?></span>
			<h3>
				<?=$view->pd_name?>
			</h3>
			<hr>
			<ul class="product_metas list-unstyled">
				<li class="row">
					<div class="col-xs-3 title">가격</div>
					<div class="col-xs-9 desc">
						<?=number_format($view->pd_price)?> 원
						<input type="hidden" id="pd_price" value="<?=$view->pd_price?>">
					</div>
				</li>
				<li class="row">
					<div class="col-xs-3 title">최소주문</div>
					<div class="col-xs-9 desc">
						<?=number_format($view->pd_min)?> 개
						<input type="hidden" id="pd_min" value="<?=$view->pd_min?>">
					</div>
				</li>
				<? foreach( (array) $options as $option_type => $row ) : ?>
					<li class="row">
						<div class="col-xs-3 title"><?=$option_type?></div>
						<div class="col-xs-9 desc">
							<select class="form-control select2 product_option" required="required" data-ot_type="<?=$option_type?>">
								<? foreach( (array) $row as $option ) : ?>
									<option value="<?=$option->ot_type?>_<?=$option->ot_name?>"
										data-ot_price="<?=$option->ot_price?>"
										data-ot_type="<?=$option->ot_type?>"
										data-ot_name="<?=$option->ot_name?>"
										>
										<?=$option->ot_name?>
										<? if( $option->ot_price ) : ?>
											(<?=$option->ot_price?>원)
										<? endif; ?>
									</option>
								<? endforeach; ?>
							</select>
						</div>
					</li>
				<? endforeach; ?>

				<li class="row">
					<div class="col-xs-3 title">수량</div>
					<div class="col-xs-9 desc">
						<input
							type="text"
							name="buy_count"
							id="buy_count"
							class="form-control asSpinner"
							value="<?=$view->pd_min?>"
							data-plugin="asSpinner"
							data-min="<?=$view->pd_min?>"
							data-max="100000"
							data-looping="false"
							>
					</div>
				</li>
			</ul>

			<div class="well" id="total_price" data-value="">
				<div class="row">
					<div class="col-xs-6">총 합계금액</div>
					<div class="col-xs-6 text-right">
						<strong class="price blue-800"></strong> 원
					</div>
				</div>
			</div> <!-- total_price -->

			<div id="order_buttons">
				<!-- ifelse -->
				<? if( $view->pd_use ) : ?>
					<button type="submit" data-type="cart" class="begin_order_btn btn btn-lg btn-primary btn-block">
						장바구니에 담기
					</button>
				<? else : ?>
					<div class="alert alert-danger text-center">판매중지된 상품입니다.</div>
				<? endif; ?>
				<!-- End Of ifelse -->

			</div>	<!-- order_buttons -->

			<? $form->close(); ?>
		</div> <!-- col-md-8 -->
	</div> <!-- row -->

	<ul class="nav nav-justified nav-tabs nav-tabs-line margin-top-30 margin-bottom-30 detail_tab">
		<li class="active">
			<a href="#tab_product_detail">제품 상세설명</a>
		</li>
		<li class="">
			<a href="#tab_comment">댓글</a>
		</li>
	</ul>	<!-- detail_tab -->

	<div role="tabpanel" class="tab-pane active" id="tab_product_detail">
		<?=$view->pd_detail?>
	</div>	<!-- tab_product_detail -->

	<ul class="nav nav-justified nav-tabs nav-tabs-line margin-top-30 margin-bottom-30 detail_tab">
		<li class="">
			<a href="#tab_product_detail">제품 상세설명</a>
		</li>
		<li class="active">
			<a href="#tab_comment">댓글</a>
		</li>
	</ul>	<!-- detail_tab -->

	<div role="tabpanel" class="tab-pane " id="tab_comment">
		<?=module_comment()?>
	</div> <!-- tab_comment -->

</div> <!-- product_view -->


<script>
//	$(function(){
//		$(".begin_order_btn").click(function(event) {
//			$("#_order_type").val( $(this).attr('data-type') );
//			$("#begin_order_form").submit();
//		});
//	});

	function begin_order_before() {
		return true;
	}

	function begin_order_success(response, btn) {
		if( response.status == undefined ) {
			console.log( response );
			alert('통신에러');
		} else if( response.status == 'ok' ) {
			if( response.type == 'cart' ) {
				$(".cart_count_text").html(
						Number($(".cart_count_text").html()) + 1
				);

				if( !confirm('장바구니 페이지로 이동하시겠습니까?') ) {
					$(btn).button('reset');
					return false;
				}
			}

			location.href = response.url;
		} else {
			alert( response.msg );
			$(btn).button('reset');
		}
	}

	$(function(){
		/*
			#pd_price
			#pd_min
			.product_option []
				data-ot_type
				data-ot_name
				data-ot_price
			#buy_count
		 */

		// 초기화
		var pd_price = parseInt( $("#pd_price").val() );
		var pd_min = parseInt( $("#pd_min").val() );
		var buy_count = parseInt( $("#buy_count").val() );
		var options = [];
		var options_price = 0;
		var total_price = 0;

		calc_total_price();

		// 스피너 change 이벤트 적용
		$(document).on('click', ".spinnerUi-control > span", function(){
			$(this).parent().siblings('input.asSpinner').change();
		});

		// 변수 적용시 재계산
		$("#buy_count, .product_option").change(function(event) {
			// console.log( $("#buy_count").val() );
			calc_total_price();
		});

		// 계산 펑션
		function calc_total_price() {
			options = [];
			options_price = 0;
			total_price = 0;

			$(".product_option").each(function(index, el) {
				var one_option = {};
				one_option.ot_type = $(el).children(':selected').attr('data-ot_type');
				one_option.ot_name = $(el).children(':selected').attr('data-ot_name');
				one_option.ot_price = parseInt( $(el).children(':selected').attr('data-ot_price') );
				options.push(one_option);

				options_price += one_option.ot_price;
			});

			$("#_options").val( JSON.stringify(options) );

			buy_count = parseInt( $("#buy_count").val() );
			total_price = ( pd_price + options_price ) * buy_count;
			$("#total_price .price").html( add_comma(total_price) );
		}
	});
</script>