$(function() {
    $(".burger-menu-icon").click(function() {
        $('body').toggleClass('m-menu-opened');


        // // init
        // var menu_main = $(".menu--main");
        // var menu_second = $(".menu--submenu");
        // var margin = menu_main.width();
        //
        // if ( menu_second.length > 0 ){
        //     $(".menu-wr-mob").css('left', -margin * 2 + 'px');
        //     $(".menu-wr-mob").css('width', margin * 2 + 'px');
        //     margin = margin*2;
        // }else{
        //     $(".menu-wr-mob").css('left', -margin + 'px');
        //     $(".menu-wr-mob").css('width', margin + 'px');
        // }
        // // end init
        // if ( $(".wr").css("marginLeft") == "0px"){
        //     $(".wr").css("marginLeft", margin + 'px');
        // }else{
        //     $(".wr").css("marginLeft", "0px");
        // }

    });
    $(window).resize(function () {
        // if( document.body.clientWidth>1069 ) {
        //     $(".wr").css("marginLeft", "0px");
        //     $(".menu-wr-mob").css({left: 0, width: '100%'});
        // } else {
        //     $(".menu-wr-mob").css({left: '-260px', width: '260px'});
        // }
    });
});
