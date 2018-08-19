$(document).ready(function () {

    $('.scroller__search, .carousel__search').on('click', function (event) {
        changeFashionWeekOrder($(this));
        event.preventDefault();
        return false;
    });

    function changeFashionWeekOrder(target)
    {
        $.ajax({method: 'POST', url: target.attr('data-url'), data: {order: target.attr('data-order')}}).done(function (data) {
            $('.scroller__wrap').html(data);
            $('.scroller__search').off('click');
            $('.scroller__search').on('click', function (event) {
                changeFashionWeekOrder($(this));
            });
        });
    }

    var windowWidth = $(window).width();

    scrollerHeight();
    carousel();

    // make scroller height the same like carousel slide height
    function scrollerHeight(){
        var scrollHeight = $('.carousel__img').height();
        $('.scroller, .scroller__wrap').outerHeight(scrollHeight);
    }

    (function scrollPosition(){
        var scrollHeight = $('.scroller__cursor').height() + 10, // top and bottom offset = 5 + 5 = 10
            $listHeight = $('.scroller__list-wrap').outerHeight(true),
            $wrapHeight = $('.scroller__wrap').height();

        if($listHeight <  $wrapHeight){
            $('.scroller__bar').addClass('is-hide');
        }
        $('.scroller__wrap').scroll(function () {
            var $scrollTop = $(this).scrollTop(),
                $listHeight = $('.scroller__list-wrap').outerHeight(true),
                $wrapHeight = $('.scroller__wrap').height(),
                $scrollCursor = $('.scroller__cursor'),

                scrollSpace = $listHeight - $wrapHeight,
                scrollDistance = ($scrollTop / scrollSpace) * ($wrapHeight - scrollHeight);

            $scrollCursor.css({'top' : scrollDistance + "px"});
        });
    })();

    var scrollHeight = $('.scroller__cursor').height() + 5; // position top == 5px

    $( ".scroller__cursor" ).draggable({
        axis: "y",
        containment : '.scroller',
        refreshPositions : true,
        cursorAt: false,
        tolerance: "pointer",
        drag: function(event, ui) {
            var top                 = ui.position.top,
                $listHeight = $('.scroller__list-wrap').outerHeight(true),
                $wrapHeight = $('.scroller__wrap').height(),
                scrollSpace = $wrapHeight - scrollHeight,
                scrollDistance = ($listHeight - $wrapHeight),
                ratio = scrollDistance / scrollSpace,
                res = top * ratio;

            $('.scroller__wrap').scrollTop(res);
        }
    });

    // video Slider in header
    (function videoSlider() {
        var sliderWidth = 0,
            sliderItemWidth = $('.video-slider__list-item').width(),
            videoBlockWidth = $('.video-slider__list').width(),
            wrapperWidth = $('.video-slider').width(),
            listItemCount = 0,
            offsetLeft = 0,
            blocksDiff = 0,
            activeSlideIndex = 0,
            padding = 14,
            windowWidth = $(window).width();

        $('.video-slider__list').width(getSliderWidth());

        $(window).resize(function () {
            wrapperWidth = $('.video-slider').width();
        });

        $('.video-slider__btn').click(function () {
            if ($(this).hasClass('video-slider__btn--right')) {
                $('.video-slider__list').css({'left': moveSliderTo('next')});
            } else {
                $('.video-slider__list').css({'left': moveSliderTo('prev')});
            }
        });

        $('.video-slider__list-link.player , .video__wrapper__big-preview').click(function (e) {
            var block = $(this).attr('data-from');
            var videoFrameSrc = $(this).attr('href');
            var videoLink = $(this).attr('data-link');
            var videoId = $(this).attr('data-video-id');

            if($(this).hasClass('video-slider__list-link')) {
                var v_title = $(this).closest('.video-slider__list-item').find('.video-slider__title a').text();
                $('.video-main-page .video__title a').attr('href', videoLink).text(v_title);
            }
            $('#'+block).find('.video__wrapper iframe').attr('src', videoFrameSrc);
            $('#'+block).find('.share__wrapper').children().each(function() {
                if (!$(this).hasClass('is-hide') && $(this).attr('data-video-id') !== videoId) {
                    $(this).addClass('is-hide');
                }
                if ($(this).attr('data-video-id') === videoId) {
                    $(this).removeClass('is-hide');
                }
            });
            $('.video__wrapper__big-preview').fadeOut(300);

            return false;
        });

        blocksDiff = -(sliderWidth - videoBlockWidth);

        // width of all li
        function getSliderWidth() {
            $('.video-slider__list-item').each(function () {
                sliderWidth += $(this).outerWidth(true);
            });

            if (sliderWidth < wrapperWidth)
                $('.video-slider__btn--right').addClass('is-hide');
            else
                $('.video-slider__btn--right').removeClass('is-hide');

            return sliderWidth;
        }

        // move slider
        function moveSliderTo(direction) {
            var rightLimit = sliderWidth - wrapperWidth;
            if (direction == 'next') {
                offsetLeft += (sliderItemWidth + padding);
                $('.video-slider__btn--left').removeClass('is-hide');
                if (blocksDiff > -(offsetLeft))
                    $('.video-slider__btn--right').addClass('is-hide');
                if (offsetLeft > rightLimit)
                    offsetLeft = rightLimit;
            } else {
                offsetLeft -= (sliderItemWidth + padding);
                if (offsetLeft <= 0) {
                    $('.video-slider__btn--left').addClass('is-hide');
                    offsetLeft = 0;
                }

                if (blocksDiff < -(offsetLeft))
                    $('.video-slider__btn--right').removeClass('is-hide');
            }

            return -offsetLeft;
        }
    })();
});
$(window).resize(function () {
    carousel();
});
// carousel with 2 slides
function carousel() {
    var $carousel = $('.carousel-main .carousel__list'),
        $carouselItem = $('.carousel-main .carousel__item'),
        $button = $('.carousel-main .carousel__btn'),
        carouselItemWidth = $carousel.find('.carousel__item').outerWidth(true),
        initialPosition = carouselItemWidth * (-2),
        offset = initialPosition,
        carouseLength = $carouselItem.length,
        carouselLengthWithClone = $carouselItem.length + 2,
        maxOffsetRight = carouselLengthWithClone * carouselItemWidth,
        maxOffsetLeft = carouseLength * carouselItemWidth,
        minOffset = 0,
        carouselWidth = 0,
        clonedItemLength = 0,
        windowWidth = $(window).width();




        var $cloneFirstElement = $carouselItem.filter(':first').clone().addClass('cloned');
        var $cloneAfterFirstElement = $($carousel.children()[1]).clone().addClass('cloned');
        var $cloneLastElement = $carouselItem.filter(':last').clone().addClass('cloned');
        var $cloneBeforeLastElement = $($carousel.children()[carouseLength - 2]).clone().addClass('cloned');

        $cloneFirstElement.appendTo('.carousel-main .carousel__list');
        $cloneAfterFirstElement.appendTo('.carousel-main .carousel__list');
        $cloneLastElement.prependTo('.carousel-main .carousel__list');
        $cloneBeforeLastElement.prependTo('.carousel-main .carousel__list');

        clonedItemLength = $('.carousel-main .cloned').length;
        carouselWidth = ($carouselItem.length + clonedItemLength) * carouselItemWidth;

        $carousel.css({'left': initialPosition, 'width': carouselWidth});

        $button.click(function () {
            if ($(this).hasClass('carousel__btn--next')) {
                offset -= carouselItemWidth;
                $carousel.css({'left': offset});
                if (offset == -maxOffsetRight) {
                    offset = initialPosition;
                    $carousel.one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend',
                        function (e) {
                            $carousel.css({'left': offset, 'transition-duration': '0s'});
                        });
                }
            } else {
                offset += carouselItemWidth;
                $carousel.css({'left': offset});
                if (offset == minOffset) {
                    offset = -maxOffsetLeft;
                    $carousel.one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend',
                        function (e) {
                            $carousel.css({'left': offset, 'transition-duration': '0s'});
                        });
                }
            }
            $carousel.css({'transition-duration': '.3s'});
        });
    if(windowWidth < 768){
        carouselWidth = ($carouselItem.length + clonedItemLength) * carouselItemWidth;
        $carousel.css({'width': carouselWidth+40});
    }
}


