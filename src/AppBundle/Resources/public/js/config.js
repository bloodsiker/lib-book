// Place third party dependencies in the lib folder
//
// Configure loading modules from the lib directory,
// except 'app' ones,
requirejs.config({
    "baseUrl": "/bundles/app/js/lib",
    "paths": {
        "app": "../app",
        jquery: 'jquery-2.1.4.min',
        lightCarousel: "light_carousel"
    },
    "urlArgs": "bust=" +  (new Date()).getTime(),
    "shim": {
        "lightCarousel": ["jquery"]
    }
});



// Load the main app module to start the app
requirejs(["/bundles/app/js/app/fake_ajax.js"]);
requirejs(["/bundles/app/js/app/top_menu.js"]);
requirejs(["/bundles/app/js/app/light_carousel.js"]);