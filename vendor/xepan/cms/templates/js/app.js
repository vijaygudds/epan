$(document).ready(function() {

    // Basic Functions
    //â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“
    function windowWidth() {
        var winWidth = $(window).width();
        // console.log(winWidth);
        return winWidth;
    };
    function windowHeight() {
        var winHeight = $(window).height();
        // console.log(winHeight);
        return winHeight;
    };

    // Slider Init
    //â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“
    $('body').css('height', windowHeight());

    // var slider = new Slider();
    $('#main-slider').Slider({
        speed: 1000,
    });

    // Functions onResize
    //â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“â€“
    $(window).resize(function() {
        $('body').css('height', windowHeight());
    });
})
