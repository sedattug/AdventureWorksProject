function mask_div(div_id, text) {
    $("#" + div_id).mLoading({
        text: text
    });
}

function unmask_div() {

    $(".mloading").removeClass('active');

}

$(window).on('load', function () {
    // Animate loader off screen
    $(".se-pre-con").fadeOut(1000);
    ;
});