$(function(){
    setTimeout(function () {
        var frame = $('iframe'),
            i, j = frame.length;
        for(i=0; i <=j; i++ ){
            var head = $(frame[i]).contents().find('head');
            head.append($("<link/>",
                { rel: "stylesheet", href: "/bundles/app/css/iframe.css", type: "text/css" }));
        }
    },2000);
})


