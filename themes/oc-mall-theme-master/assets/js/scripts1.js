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
