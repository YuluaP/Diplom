$(document).ready(function () {

    var menuContainer = $('.left_container'),
        mainContainer = $('.main_container'),
           buttonMenu = $('.burger'),
          buttonPopUp = $('.menu_level_1>li');
   
    menuContainer.mouseover(function(){
    	$(this).addClass('activeCont');
    });

    mainContainer.click(function(){
    	menuContainer.removeClass('activeCont');
    });
    /*
	*	Script for burger menu
    */	
    buttonMenu.on("click", function(){
        if(menuContainer.hasClass("activeCont")) {
        	menuContainer.removeClass('activeCont');

        } else {
        	menuContainer.addClass('activeCont');
        }
    });
    /*
    *	Script for pop-up blocks of items menu
    */
    if(buttonPopUp.children("menu_level_2")) {
   		$('.menu_level_2').parent().prepend('<div></div>');
   		buttonPopUp.children('div').addClass('menuBtn');

   		$('.menuBtn').click(function(){
    		$(this).parent().siblings().removeClass('activeLevel2');
    		$(this).parent().toggleClass('activeLevel2');
    	});
    }
});