var winHeight = window.outerHeight;
var _section = document.querySelector("#admin-container");

document.addEventListener("DOMContentLoaded", function() {
    if (_section.offsetHeight < winHeight) {
        _section.style.height = winHeight + "px";
    }
});