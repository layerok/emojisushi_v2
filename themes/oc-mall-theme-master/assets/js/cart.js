$(function () {
    var $body = $('body');
    $$update = {'cart': '#js-mall-cart'};

    if($('#quickCheckout').length){
        $$update = { ...$$update, 'quickCheckout::cart': '.js-mall-cart-checkout' }
    }

    $.subscribe('mall.discount.applied', refreshCart);
    $.subscribe('mall.shipping.update', refreshCart);
    $.subscribe('mall.address.update', refreshCart);
    //$.subscribe('mall.cart.productAdded', refreshCart);

    function refreshCart () {
        $.request('cart::onRun', {
            update: $$update
        })
    }


    var $count = $('.mall-cart-count');
    var $total = $('.mall-cart-total');
    $.subscribe('products.cart.productAdded', function (e, data) {
        $count.removeClass('invisible').text(data.new_items_count).show();
        $total.text(data.total_post_taxes);
    });
    $.subscribe('mall.cart.productRemoved', function (e, data) {
        $count.text(data.new_items_count);
        $total.text(data.total_post_taxes);
    });

    $.subscribe('mall.cart.update', function (e, data) {
        $total.text(data.total_post_taxes);
        refreshCart();
    });




    $body.on('change', '.js-mall-quantity', function () {
        $.request('cart::onUpdateQuantity', {
            data: {id: this.dataset.id, quantity: this.value},
            update: $$update,
            loading: $.oc.stripeLoadIndicator,
            flash: true,
            success: function (data) {
                this.success(data)
                $.publish('mall.cart.update', data)
            },
            handleFlashMessage: function (message, type) {
                $.oc.flashMsg({text: message, class: type})
            }
        })
    });


    $body.on('click', '.js-mall-add-product', function () {
        $.request('products::onAddToCart', {
            data: {product: this.dataset.product, variant: this.dataset.variant},
            update: $$update,
            loading: $.oc.stripeLoadIndicator,
            flash: true,
            success: function (data) {
                this.success(data)
                $.publish('mall.cart.update', data)
                $.publish('products.cart.productAdded', data)
            },
            handleFlashMessage: function (message, type) {
                $.oc.flashMsg({text: message, class: type})
            }
        })
    });

    $body.on('click', '.js-mall-remove-product', function () {
        $.request('cart::onRemoveProduct', {
            data: {id: this.dataset.id},
            update: $$update,
            loading: $.oc.stripeLoadIndicator,
            success: function (data) {
                this.success(data)
                $.publish('mall.cart.update', data)
                $.publish('mall.cart.productRemoved', data)
            }
        })
    });


    $body.on('click', '.js-wcart-item__remove-notice-open', function(){
        $('.js-wcart-item.is-notice').each(function(){
            $(this).removeClass('is-notice');
        })
        $(this).closest('.js-wcart-item').addClass('is-notice');

    });
    $body.on('click', '.js-wcart-item__remove-notice-close', function(){
        $(this).closest('.js-wcart-item').removeClass('is-notice');
    });

    $body.on('click', '.js-wcart-item__remove', function(){
        $(this).closest('.js-wcart-item').removeClass('is-notice');
    });



    $body.on('click', '.js-wcart-item__collapse-toggle', function(){
        $(this).closest('.js-wcart-item').toggleClass('is-opened');
    });

    $body.on('click', '.js-wcart-spinner__increment', function(){
        console.log('click');
        let $input = $(this).parent().find('.js-wcart-spinner__quantity');
        let $currVal = parseInt($input.val());
        $input.val($currVal + 1);
        $input.trigger('change');
    });

    $body.on('click', '.js-wcart-spinner__decrement', function(){
        let $input = $(this).parent().find('.js-wcart-spinner__quantity');
        let $currVal = parseInt($input.val());
        if($currVal < 2){
            return;
        }
        $input.val($currVal - 1);
        $input.trigger('change');
    });

})
