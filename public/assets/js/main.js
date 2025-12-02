// Toogle left menu effect
document.getElementById("left-menu-toogle-button").addEventListener("click", function(){
    var leftMenu = document.querySelector('.left-menu');
    var contentElement = document.querySelector('.content-wrapper');

    if (leftMenu.style.marginLeft == '-250px'){
        contentElement.style.transition = "all 0.5s";
        contentElement.style.setProperty('margin-left', '250px', 'important');
        leftMenu.style.transition = 'all 0.5s';
        leftMenu.style.setProperty('margin-left', '0px', 'important');
    } else {
        contentElement.style.transition = "all 0.5s";
        contentElement.style.setProperty('margin-left', '0px', 'important');
        leftMenu.style.transition = 'all 0.5s';
        leftMenu.style.setProperty('margin-left', '-250px', 'important');
    }
});