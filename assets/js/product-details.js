/* il valore dalla variabile è assegnato all'interno della view "product-details.phtml" tramite php. */
prodDetails = prodDetails;

var _carItemsElements = document.querySelectorAll(".carousel-item");
var _carPrevBtn = document.querySelector(".carousel-control-prev");
var _carNextBtn = document.querySelector(".carousel-control-next");
var _owlCarousel = document.querySelector("#prod-details #owl-carousel-cont .owl-carousel");
var _owlCarItemsElements = document.querySelectorAll("#prod-details #img-car-cont .owl-carousel .owl-img-cont .img-canvas");
var _owlNavPrevBtn = document.querySelector('#prod-details #owl-nav-prev');
var _owlNavNextBtn = document.querySelector('#prod-details #owl-nav-next');
var _suggestionOwlCarCont = document.querySelector("#suggestion-owlcar-cont");
var _prodDetailsForm = document.querySelector("#prod-details form");

var _imgs = document.querySelectorAll("#cont1 #img-cont img");
var _imgDetailModal = document.querySelector("#img-modal");
var _modalMainImg = document.querySelector("#img-modal #img");
var _modalNavImgs = document.querySelectorAll("#img-modal #modal-img-nav .img-canvas");
var _closeModalIcon = document.querySelector("#img-modal i");
var promiseArr = [];

var _colors = document.querySelector("#prod-details #colors");
var _colorsSelect_selected = document.querySelector("#prod-details #colors #selected");
var _colorsSelectDropd = document.querySelector("#prod-details #colors #select-dropdown");
var _sizes = document.querySelector("#prod-details #sizes");
var _sizesRadioCont = document.querySelector("#prod-details #sizes #sizes-cont");
var _availability = document.querySelector("#prod-details #availability");
var _prodDetailsIDInput = document.querySelector("#prod-details #h input");
var colorRadioWrappers;

var distinctColors = [];
var distinctSizes = [];

for (var row of prodDetails) {
    for (var column in row) {
        if (column === "Color") {
            if (distinctColors.indexOf(row[column]) !== -1) {
                continue;
            } else {
                distinctColors.push(row[column]);
            }
        }
    }
}

if (!distinctColors[0]) {
    _colors.style.display = "none";

    setSizesAndAvailability(null);
} else {
    distinctColors.forEach(function (color, i) {
        var _radioWrapper = document.createElement("div");
        var _radio = document.createElement("input");

        if (i === 0) {
            _radio.checked = true;
            _colorsSelect_selected.style.backgroundColor = color;
            setSizesAndAvailability(color);
        }

        _radioWrapper.setAttribute("class", "radio-wrapper");
        _radioWrapper.style.backgroundColor = color;

        _radio.setAttribute("type", "radio");
        _radio.setAttribute("name", "Color");
        _radio.setAttribute("value", color);

        _radioWrapper.appendChild(_radio);
        _colorsSelectDropd.appendChild(_radioWrapper);
    });

    var _colorRadioWrappers = document.querySelectorAll("#prod-details #colors #select .radio-wrapper");

    _colorsSelect_selected.onclick = function () {
        _colorsSelectDropd.style.display = "flex";
    };

    _colorRadioWrappers.forEach(function (_radioWrapper) {
        _radioWrapper.addEventListener("click", function (ev) {
            ev.preventDefault();
    
            var color = this.firstElementChild.value;
    
            this.firstElementChild.checked = true;
    
            setSizesAndAvailability(color);
    
            _colorsSelect_selected.style.backgroundColor = color;
    
            _colorsSelectDropd.style.display = "none";
    
            return false;
        });
    });
    
}


