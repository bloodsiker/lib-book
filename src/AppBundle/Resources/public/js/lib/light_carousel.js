'use strict';

(function($) {
    $.fn.Carousel = function (options) {


        var settings = $.extend({
            current: '.current',
            sliderContainer: '.slider__slide-block',
            sliderItem: '.slider__item',
            navLeft: '.nav.nav--left',
            navRight: '.nav.nav--right',
            descriptionBlock: '.slider__description',
            copyrightBlock: false,
            gallery_type: '',
            countBlock: false,
            index: 0
        }, options);
        var elements = {};

        this.each(function() {
            init(this);
        });
        return this;



        function makeVars( carousel ){

            elements.slider = $(carousel);
            elements.sliderItem = $(carousel).find( settings.sliderItem );
            elements.sliderItem = $(elements.sliderItem);
            elements.navLeft = $(carousel).find( settings.navLeft );
            elements.navRight = $(carousel).find( settings.navRight );
            elements.sliderContainer = $(carousel).find( settings.sliderContainer );
            if ( options.descriptionBlock){
                elements.sliderDescription = $(settings.descriptionBlock);
            }else{
                elements.sliderDescription = $(carousel).find( settings.descriptionBlock );
            }
            if ( options.countBlock){
                elements.galleryCount = $(settings.countBlock);
            }
            if ( options.copyrightBlock){
                elements.copyrightBlock = $(settings.copyrightBlock);
            }

        }

        function setImgWidth(carousel){
             $(settings.sliderItem).find("img").css("max-height", $("#gallery_block").height() );
        }

        function init( carousel ){

            makeVars( carousel );

            if (settings.gallery_type){
                setImgWidth( carousel );
                setCurrentCount(0);
            }


            setFirstSlide();

            elements.navLeft.click(function(){

                if ( elements.sliderContainer.find(".current").index() == 0 ){
                    lastSlide('first');
                    return false;
                }
                settings.index = elements.sliderContainer.find(".current").index()-1;
                rollSlider(settings.index);
                if( typeof ga !== "undefined"){
                    ga('send', 'pageview');
                }
            });

            elements.navRight.click(function(){
                if ( elements.sliderContainer.find(".current").index() >= elements.sliderItem.length-1 ){
                    lastSlide('last');
                    return false;
                }
                settings.index = elements.sliderContainer.find(".current").index()+1;
                rollSlider(settings.index);
                if( typeof ga !== "undefined"){
                    ga('send', 'pageview');
                }
            });

            var resizeId;
            $(window).resize(function() {
                clearTimeout(resizeId);
                resizeId = setTimeout(doneResizing, 500);
            });

        }

        function doneResizing(){
            console.log(settings.index);
            rollSlider(settings.index);
        }

        function rollSlider( index ){

            elements.sliderItem.removeClass("current");
            elements.sliderItem.eq(index).addClass("current");
            //elements.sliderContainer.animate({"left": -index* elements.slider.width()}, 500);
            elements.sliderContainer.css({"transform": "translate("+-index* elements.slider.width()+"px, 0)"}, 500);
            setCurrentText (settings.index);
            if ( settings.countBlock ){
                setCurrentCount( settings.index );
            }
            if ( settings.copyrightBlock ){
                setCurrentCopyright( settings.index );
            }
        }

        function lastSlide( param ){
            if (param == 'last') {
                elements.sliderContainer.animate({"left": "-=50px"}, 200).animate({"left": "+=50px"}, 200);
            }
            if (param == 'first') {
                elements.sliderContainer.animate({"left": "+=50px"}, 200).animate({"left": "-=50px"}, 200);
            }
        }

        function setCurrentText(index){
            elements.sliderDescription.html( elements.sliderItem.eq(index).attr('data-description') );
        }
        function setCurrentCount(index){
            elements.galleryCount.html( index+1 + "/" + $(elements.sliderItem).length);
        }
        function setCurrentCopyright(index){
            var copyright = elements.sliderItem.eq(index).attr('data-copyright');
            if (copyright != ""){
                elements.copyrightBlock.html( "Фото: " +  copyright);
            }else{
                elements.copyrightBlock.html( "" );
            }
        }

        function setFirstSlide(){
            if ( settings.index != 0 ){
                elements.navLeft.unbind("click");
                elements.navRight.unbind("click");
                elements.sliderItem.removeClass( "current" );
                elements.sliderItem.eq( settings.index-1 ).addClass( "current" );
                //elements.sliderContainer.css("left", -(settings.index-1) * elements.slider.width());
                console.log(settings.index);
                elements.sliderContainer.css({"transform": "translate("+-(settings.index-1)* elements.slider.width()+"px, 0)"}, 500);
            }else{
                elements.sliderItem.eq( settings.index ).addClass( "current" );
            }
            elements.sliderItem.animate({opacity: 1}, 300);
            setCurrentText (settings.index-1);
            if ( settings.copyrightBlock ){
                setCurrentCopyright (settings.index-1);
            }

            if (settings.gallery_type){
                setCurrentCount(settings.index-1);
            }
        }



    }

}(jQuery));

