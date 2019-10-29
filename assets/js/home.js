$('.promo-owlcar').owlCarousel({
    nav: false,
    autoWidth: true,
    autoHeight: false,
    margin: 5,
    dots: false,
    stagePadding: 50,
    responsive: {
        0: {
            items: 2
        },
        568: {
            items: 3
        },
        1024: {
            items: 4
        }
    },
    onTranslated: promoInfo
});

$('.suggestions-owlcar').owlCarousel({
    nav: false,
    autoWidth: true,
    autoHeight: false,
    margin: 5,
    dots: false,
    stagePadding: 50,
    responsive: {
        0: {
            items: 2
        },
        568: {
            items: 3
        },
        1024: {
            items: 4
        }
    },
    onTranslated: sgstInfo
});

var _promoOwlNavPrevBtn = document.querySelector("#homepage #promo-owl-nav-prev");
var _promoOwlNavNextBtn = document.querySelector("#homepage #promo-owl-nav-next");

var _sgstOwlNavPrevBtn = document.querySelector("#homepage #sgst-owl-nav-prev");
var _sgstOwlNavNextBtn = document.querySelector("#homepage #sgst-owl-nav-next");

_promoOwlNavPrevBtn.onclick = function () { $(".promo-owlcar").trigger("prev.owl.carousel"); };
_promoOwlNavNextBtn.onclick = function () { $(".promo-owlcar").trigger("next.owl.carousel"); };

_sgstOwlNavPrevBtn.onclick = function () { $(".suggestions-owlcar").trigger("prev.owl.carousel"); };
_sgstOwlNavNextBtn.onclick = function () { $(".suggestions-owlcar").trigger("next.owl.carousel"); };

function promoInfo(event) {
    return buttonDisplayManager(event, {
        prev: _promoOwlNavPrevBtn,
        next: _promoOwlNavNextBtn
    });
}

function sgstInfo(event) {
    return buttonDisplayManager(event, {
        prev: _sgstOwlNavPrevBtn,
        next: _sgstOwlNavNextBtn
    });
}

function buttonDisplayManager(e, btn) {
    var visibleItems = 0;

    if (e.currentTarget.clientWidth > 550) {
        visibleItems = 4;
    }

    if (e.currentTarget.clientWidth > 313 && e.currentTarget.clientWidth < 550) {
        visibleItems = 3;
    }

    if (e.currentTarget.clientWidth === 313) {
        visibleItems = 2;
    }

    if (e.item.index === 0) {
        if (visibleItems === 2) {
            btn.prev.classList.add("btn-disabled");
        } else {
            btn.prev.style.display = "none";
        }
    } else {
        if (visibleItems === 2) {
            btn.prev.classList.remove("btn-disabled");
        } else {
            btn.prev.style.display = "block";
        }
    }

    if ((e.item.index + visibleItems) === e.item.count) {
        if (visibleItems === 2) {
            btn.next.classList.add("btn-disabled");
        } else {
            btn.next.style.display = "none";
        }
    } else {
        if (visibleItems === 2) {
            btn.next.classList.remove("btn-disabled");
        } else {
            btn.next.style.display = "block";
        }
    }
}