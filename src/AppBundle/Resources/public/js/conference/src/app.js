//import smoothScrollMenu from "./smoothscroll_menu.js";


//var param = new smoothScrollMenu({menuID: "anchor-menu", fixMenu: true});


function showHideMenu(event){
    event.preventDefault();
    document.getElementById('menu_responsive').classList.toggle('-open');
}
var menuSandwich = document.getElementById("menu-sandwich");
menuSandwich.addEventListener('click', function(event){showHideMenu(event)}, false);