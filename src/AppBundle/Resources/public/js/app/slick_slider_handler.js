$('.vogue-slick').slick({
    slidesToShow: 6,
    speed: 200,
    //prevArrow: $(".slick-prev"),
    //nextArrow: $(".slick-next")

    responsive: [
        {
            breakpoint: 1024,
            settings: {
                slidesToShow: 5
            }
        },
        {
            breakpoint: 800,
            settings: {
                slidesToShow: 4
            }
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 3
            }
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 2
            }
        }
    ],
    prevArrow: '<div class="slick-prev"><svg width="50" height="50" viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg"><g fill="#FFF" fill-rule="evenodd"><path d="M3 24h47v2H3z"/><path d="M1.075 25.01L14.51 11.575l1.415 1.415L2.49 26.425z"/><path d="M1.075 24.99L14.51 38.425l1.415-1.415L2.49 23.575z"/></g></svg></div>',
    nextArrow: '<div class="slick-next"><svg width="50" height="50" viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg"><g fill="#FFF" fill-rule="evenodd"><path d="M47 24H0v2h47z"/><path d="M48.925 25.01L35.49 11.575l-1.415 1.415L47.51 26.425z"/><path d="M48.925 24.99L35.49 38.425l-1.415-1.415L47.51 23.575z"/></g></svg></div>'
}).on('afterChange', function(event, slick, currentSlide, nextSlide){
    if( typeof ga !== "undefined"){
        ga('send', 'pageview');
    }
});