function setSizesAndAvailability(color) {
    distinctSizes = [];

    for (var row of prodDetails) {
        for (var column in row) {
            if (row[column] === color) {
                if (distinctSizes.indexOf(row["Size"]) !== -1) {
                    continue;
                } else {
                    distinctSizes.push({
                        size: row["Size"],
                        availability: row["Availability"],
                        productDetailsID: row["ProductDetailsID"]
                    });
                }
            }
        }
    }

    if (!distinctSizes[0]) {
        _sizes.style.display = "none";
    } else {
        _sizesRadioCont.innerHTML = "";
        _availability.innerHTML = "";

        distinctSizes.forEach(function (obj) {
            var _sizeRadioWrapper = document.createElement("div");
            var _sizeRadio = document.createElement("input");
            var _sizeRadioLabel = document.createElement("label");
            var _span = document.createElement("span");
            var _spanTxt = document.createTextNode(obj.size.toUpperCase());

            _span.appendChild(_spanTxt);

            _sizeRadioLabel.appendChild(_span);
            _sizeRadioLabel.setAttribute("for", "size-" + obj.size + "-radio");
            _sizeRadioLabel.setAttribute("class", "custom-label");

            _sizeRadio.setAttribute("class", "custom-control-input radio");
            _sizeRadio.setAttribute("id", "size-" + obj.size + "-radio");
            _sizeRadio.setAttribute("type", "radio");
            _sizeRadio.setAttribute("name", "Size");
            _sizeRadio.setAttribute("value", obj.size);
            _sizeRadio.setAttribute("data-availability", obj.availability);
            _sizeRadio.setAttribute("data-id", obj.productDetailsID);

            if (obj.availability === "black") {
                _sizeRadio.setAttribute("disabled", true);
            }

            _sizeRadioWrapper.appendChild(_sizeRadio);
            _sizeRadioWrapper.appendChild(_sizeRadioLabel);
            _sizeRadioWrapper.setAttribute("class", "size-radio");

            _sizesRadioCont.appendChild(_sizeRadioWrapper);
        });

        _sizeRadios = document.querySelectorAll("#prod-details #sizes .radio");

        _sizeRadios.forEach(function (_sizeRadio) {
            _sizeRadio.addEventListener("click", function (ev) {
                var availability = this.dataset.availability;

                _prodDetailsIDInput.value = this.dataset.id;

                setAvailability(availability);
            });
        });
    }

    function setAvailability(availability) {
        _availability.innerHTML = "";

        var _span = document.createElement("span");
        var spanTxt;

        function setSpanTxt(text) {
            _spanTxt = document.createTextNode(text);
        }

        switch (availability) {
            case "green":
                setSpanTxt("Disponibilità immediata");
                break;
            case "yellow":
                setSpanTxt("Disponibilità tra 2 settimane");
                break;
            case "red":
                setSpanTxt("Disponibilità tra 4 settimane");
                break;
            default:
                break;
        }

        _span.setAttribute("class", availability);
        _span.appendChild(_spanTxt);

        _availability.appendChild(_span);
    }
}

if (_carPrevBtn || _carNextBtn) {
    _carPrevBtn.onclick = function () { setTimeout(function () { setActiveClass("prev") }, 200); };
    _carNextBtn.onclick = function () { setTimeout(function () { setActiveClass("next") }, 200); };
}

function setActiveClass(direction) {
    $(".carousel").carousel("pause");
    _carItemsElements.forEach(function (elem, i) {
        if ((direction === "prev" && i === 0) || (direction === "next" && i === _carItemsElements.length - 1)) {
            return;
        }
        _owlCarItemsElements[i].classList.remove("img-canvas-active");
    });

    _carItemsElements.forEach(function (elem, i) {

        if (elem.classList.contains("active")) {
            if (direction === "prev" && i !== 0) {
                _owlCarItemsElements[i - 1].classList.add("img-canvas-active");
            }

            if (direction === "next" && i !== _carItemsElements.length - 1) {
                _owlCarItemsElements[i + 1].classList.add("img-canvas-active");
            }
        }
    });

    if (direction === "prev") {
        $(".imgs-view-car").trigger("prev.owl.carousel");
    } else {
        $(".imgs-view-car").trigger("next.owl.carousel");
    }
}

$(".carousel").carousel({
    wrap: false,
    interval: false
});

$('.imgs-view-car').owlCarousel({
    items: 3,
    nav: true,
    autoWidth: true,
    autoHeight: true,
    margin: 5,
    dots: false,
    onTranslated: owlNavBtnManager
});

$('.suggestion-owlcar').owlCarousel({
    items: 5,
    nav: false,
    autoWidth: true,
    autoHeight: false,
    margin: 5,
    dots: false
});

var dragging = false;
var totalMovement = 0;

_owlCarousel.addEventListener("mousedown", function () {
    totalMovement = 0;

    this.addEventListener("mousemove", function (e) {
        if (e.movementX > 0) {
            totalMovement += e.movementX;
        }

        if (e.movementX < 0) {
            totalMovement -= e.movementX;
        }
    });
});

_owlCarousel.addEventListener("mouseup", function (e) {
    if (totalMovement > 0) {
        dragging = true;
    } else {
        dragging = false;
    }
});

_owlCarItemsElements.forEach(function (elem, i) {
    elem.onclick = function () {

        if (dragging) {
            return;
        } else {
            _owlCarItemsElements.forEach(function (e) {
                e.classList.remove("img-canvas-active");
            });
            this.classList.add("img-canvas-active");
            (function (j) { $(".carousel").carousel(j) })(i);
            $(".carousel").carousel("pause");
        }
    };
});

