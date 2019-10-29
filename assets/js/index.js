var height = window.outerHeight;
var navHeight = document.querySelector("#top-navbar").offsetHeight;
var _section = document.querySelector("#main-container");
var _mainScript = document.querySelector("#main-script");
var scripts = document.querySelectorAll("script");
var viewHeight = height - navHeight;

document.addEventListener("DOMContentLoaded", function() {
    if (_section.offsetHeight < viewHeight) {
        _section.style.height = viewHeight + "px";
    }
});