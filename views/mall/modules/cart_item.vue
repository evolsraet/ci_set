<script type="text/x-template" id="cart_item">
    <tr>
        <td>
            <div class="checkbox-custom checkbox-primary">
                <input type="checkbox"
                    :id="'_cart_checkbox_' + item.cart_id"
                    class="_cart_checkbox"
                    v-model="item._cart_checkbox"
                    >
                <label :for="'_cart_checkbox_' + item.cart_id"></label>
            </div>
        </td>
        <td class="text-center">
            <img class="제품 이미지" :src="item.product.front_image"
                style="max-width: 200px; max-height: 80px"
                >
        </td>
        <td class="product_info">
            <h4>
                {{ item.product.pd_name }}
                <a :href="'/mall/view/' + item.product.pd_id"
                    data-toggle="tooltip"
                    title="상품으로 이동"
                    >
                    <small>
                        <i class="fa fa-link"></i>
                    </small>
                </a>
            </h4>
            <!-- 옵션 -->
            <span v-for="option in item.product.op_options" class="label label-primary margin-right-5">
                {{ option.ot_type }} : {{ option.ot_name }}
            </span>
            <!-- 시간 -->
            <span class="label label-default label-outline margin-right-5">
                <i class="fa fa-clock-o"></i> {{ item.cart_created_at }}
            </span>
            <!-- 세션 장바구니 -->
            <span class="label label-info label-outline margin-right-5"
                v-if="!item.cart_mb_id"
                >
                비회원 장바구니
            </span>
        </td>
        <td class="text-right">
            {{ item.cart_item.op_price_one | add_comma }} 원
        </td>
        <td class="text-center item_count">
            <div class="form-inline">
                <div class="input-group">
                    <input type="number"
                        class="form-control col-md-1"
                        v-on:change="op_count_change"
                        v-model="item.cart_item.op_count"
                        number
                        >
                    <span class="input-group-btn">
                        <button
                            type="button"
                            class="btn btn-success"
                            title="수정"
                            v-on:click="cart_update"
                            >
                            <i class="fa fa-check"></i>
                        </button>
                        <button
                            type="button"
                            class="btn btn-warning"
                            title="삭제"
                            v-on:click="cart_delete"
                            >
                            <i class="fa fa-remove"></i>
                        </button>
                    </span>
                </div>
            </div>
        </td>
        <td class="price_td text-right">
            <!-- item.cart_item.op_price_one * item.cart_item.op_count -->
            <span id="price" data-op_price="">{{ item.cart_item.op_price | add_comma }}</span>
            원
        </td>
    </tr>
</script>

<script>
$(document).ready(function() {

    Vue.component('cart_item', {
        template: '#cart_item',
        props: ['item', 'index'],
        data: function(){
            return {
            };
        },
        methods: {
            cart_item_test: function() {
                this.item.cart_item.op_count++;
            },
            op_count_change: function() {
                var self = this;

                if( self.item.cart_item.op_count < self.item.product.pd_min )
                    self.item.cart_item.op_count = self.item.product.pd_min;

                self.item.cart_item.op_price = self.item.cart_item.op_price_one * self.item.cart_item.op_count;
            },
            cart_update: function() {
                var self = this;
                var url = '/mall/cart_update';

                params = {};
                params.csrf_token         = csrf_hash;
                params.cart_id            = self.item.cart_id;
                params.cart_item_op_count = self.item.cart_item.op_count;
                params.cart_item_op_price = self.item.cart_item.op_count;

                $.post(url, params, function(data, textStatus, xhr) {
                    if( data.status == 'ok' )
                        toastr.success( data.msg );
                    else
                        toastr.error( data.msg );
                });
            },
            cart_delete: function() {
                if( !confirm("삭제 후 복구 불가능합니다. 정말 삭제하시겠습니까?") ) return false;

                var self = this;
                var url = '/mall/cart_delete';

                params = {};
                params.csrf_token = csrf_hash;
                params.cart_id = self.item.cart_id;

                $.post(url, params, function(data, textStatus, xhr) {
                    if( data.status == 'ok' ) {
                        toastr.info( data.msg );
                        self.$emit('cart_item_remove', self.index);
                    } else {
                        toastr.error( data.msg );
                    }
                });
            }
        }
    });

});

</script>