My.namespace('Classes.Dropdown')
My.namespace('Classes.WorkingHoursChecker')
My.namespace('Inst.BranchDropdown');
My.namespace('Inst.Checker');

My.Classes.WorkingHoursChecker = function () {
    var Hours = {};
    function WorkingHoursChecker()
    {
        Hours = this;
        this.showSpotsDropdown = false;
        this.timeout = 30;
        this.now = new Date();
        this.lastVisitDate = new Date(localStorage.lastActivity);

    }

    WorkingHoursChecker.prototype.check = function () {
        _dbg();
        if (!My.Inst.Storage.exists('lastActivity')) {
            this.showSpotsDropdown = true;
        } else {
            this.showSpotsDropdown = _diff_minutes(this.lastVisitDate, this.now) > this.timeout;
        }
    }

    WorkingHoursChecker.prototype.init = function () {

        this.check();

        if (this.showSpotsDropdown) {
            $.magnificPopup.open({
                items: {
                    src: '#js-mfp-location', // can be a HTML string, jQuery object, or CSS selector
                    type: 'inline'
                }
            });
        }


        localStorage.lastActivity = new Date();
    }

    function _diff_minutes(dt2, dt1)
    {

        var diff =(dt2.getTime() - dt1.getTime()) / 1000;
        diff /= 60;
        return Math.abs(Math.round(diff));

    }

    function _dbg()
    {
        console.log("Последняя активность была: ", _diff_minutes(Hours.lastVisitDate, Hours.now), " минут назад")
    }

    return WorkingHoursChecker;
}();



My.Inst.Checker = new My.Classes.WorkingHoursChecker();

My.Inst.BranchDropdown.init();
My.Inst.Checker.init();


let d = new Date(new Date().toLocaleString("ru-RU", {timeZone: "Europe/Kiev"})); // timezone ex: Asia/Jerusalem


$('.js-mfp-inline').magnificPopup({
    type:'inline',
    removalDelay: 300,
    fixedContentPos: false,
    fixedBgPos: true,
    mainClass: 'zoom-in',
    midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
});


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