_owlNavPrevBtn.onclick = function () { $(".imgs-view-car").trigger("prev.owl.carousel"); };
_owlNavNextBtn.onclick = function () { $(".imgs-view-car").trigger("next.owl.carousel"); };

function owlNavBtnManager(event) {
    if (event.item.index === 0) {
        _owlNavPrevBtn.style.display = "none";
    } else {
        _owlNavPrevBtn.style.display = "block";
    }

    if (event.item.count === event.item.index + 3) {
        _owlNavNextBtn.style.display = "none";
    } else {
        _owlNavNextBtn.style.display = "block";
    }
}

if (window.innerWidth < 1300) {
    _suggestionOwlCarCont.style.width = (window.innerWidth - 1) + "px";
}

window.addEventListener("resize", function () {
    if (window.innerWidth < 1300) {
        _suggestionOwlCarCont.style.width = (window.innerWidth - 1) + "px";
    }
});


/* 1024px = 64em */
if (window.innerWidth < 1024) {
    _imgs.forEach(function (_img) {
        _img.onclick = function () {
            showModal(this);
        };
    });
}

if (window.innerWidth >= 1024) {
    _imgs.forEach(function (_img) {
        var promise = new Promise(function (resolve, reject) {
            $(_img)
                .wrap('<span class="img-carousel-cont" style="display:inline-block"></span>')
                .css('display', 'block')
                .parent()
                .zoom({ callback: function () { resolve(true); } });
        });
        promiseArr.push(promise);
    });

    Promise.all(promiseArr).then(function () {
        _imgs = document.querySelectorAll("#cont1 #img-cont .img-carousel-cont .zoomImg");
        _imgs.forEach(function (_img) {
            _img.onclick = function () {
                showModal(this);
            };
        });
    });
}

_modalNavImgs.forEach(function (_img) {
    _img.onclick = function () {
        changeSelImg(this);
    };
});

_closeModalIcon.onclick = function () {
    document.body.style.overflow = "auto";
    _imgDetailModal.style.display = "";
}

function showModal(_selectedImg) {
    document.body.style.overflow = "hidden";
    _imgDetailModal.style.display = "flex";
    _modalMainImg.setAttribute("src", _selectedImg.src);
}

function changeSelImg(_selectedImg) {
    /* la regexp "regEx" combacia con il seguente formato:  url("/assets/images/my-image_n1.jpeg")  oppure  url('/risorse/immagini/uomo-camicia_12.png')  etc... */
    var regEx = /^url\("|'\/[a-z]+\/[a-z]+\/[\w-]+\.[a-z]+'|"\)$/;
    /* la regexp "regExp" viene utilizzata per estrarre la url da una stringa che combacia con il formato specificato nella regexp "regEx" */
    var regExp = /^url\("|'(?:\/[a-z]+\/[a-z]+\/[\w-]+\.[a-z]+)'|"\)$/;
    var url = _selectedImg.style.backgroundImage.split(regExp)[1];

    _modalMainImg.setAttribute("src", url);

    _modalNavImgs.forEach(function (_img) {
        if (_img.style.backgroundImage === _selectedImg.style.backgroundImage) {
            _img.classList.add("active");
            return;
        }
        _img.classList.remove("active");
    });
}



_prodDetailsForm.onsubmit = function () {
    var _radios = this.querySelectorAll(".radio") || null;
    var _select = this.querySelector(".custom-select") || null;

    var count = 0;
    var fieldError = {
        radio: false,
        select: false
    };

    if (_radios) {
        var _sizeError = this.querySelector("#size-error");

        _radios.forEach(function (elem) {
            if (elem.checked) {
                count++;
            }
        });

        if (count === 0) {
            if (!fieldError["radio"]) {
                _sizeError.classList.remove("remove");
                fieldError["radio"] = true;
            }
        } else {
            _sizeError.classList.add("remove");
            fieldError["radio"] = false;
        }
    }

    if (_select) {
        var _colorError = this.querySelector("#color-error");

        if (_select.value === "Seleziona colore") {
            if (!fieldError["select"]) {
                _colorError.classList.remove("remove");
                fieldError["select"] = true;
            }
        } else {
            _colorError.classList.add("remove");
            fieldError["select"] = false;
        }
    }

    if (fieldError["radio"] || fieldError["select"]) {
        return false;
    } else {
        return true;
    }
};