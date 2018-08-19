class smoothscroll_helper{

    //var smoothScroll = require('smoothscroll');

    _getTop (element) {
        // return value of html.getBoundingClientRect().top ... IE : 0, other browsers : -pageYOffset
        if(element.nodeName === 'HTML') return -window.pageYOffset;
        return element.getBoundingClientRect().top + window.pageYOffset;
    };
// ease in out function thanks to:
// http://blog.greweb.fr/2012/02/bezier-curve-based-easing-functions-from-concept-to-implementation/
    _easeInOutCubic (t) { return t<.5 ? 4*t*t*t : (t-1)*(2*t-2)*(2*t-2)+1 };

// calculate the scroll position we should be in
// given the start and end point of the scroll
// the time elapsed from the beginning of the scroll
// and the total duration of the scroll (default 500ms)
    _position (start, end, elapsed, duration) {
        if (elapsed > duration) return end;
        return start + (end - start) * this._easeInOutCubic(elapsed / duration); // <-- you can change the easing funtion there
        // return start + (end - start) * (elapsed / duration); // <-- this would give a linear scroll
    };

    smoothScroll (el, duration, callback, context){
        duration = duration || 500;
        context = context || window;
        var start = window.pageYOffset;

        if (typeof el === 'number') {
            var end = parseInt(el);
        } else {
            var end = this._getTop(el);
        }

        var clock = Date.now();
        var requestAnimationFrame = window.requestAnimationFrame ||
            window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame ||
            function(fn){window.setTimeout(fn, 15);};

        var step = () => {
            var elapsed = Date.now() - clock;
            if (context !== window) {
                context.scrollTop = this._position(start, end, elapsed, duration);
            }
            else {
                window.scroll(0, this._position(start, end, elapsed, duration));
            }

            if (elapsed > duration) {
                if (typeof callback === 'function') {
                    callback(el);
                }
            } else {
                requestAnimationFrame(step);
            }
        };
        step();
    };
}

export default smoothscroll_helper;