var form = document.querySelector('form');

form.addEventListener("submit", function(){
    var button = form.getElementsByClassName('btn-submit')[0];
    setButtonLoadingEffect(button);
});

var userInfoForm = document.getElementById('changeUserInfoForm');
userInfoForm.addEventListener("submit", function(){
    var button = userInfoForm.getElementsByClassName('btn-submit')[0];
    setButtonLoadingEffect(button);
});

function setButtonLoadingEffect(button){
    const oldWidth = button.offsetWidth;
    button.style.width = oldWidth + 'px';
    button.innerHTML = '<div class="spinner-border text-light spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';
}