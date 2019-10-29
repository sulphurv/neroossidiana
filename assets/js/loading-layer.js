var loadBackground = document.createElement("div");
loadBackground.id = "load-background-layer";

var spinner = document.createElement("div");
spinner.classList.add("spinner-border");
spinner.style = "width: 3rem; height: 3rem;";

var span = document.createElement("span");
span.classList.add("sr-only");
span.innerText = "Loading...";

spinner.appendChild(span);

loadBackground.appendChild(spinner);

document.body.appendChild(loadBackground);

window.onload = function () {
    document.body.removeChild(loadBackground);
};