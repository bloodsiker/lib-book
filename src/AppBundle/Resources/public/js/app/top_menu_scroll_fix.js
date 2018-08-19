$(function() {
    function addFixMenu() {
        if ($(window).width() > 1400) {
            if (menuOffsetTop < $(window).scrollTop()) {

                if (!menuEl.hasClass('-fix')) {
                    menuEl.addClass('-fix');
                    $('main').css('marginTop', menuHeight);
                    $('.hide-submenu .menu--submenu').hide();
                    $('.hide-submenu .menu--main').find('a.-active').removeClass('-has-child');
                }
            } else {
                menuEl.removeClass('-fix');
                $('main').css('marginTop', 0);
                $('.hide-submenu .menu--main').find('a.-active').addClass('-has-child');
                $('.hide-submenu .menu--submenu').show();
            }
        }
    }

    var menuEl = $("#menu-to-fix");
    var menuOffsetTop = menuEl.offset().top;
    var menuHeight = menuEl.height();


    addFixMenu();
    $(document).scroll(function(e){
        if (!menuEl.hasClass('-fix')){
            menuOffsetTop = menuEl.offset().top;
        }
        addFixMenu();
    });



    var resizeTimer;
    $(window).on('resize', function(e) {

        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if ($(window).width() < 1400){
                menuEl.removeClass('-fix');
                $('main').css('marginTop', 0);
            }
        }, 250);

    });

});
