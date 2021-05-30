My.Inst.BranchDropdown.init();
My.Inst.Checker.init();



$(document).ready(function() {

    function initScrollbar() {
        var $selector = $('.mcs-horizontal-example');

        if(window.innerWidth > 991) {
            $selector.mCustomScrollbar('destroy');
            $selector.mCustomScrollbar({
                axis: "x",
                theme: "light-thin"
            });

        } else {
            $selector.mCustomScrollbar('destroy');
        }
    }

    window.addEventListener('resize', function() {
        initScrollbar()
    })

    initScrollbar();



})




let d = new Date(new Date().toLocaleString("ru-RU", {timeZone: "Europe/Kiev"})); // timezone ex: Asia/Jerusalem


$('.js-mfp-inline').magnificPopup({
    type:'inline',
    removalDelay: 300,
    fixedContentPos: false,
    fixedBgPos: true,
    mainClass: 'zoom-in',
    midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
});
