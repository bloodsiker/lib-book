$(function() {
    var galleryIndex = 1,
        pageHash = document.location.hash;

    if(pageHash.search('#photo-') != -1) {
        galleryIndex = parseInt(pageHash.replace('#photo-', ''));
    }


    if ( $(".gallery .slider").length > 0 ) {
        $(".gallery .slider").Carousel({
            index: galleryIndex,
            gallery_type: "full",
            descriptionBlock: ".gallery__description",
            countBlock: ".gallery__count",
            copyrightBlock: ".gallery__copyright"
        });
    }else if( $(".article-detail .slider").length > 0  ){
        $('.slider .slider__slide-block').on('init', function(event, slick){
            $(this).parent().parent().find('.slider__description').html(
                $(this).find('.slider__item:eq(0)').attr('data-description')
            );
            $(this).parent().parent().find('.slider__copyright').html(
                $(this).find('.slider__item:eq(0)').attr('data-copyright')
            );
        }).slick({
            slidesToShow: 1,
            speed: 200,
            infinite: false,
            prevArrow: '<div class="slick-prev">' +
            '<svg width="28px" height="28px" viewBox="0 0 28 28" version="1.1" xmlns="http://www.w3.org/2000/svg">' +
            '<g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">' +
            '<g id="020-article_gallery" transform="translate(-155.000000, -461.000000)" fill="#000">' +
            '<g id="Group" transform="translate(145.000000, 451.000000)">' +
            '<polygon id="arrow-left" points="13.801039 23 38 23 38 25 13.801039 25 25.4042106 36.6 24.0038278 38 10 24 24.0038278 10 25.4042106 11.4"></polygon></g></g></g></svg></div>',
            nextArrow: '<div class="slick-next">' +
            '<svg width="28px" height="28px" viewBox="0 0 28 28" version="1.1" xmlns="http://www.w3.org/2000/svg" >' +
            '<g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">' +
            '<g id="020-article_gallery" transform="translate(-732.000000, -461.000000)" fill="#000">' +
            '<g id="Group" transform="translate(746.000000, 475.000000) scale(-1, 1) translate(-746.000000, -475.000000) translate(722.000000, 451.000000)">' +
            '<polygon id="arrow-right" points="13.801039 23 38 23 38 25 13.801039 25 25.4042106 36.6 24.0038278 38 10 24 24.0038278 10 25.4042106 11.4"></polygon></g></g></g></svg></div>'
        }).on('afterChange', function(event, slick, currentSlide, nextSlide){
            if( typeof ga !== "undefined"){
                ga('send', 'pageview');
            }
            $(this).parent().parent().find('.slider__description').html(
                $(this).find('.slick-current').attr('data-description')
            );
            $(this).parent().parent().find('.slider__copyright').html(
                $(this).find('.slick-current').attr('data-copyright')
            );
            $(this).parent().parent().find("#slick-slider-current").html( $(this).find('.slick-current').index()+1 );
            $(this).parent().parent().find("#slick-slider-total").html( $(this).find('.slider__item').length );
        });
    } else if ( $(".tag-header .slider").length > 0 ) {
        $('.slider .slider__slide-block').on('init', function(event, slick){
            $(this).parent().parent().find('.slider__description').html(
                $(this).find('.slider__item:eq(0)').attr('data-description')
            );
        }).slick({
            slidesToShow: 1,
            speed: 200,
            infinite: false,
            prevArrow: '<div class="slick-prev"><svg width="52px" height="28px" viewBox="0 0 52 28" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g id="025-tag" transform="translate(-138.000000, -743.000000)" fill="#BF945B"><polygon id="arrow-left" points="141.801039 756 190 756 190 758 141.801039 758 153.404211 769.6 152.003828 771 138 757 152.003828 743 153.404211 744.4"></polygon></g></g></svg></div>',
            nextArrow: '<div class="slick-next"><svg width="52px" height="28px" viewBox="0 0 52 28" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g id="025-tag" transform="translate(-1090.000000, -743.000000)" fill="#BF945B"><polygon id="arrow" points="1138.19896 756 1090 756 1090 758 1138.19896 758 1126.59579 769.6 1127.99617 771 1142 757 1127.99617 743 1126.59579 744.4"></polygon></g></g></svg></div>'
        }).on('afterChange', function(event, slick, currentSlide, nextSlide){
            if( typeof ga !== "undefined"){
                ga('send', 'pageview');
            }
            $(this).parent().parent().find('.slider__description').html(
                $(this).find('.slick-current').attr('data-description')
            );
            $(this).parent().parent().find("#slick-slider-current").html( $(this).find('.slick-current').index()+1 );
            $(this).parent().parent().find("#slick-slider-total").html( $(this).find('.slider__item').length );
        });
    } else {
        $(".slider").Carousel({});

    }


    function showSlider(){
        $("body").removeClass("all_photo");
        $("#one_photo").hide();
        $("#all_photo").show();
    }
    $("#all_photo").click(function(){
        $("body").addClass("all_photo");
        $("#all_photo").hide();
        $("#one_photo").show();
    });
    $("#one_photo").click(function(){
        showSlider();
    });


    $(".gallery-list__item").click(function(){
        var index = $(this).index();
        showSlider();
        $(".gallery .slider").Carousel({
            index: index+1 ,
            gallery_type: "full",
            descriptionBlock: ".gallery__description",
            countBlock: ".gallery__count",
            copyrightBlock: ".gallery__copyright"
        });
    });



});
