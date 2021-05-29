My.Inst.BranchDropdown.init();
My.Inst.Checker.init();



$(document).ready(function() {
    $(".mcs-horizontal-example").mCustomScrollbar({
        axis:"x",
        theme: "light-thin",
        advanced:{
            autoExpandHorizontalScroll:true //optional (remove or set to false for non-dynamic/static elements)
        }
    });

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
