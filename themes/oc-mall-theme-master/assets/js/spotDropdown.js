(function(factory) {
    factory(window.jQuery);
})(function($) {
    let Drop = {};
    let Dropdown = function() {

        function Dropdown()
        {
            Drop = this;

            this.config = {
                hideClass: 'hidden',
                activeClass: 'font-bold'
            }

            this.$$dropdowns = {};

            this.classes = {
                container: '.js-spot-dropdown',
                list: '.js-spot-dropdown-list',
                titleContainer: '.js-spot-dropdown-title-container',
                title: '.js-spot-dropdown-title',
                listItem: '.js-spot-dropdown-list-item',
                input: '.js-spot-dropdown-input',
                form: '.js-spot-dropdown-form'
            }
            this.events = {
                CLICK: 'click'
            }

            this.addonHandlers = {};

        }


        Dropdown.prototype.registerHandler = function (name) {

            var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
            var handler = arguments[2];
            var _data$event = data.event,
                event = _data$event === undefined ? this.events.CLICK : _data$event,
                _data$selector = data.selector,
                selector = _data$selector === undefined ? null : _data$selector;


            if (null == selector) {
                console.warn("Не указан селектор для обработчика");
                console.log("registerHandler [" + name + "]");
                return false;
            }

            Drop.addonHandlers[name] = {
                selector: selector,
                event: event,
                handler: handlerWrapper
            }

            function handlerWrapper(event)
            {
                let $this = $(this);
                let $container = $this.parents(Drop.classes.container);
                let $input = $container.find(Drop.classes.input);
                let $form = $container.find(Drop.classes.form);
                let $items = $container.find(Drop.classes.listItem);
                handler.call(Drop, event, $this, $container, $input, $form, $items);
            }

        }

        Dropdown.prototype.eachHandler = function (turnOn) {
            console.log("eachHandler [ " + (turnOn ? 'ON' : 'OFF') + " ]");
            if (!this.$$dropdowns.length) {
                return;
            }
            for (var key in this.addonHandlers) {
                var _addonHandlers$key = this.addonHandlers[key],
                    event = _addonHandlers$key.event,
                    selector = _addonHandlers$key.selector,
                    handler = _addonHandlers$key.handler;


                if (typeof event == 'function') {
                    event = event.call(this);
                    this.addonHandlers[key].event = event;
                }
                if (typeof selector == 'function') {
                    selector = selector.call(this);
                    this.addonHandlers[key].selector = selector;
                }

                this.$$dropdowns.off(event, selector, handler);
                if (turnOn) {
                    this.$$dropdowns.on(event, selector, handler);
                }
            }
        }

        Dropdown.prototype.getList = function ($container) {
            return $container.find(Drop.classes.list);
        }

        Dropdown.prototype.init = function () {
            this.$$dropdowns = $(Drop.classes.container);

            Drop.eachHandler.call(this,true);

            $(document).on('click', function (e) {

                let $target = $(e.target);
                let $container = $target.parents(Drop.classes.container);

                let isClickInsideElement = $container.length;


                if (!isClickInsideElement) {
                    Drop.closeAll();
                }
            })


        }

        Dropdown.prototype.toggle = function ($container) {
            let $list = Drop.getList($container);
            $list.toggleClass(Drop.config.hideClass);
        }

        Dropdown.prototype.closeAll = function () {
            this.$$dropdowns.find(this.classes.list).addClass(Drop.config.hideClass);
        }


        return Dropdown;
    }();

    My.Classes.Dropdown = Dropdown;

    My.Inst.BranchDropdown = new My.Classes.Dropdown();

    Drop.registerHandler('openDropdown', {
        selector: Drop.classes.titleContainer,
        event: Drop.events.CLICK
    }, function (event, $this, $container) {
        Drop.toggle($container);
    })

    Drop.registerHandler('selectOption', {
        selector:  Drop.classes.listItem,
        event: Drop.events.CLICK
    }, function (event, $this, $container, $input, $form, $items) {
        let id = $this.data('id');
        $input.val(id);
        $items.removeClass(Drop.config.activeClass);
        $this.addClass(Drop.config.activeClass);
        Drop.closeAll();
        $form.submit();
    })


})
