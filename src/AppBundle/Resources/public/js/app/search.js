$(function() {

    $("#show-search-form").click(function(){
        $("#search-form").addClass("-show");
        $("form input[name='keyword']").focus();
        $(".menu--main .menu__items-wr").css("opacity", 0);
    });


    $("#search-close-icon").click(function(e){
        e.preventDefault();
        $("#search-form").removeClass("-show");
        $("form input[name='keyword']").val('').focus();
        $(".menu--main .menu__items-wr").css("opacity", 1);
    })
});
