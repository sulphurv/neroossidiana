$(document).ready(function () {
    var _sctnBodys = document.querySelectorAll("footer .collapse");
    var _btnConts = document.querySelectorAll("footer .btn-cont");

    var resize = function () {
        if (window.innerWidth > 1300) {
            _sctnBodys.forEach(function (_sctn) {
                _sctn.classList.add("show");
            });
        }

        if (window.innerWidth <= 1300) {
            _sctnBodys.forEach(function (_sctn) {
                _sctn.classList.remove("show");
            });
        }
    }

    function setChevronDirection(elem) {
        _chevron = elem.querySelector("i");
        _btn = elem.querySelector("button");

        setTimeout(function(){
            if (_btn.attributes["aria-expanded"].value == "false") {
                _chevron.classList.remove("rotate");
            } else {
                _chevron.classList.add("rotate");
            }
        }, 100);
    }

    resize();

    window.onresize = function () {
        resize();
        _btnConts.forEach(function (_btnCont) {
            setChevronDirection(_btnCont);
        });
    };

    _btnConts.forEach(function (_btnCont) {
        setChevronDirection(_btnCont);
        _btnCont.onclick = function () { setChevronDirection(this); };
    });
});