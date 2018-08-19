$(function() {

    $(".custom-selector-list").mCustomScrollbar({theme: 'dark-2'});

    $(".custom-selector").click(function(e){

        if ( $(window).width() > 670 ){
            $(".custom-selector-list").css('overflow', 'hidden');
            $(this).next().slideToggle( "fast");
        }else{
            $(this).next().fadeToggle( "fast");
            $(this).next().next().fadeToggle( "fast");
            $(this).next().next().next().fadeToggle( "fast");
        }


    });


    $(document).mouseup(function(e){
        var container = $(".custom-selector-wr");

        if (!container.is(e.target) // if the target of the click isn't the container...
            && container.has(e.target).length === 0) // ... nor a descendant of the container
        {
            close_popup();
        }
    });

    $(".custom-selector-list-cover__close").click(function(){
        close_popup();
    });



    function close_popup(){
        $(".custom-selector-list").slideUp('fast');
        $(".custom-selector-list-cover").fadeOut( "fast");
        $(".custom-selector-list-cover__close").fadeOut( "fast");
    }
});