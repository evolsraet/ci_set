<!--
	http-vue-loader IE 에러
-->

<?
	use Phpform\Phpform;
	$form = new Phpform();
?>

<? $this->assets->load_js( "https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.min.js" ); ?>
<? $this->assets->load_js( "https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.min.js" ); ?>
<? $this->assets->add_js( LIB."babel.min.js" ); ?>

<? $this->assets->load_js( "https://unpkg.com/vue" ); // 개발 ?>
<? $this->assets->load_js( "https://unpkg.com/http-vue-loader" ); // 개발 ?>
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
	<div class="table-responsive">
		<table class="table table-striped table_vertical_middle">
			<thead>
				<tr>
					<th class="text-center">
						<div class="checkbox-custom checkbox-primary">
							<input type="checkbox"
								id="cart_checkbox_toggle"
								v-on:click="cart_checkbox_toggle"
								v-model="cart_checkbox_all"
								>
								<!-- v-model="cart_checkbox_all" -->
							<label for="cart_checkbox_toggle"></label>
						</div>
					</th>
					<th colspan="2" class="text-center">제품</th>
					<th class="text-center">단가</th>
					<th class="text-center">수량</th>
					<th class="text-center">금액</th>
				</tr>
			</thead>
			<tbody>
				<tr
					is="cart_item"
					ref="cart_item_ref"
					v-for="(item, index) in carts"
					:item="item"
					:index="index"
					:key="'item-' + index"
					@cart_item_remove="cart_item_remove"
					>
				</tr>
				<tr v-show="!carts.length">
					<th colspan="6" class="text-center">담겨진 상품이 없습니다.</th>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="well">
		<div class="row">
			<div class="col-md-4 grey-800">
				선택 상품<br>총 금액
			</div>
			<div class="col-md-4 calculate">
				<p class="margin-top-10">
					제품금액 ( {{ product_price | add_comma }} )원 +
					배송비 ( {{ deli_price | add_comma }} ) 원
				</p>
			</div>
			<div class="col-md-4 text-right total_price">
				<span class="price">{{ total_price | add_comma }}</span> 원
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-6">
			<button type="button"
				class="begin_order_btn btn btn-lg btn-default btn-outline btn-block"
				v-on:click="cart_order('all')"
				>
				전체 주문하기
			</button>
		</div>
		<div class="col-xs-6">
			<button type="button"
				class="begin_order_btn btn btn-lg btn-primary btn-block"
				v-on:click="cart_order('select')"
				>
				선택 주문하기
			</button>
		</div>
	</div>

	<? $form->open('cart_order_form', '/mall/order_write' ); ?>
		<input type="hidden" name="cart_checked" v-model="cart_checked">
	<? $form->close(); ?>
</div>

<script>
// $(document).ready(function() {
	Vue.filter('add_comma', function (value) {
		return add_comma(value);
	});

	// 앱
	var app = new Vue({
		el: '#cart_page',
		components: {
			'cart_item': httpVueLoader('/views/mall/modules/cart_item.vue')
		},
		data: {
			carts: [],
			cart_checked: '',
			deli_price_original: 0,
			product_price: 0,
			cart_checkbox_all: true,
			check_count: 0,
		},
		watch: {
		},
		computed: {
			total_price: function(){
				sum = 0;
				this.check_count = 0;
				for (var i = this.carts.length - 1; i >= 0; i--) {
					if( this.carts[i]._cart_checkbox ) {
						this.check_count += 1;
						sum += this.carts[i].cart_item.op_price;
					}
				}
				this.product_price = sum;

				return this.product_price + this.deli_price;
			},
			deli_price: function() {
				if( this.carts.length > 0 && this.check_count )
					return this.deli_price_original;
				else
					return 0;
			},
		},
		methods: {
			cart_item_remove: function( index ) {
				console.log( index );
				this.carts.splice( index, 1);
			},
			cart_order: function(type){
				cart_checked_array = [];
				if( type == 'all' ) {
					for (var i = this.carts.length - 1; i >= 0; i--) {
						cart_checked_array.push( Number(this.carts[i].cart_id) );
					}
				} else {
					for (var i = this.carts.length - 1; i >= 0; i--) {
						if( this.carts[i]._cart_checkbox )
							cart_checked_array.push( Number(this.carts[i].cart_id) );
					}
				}

				if( !cart_checked_array.length ) {
					alert('선택된 상품이 없습니다.');
					return false;
				}

				// 장바구니 업데이트
				$.each(this.$refs.cart_item_ref, function(key, row) {
					row.cart_update()
				});

				// 폼 서밋
				this.cart_checked = JSON.stringify(cart_checked_array);
				this.$nextTick(function(){
					document.getElementById('cart_order_form').submit();
				});
			},
			get_data: function(){
				var self = this;

				$.getJSON('/mall/cart_data', function(data) {
					self.carts               = data.carts;
					self.deli_price_original = data.deli_price;
				});
			},
			cart_checkbox_toggle: function() {
				var self = this;
				this.$nextTick(function(){
					$.each( self.carts, function(key, row) {
						row._cart_checkbox = self.cart_checkbox_all;
					});
				});
			},
		},
		mounted: function () {
			this.get_data();
			// this.$nextTick(function () {
			// 	// 모든 화면이 렌더링된 후 실행합니다.
			// })
		}
	});
// });
</script>