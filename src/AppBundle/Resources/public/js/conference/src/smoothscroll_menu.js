'use strict';

import scrollMethods from "./smoothscroll_helper.js";

class smoothScrollMenu{

    constructor (param){

        this.param = {
            menuID: "anchor-menu",
            scrollSpeed: 500,
            fixMenu: false,
            menuHeight: 0
        };
        this.param = Object.assign(this.param, param);
        this.param.menu = document.getElementById(this.param.menuID);
        this.param.menuItems = Array.prototype.slice.call( this.param.menu.querySelectorAll('a[data-anchor]') );



        this.setEvents();

        if( this.param.fixMenu ){
            this.param.menuHeight = document.getElementById (this.param.menuID).offsetHeight;
        }


        this.scrollMethods = new scrollMethods({});
    }


    setEvents(){
        this.param.menuItems.forEach( (el) => {
            el.addEventListener('click', (event) => {
                event.preventDefault();
                this.scrollTo(event.currentTarget);
            })
        });


        var timer = null;
        window.addEventListener('scroll', () => {
            if(timer !== null) {
                clearTimeout(timer);
            }
            timer = setTimeout( () => {
                this.setCurrentMenu();
            }, 150);
        }, false);
    }

    scrollTo(scrollToElement){
        var scrollToY = document.getElementById( scrollToElement.getAttribute('data-anchor')).offsetTop - this.param.menuHeight;
        this.scrollMethods.smoothScroll(scrollToY, 1000, () => { /*this.setCurrentMenu(scrollToElement)*/ document.getElementById('menu').classList.remove('-open'); });
    }

    setCurrentMenu(scrollPosition=window.pageYOffset){


        this.param.menuItems.forEach( (el) => {
            el.classList.remove("-active");
        });

        if (scrollPosition == 0){
            this.param.menuItems[0].classList.add("-active");
            return true;
        }
        if (scrollPosition + window.innerHeight >= document.body.offsetHeight){
            this.param.menuItems[this.param.menuItems.length - 1].classList.add("-active");
            return true;
        }
        this.param.menuItems.forEach(function(el, key, array) {

            console.log(key);

            var sectionId = el.getAttribute('data-anchor');
            var sectionTopPosition = document.getElementById(sectionId).offsetTop - this.param.menuHeight;

            if (scrollPosition < sectionTopPosition){
                if (key == 0){
                    array[key-1].classList.add('-active');
                    return false;
                }
                array[key-1].classList.add('-active');
                return false;
            }else{
                if (key == array.length-1){
                    array[key].classList.add('-active');
                }
            }
            return true;

        });

    }
};

export default smoothScrollMenu;