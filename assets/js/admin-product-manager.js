var _nameInput = document.querySelector("#product-manager #name");

var _genderSelect = document.querySelector("#product-manager #select-gender-cont select");

var _categorySelect = document.querySelector("#product-manager #select-category-cont select");
var _categoryForm = document.querySelector("#product-manager #add-category-cont");
var _newCategoryInput = document.querySelector("#product-manager #select-category-cont #add-new-category");
var _categoryAccordionHeader = document.querySelector("#product-manager #category-accordion-header");

var _typeSelect = document.querySelector("#product-manager #select-type-cont select");
var _typeForm = document.querySelector("#product-manager #add-new-type-form");
var _newTypeInput = document.querySelector("#product-manager #select-type-cont #add-new-type");
var _typeAccordionHeader = document.querySelector("#product-manager #type-accordion-header");

var _priceInput = document.querySelector("#product-manager #price");

var _discountInput = document.querySelector("#product-manager #discount");

var _description = document.querySelector("#product-manager #description");

var _imagesForm = document.querySelector("#product-manager #images-form");
var _thumbImgsCont = document.querySelector("#product-manager #thumb-imgs-cont");

var _productDetails = document.querySelector("#product-manager #product-details");
var _sizeDetailsWrapper = document.querySelector("#product-manager .size-details-wrapper");
var _addSizeDetail = document.querySelector("#product-manager #add-size-detail");

var _submitProdManagerFormBtn = document.querySelector("#product-manager #prod-manager-submit-btn button");

var req = new Request("http://localhost/Admin/Gestione-prodotto/Fetch-Data");
var options = {
    method: "POST",
    heaers: {
        "Accept": "application/json",
        "Content-Type": "application/json"
    }
}
var product = {};

/* view shared */
var productData = productData;

product.productDetails = [];

/* Modifica */
if (productData && productData.ProductID) {
    product = productData;
    product.Images = product.Images.split(", ");
    product.NewImages = [];

    for (var i = 0, option; option = _genderSelect[i]; i++) {

        if (option.getAttribute("selected")) {
            option.selected = false;
        }

        if (option.value === product.Gender) {
            option.selected = true;
        }
    };

    options.body = JSON.stringify({ Gender: product.Gender });

    fetch(req, options).then(function (response) {
        return response.json();
    }).then(function (data) {
        var html = "<option value='' selected>Seleziona categoria</option>";

        for (var i = 0; i < data.length; i++) {
            html += "<option value='" + data[i]["Category"] + "'>" + data[i]["Category"] + "</option>"
        };

        _categorySelect.innerHTML = html;

        _categoryAccordionHeader.style.display = "flex";

        for (var j = 0, option2; option2 = _categorySelect[j]; j++) {

            if (option2.getAttribute("selected")) {
                option2.selected = false;
            }

            if (option2.value === product.Category) {
                option2.selected = true;
            }
        };

        options.body = JSON.stringify({ Gender: product.Gender, Category: product.Category });

        fetch(req, options).then(function (response) {
            return response.json();
        }).then(function (data) {
            var html = "<option value='' selected>Seleziona una sezione</option>";

            for (var i = 0; i < data.length; i++) {
                html += "<option value='" + data[i]["Type"] + "'>" + data[i]["Type"] + "</option>"
            };

            _typeSelect.innerHTML = html;

            _typeAccordionHeader.style.display = "flex";

            for (var x = 0, option3; option3 = _typeSelect[x]; x++) {

                if (option3.getAttribute("selected")) {
                    option3.selected = false;
                }

                if (option3.value === product.Type) {
                    option3.selected = true;
                }
            };
        });
    });

    var _modifyProdImgsCont = document.querySelector("#product-manager #modify-prod-images-cont");
    var _imgOverlapInceptions = document.querySelectorAll("#product-manager #modify-prod-images-cont .img-overlap-inception");

    _imgOverlapInceptions.forEach(function (_imgOverlap) {
        _imgOverlap.addEventListener("click", function (ev) {
            _modifyProdImgsCont.removeChild(this.parentElement);

            var numOfImgs = document.querySelectorAll("#product-manager #modify-prod-images-cont .img-overlap-inception").length;

            if (numOfImgs === 0) {
                _modifyProdImgsCont.style.display = "none";
            }

            var url = this.parentElement
                .querySelector("img")
                .dataset.url;

            product.Images = product.Images
                .filter(function (image) { return image !== url; });
        });
    });

    /* crea un numero di form uguale al numero di taglie disponibili */
    for (var y = 1, prodDetail; prodDetail = product.productDetails[y]; y++) {
        var _newSizeDetailsWrapper = _sizeDetailsWrapper.cloneNode(true);

        _newSizeDetailsWrapper.dataset.number = y;

        _productDetails.insertBefore(_newSizeDetailsWrapper, _addSizeDetail);
    };

    var _sizeDetailsWrappers = document.querySelectorAll("#product-manager .size-details-wrapper");

    _sizeDetailsWrappers.forEach(function (_sizeDetailsWrapper, i) {
        var _sizeDetailsForm = _sizeDetailsWrapper.querySelector("form");
        var prodDetail = product.productDetails[i];
        var z = 0;

        /* popola i campi input del form */
        while (_sizeDetailsForm[z]) {

            if (_sizeDetailsForm[z].type !== "submit") {

                for (var key in prodDetail) {

                    if (_sizeDetailsForm[z].name === key) {
                        _sizeDetailsForm[z].value = prodDetail[key];
                    }
                };
            }

            z++;
        };

        /* imposta sample colore */
        var _colorInput = _sizeDetailsForm.querySelector("#product-manager .color-input")
        var _colorSample = _sizeDetailsForm.querySelector("#product-manager .color-sample");
        _colorSample.style.backgroundColor = _colorInput.value;

        /* mostra in versione testo semplice i dettagli relativi alla taglia del prodotto */
        showSelectedSizeDetails(_sizeDetailsForm, prodDetail);
    });
}

function resetSelect(_select) {
    var _selectOptions = _select.childNodes;

    for (var i = 0; i < _selectOptions.length; i++) {
        _selectOptions[i].selected = false;
    }

    _selectOptions[0].selected = true;
};

function checkValidity(_input) {
    var _invalid = _input.parentElement.querySelector(".invalid-message");

    var errMsg = "";

    switch (_input.id) {
        case "name":
        case "add-new-category":
        case "add-new-type":
            var regExp = new RegExp(_input.pattern);

            if (!regExp.test(_input.value)) {
                errMsg = "Inserire dei valori corretti";
            }

            if (!_input.value) {
                errMsg = "Il campo input non può essere vuoto";
            }

            if (regExp.test(_input.value) && _input.value) {
                errMsg = "";
            }
            break;
        case "price":
        case "description":

            if (!_input.value) {
                errMsg = "Il campo input non può essere vuoto";
            } else {
                errMsg = "";
            }

            break;
    };

    _invalid.textContent = errMsg;
};

/* --------------------------------------------- */

_nameInput.oninput = function () {
    product.Name = this.value;

    this.parentElement.classList.remove("required");

    checkValidity(this);
};

/* --------------------------------------------- */

_genderSelect.oninput = function (ev) {
    product.Gender = this.value;

    if (!this.value) {
        _categorySelect.innerHTML = "<option value='' selected>Seleziona un genere</option>";

        _categoryAccordionHeader.style.display = "none";

        return;
    }

    if (product.Gender) {
        options.body = JSON.stringify({Gender: this.value});

        fetch(req, options).then(function (response) {
            return response.json();
        }).then(function (data) {
            var html = "<option value='' selected>Seleziona categoria</option>";

            for (var i = 0; i < data.length; i++) {
                html += "<option value='" + data[i]["Category"] + "'>" + data[i]["Category"] + "</option>"
            }

            _categorySelect.innerHTML = html;

            _categoryAccordionHeader.style.display = "flex";
        });
    }
};

/* --------------------------------------------- */

_categorySelect.oninput = function () {
    product.Category = this.value;

    this.required = true;
    _newCategoryInput.required = false;

    this.parentElement.classList.remove("required");

    if (_newCategoryInput.value) {
        _newCategoryInput.value = "";
    }

    if (!this.value) {

        _typeSelect.innerHTML = "<option value='' selected>Seleziona una categoria</option>";

        _typeAccordionHeader.style.display = "none";

        return;
    }

    if (product.Category) {
        options.body = JSON.stringify({Gender: product.Gender, Category: this.value});

        fetch(req, options).then(function (response) {
            return response.json();
        }).then(function (data) {
            var html = "<option value='' selected>Seleziona una sezione</option>";

            for (var i = 0; i < data.length; i++) {
                html += "<option value='" + data[i]["Type"] + "'>" + data[i]["Type"] + "</option>"
            }

            _typeSelect.innerHTML = html;

            _typeAccordionHeader.style.display = "flex";
        });
    }
};

_newCategoryInput.oninput = function () {
    this.required = true;
    _categorySelect.required = false;

    if (_categorySelect.value) {
        resetSelect(_categorySelect);
    }

    this.parentElement.classList.remove("required");

    checkValidity(this);
};

_categoryForm.onsubmit = function (ev) {
    ev.preventDefault();

    var value = _newCategoryInput.value.charAt(0).toUpperCase() + _newCategoryInput.value.slice(1);

    for (var i = 0, _option; _option = _categorySelect.childNodes[i]; i++) {
        if (_option.value === value) {
            $("#add-category-cont").collapse("hide");

            $("#add-new-type-cont").collapse("show");

            setTimeout(function () {
                _categoryAccordionHeader.childNodes[0].classList.add("fa-plus");
                _categoryAccordionHeader.childNodes[0].classList.remove("fa-minus");
                _categoryAccordionHeader.childNodes[1].textContent = "Aggiungi nuova categoria";
            }, 300);

            return;
        }
    };

    product.Category = value;

    _categorySelect.innerHTML += "<option value='" + value + "' selected>" + value + "</option>";

    _typeAccordionHeader.style.display = "flex";

    _newCategoryInput.required = false;
    _categorySelect.required = true;

    $("#add-category-cont").collapse("hide");

    $("#add-new-type-form").collapse("show");

    setTimeout(function () {
        _categoryAccordionHeader.childNodes[0].classList.add("fa-plus");
        _categoryAccordionHeader.childNodes[0].classList.remove("fa-minus");
        _categoryAccordionHeader.childNodes[1].textContent = "Aggiungi nuova categoria";
    }, 300);

    setTimeout(function () {
        _typeAccordionHeader.childNodes[0].classList.remove("fa-plus");
        _typeAccordionHeader.childNodes[0].classList.add("fa-minus");
        _typeAccordionHeader.childNodes[1].textContent = "Nascondi";
    }, 300);
};

_categoryAccordionHeader.onclick = function () {
    var self = this;

    if (self.childNodes[0].classList.contains("fa-plus")) {
        _newCategoryInput.required = true;
        _categorySelect.required = false;
        _typeSelect.innerHTML = "<option value='' selected>Seleziona una categoria</option>";

        if (product.category) {
            resetSelect(_categorySelect);
        }

        setTimeout(function () {
            self.childNodes[0].classList.remove("fa-plus");
            self.childNodes[0].classList.add("fa-minus");
            self.childNodes[1].textContent = "Nascondi";
        }, 300);
    } else {
        _newCategoryInput.required = false;
        _categorySelect.required = true;

        setTimeout(function () {
            self.childNodes[0].classList.add("fa-plus");
            self.childNodes[0].classList.remove("fa-minus");
            self.childNodes[1].textContent = "Aggiungi nuova categoria";
        }, 300);
    }
};

/* --------------------------------------------- */

_typeSelect.oninput = function () {
    product.Type = this.value;

    this.required = true;
    _newTypeInput.required = false;

    this.parentElement.classList.remove("required");

    if (_newTypeInput.value) {
        _newTypeInput.value = "";
    }
};

_newTypeInput.oninput = function () {
    this.required = true;
    _typeSelect.required = false;

    if (_typeSelect.value) {
        resetSelect(_typeSelect);
    }

    this.parentElement.classList.remove("required");

    checkValidity(this);
};

_typeForm.onsubmit = function (ev) {
    ev.preventDefault();

    var value = _newTypeInput.value.charAt(0).toUpperCase() + _newTypeInput.value.slice(1);

    for (var i = 0, _option; _option = _typeSelect.childNodes[i]; i++) {
        if (_option.value === value) {
            $("#add-new-type-form").collapse("hide");

            setTimeout(function () {
                _typeAccordionHeader.childNodes[0].classList.add("fa-plus");
                _typeAccordionHeader.childNodes[0].classList.remove("fa-minus");
                _typeAccordionHeader.childNodes[1].textContent = "Aggiungi nuova categoria";
            }, 300);
            return;
        }
    };

    product.Type = value;

    _typeSelect.innerHTML += "<option value='" + value + "' selected>" + value + "</option>";

    _newTypeInput.required = false;
    _typeSelect.required = true;

    $("#add-new-type-form").collapse("hide");

    setTimeout(function () {
        _typeAccordionHeader.childNodes[0].classList.add("fa-plus");
        _typeAccordionHeader.childNodes[0].classList.remove("fa-minus");
        _typeAccordionHeader.childNodes[1].textContent = "Aggiungi nuova categoria";
    }, 300);
};

_typeAccordionHeader.onclick = function () {
    var self = this;

    if (self.childNodes[0].classList.contains("fa-plus")) {
        _newTypeInput.required = true;
        _typeSelect.required = false;

        if (product.type) {
            resetSelect(_typeSelect);
        }

        setTimeout(function () {
            self.childNodes[0].classList.remove("fa-plus");
            self.childNodes[0].classList.add("fa-minus");
            self.childNodes[1].textContent = "Nascondi";
        }, 300);
    } else {
        _newTypeInput.required = false;
        _typeSelect.required = true;

        if (_newTypeInput.value) {
            _newTypeInput.value = "";
        }

        setTimeout(function () {
            self.childNodes[0].classList.add("fa-plus");
            self.childNodes[0].classList.remove("fa-minus");
            self.childNodes[1].textContent = "Aggiungi nuova categoria";
        }, 300);
    }
};

/* --------------------------------------------- */

_priceInput.oninput = function () {
    product.Price = this.value;

    this.parentElement.classList.remove("required");

    checkValidity(this);
};

/* --------------------------------------------- */

_discountInput.oninput = function () {
    product.Discount = this.value;
};

/* --------------------------------------------- */

_description.oninput = function () {
    product.Description = this.value;

    this.parentElement.classList.remove("required");

    checkValidity(this);
};

/* --------------------------------------------- */

_imagesForm.onchange = function (ev) {
    product.NewImages = ev.target.files;

    _thumbImgsCont.innerHTML = "";

    for (var i = 0, image; image = product.NewImages[i]; i++) {

        if (/\.(jpe?g|png|gif)$/i.test(image.name)) {
            var reader = new FileReader();

            reader.onload = function (ev) {
                var _imgCanvas = document.createElement("div");
                _imgCanvas.setAttribute("class", "img-canvas");
                _imgCanvas.style.backgroundImage = "url(" + this.result + ")";

                _thumbImgsCont.appendChild(_imgCanvas);
            };

            reader.readAsDataURL(image);
        }
    };

    this.classList.remove("required");

    checkValidity(this);
};

/* --------------------------------------------- */

function addSizeDetailEvents(ev) {
    var _sizeDetails = document.querySelectorAll("#product-manager .size-detail");

    function getSizeDetailData(e) {
        e.preventDefault();

        this.parentElement.classList.remove("required");

        var elemNumber = this.parentElement.dataset.number;
        var formData = {};
        var i = 0;

        while (this[i]) {

            if (this[i].type !== "submit") {
                formData[this[i].name] = this[i].value;
            }

            i++;
        };

        product.productDetails[elemNumber] = formData;

        showSelectedSizeDetails(this, formData);
    };

    for (var i = 0, _form; _form = _sizeDetails[i]; i++) {
        _form.addEventListener("submit", getSizeDetailData);
    };
};

function showSelectedSizeDetails(_form, formData) {
    var _sizeDetailsSelected = _form.nextElementSibling;
    var _selectedSize = _sizeDetailsSelected.querySelector(".size-selected span");
    var _selectedColor = _sizeDetailsSelected.querySelector(".color-selected span");
    var _selectedQunatity = _sizeDetailsSelected.querySelector(".quantity-selected span");
    var _selectedAvailability = _sizeDetailsSelected.querySelector(".availability-selected span");
    var _modifyBtn = _sizeDetailsSelected.querySelector(".btn-cont button");
    var _delete = _sizeDetailsSelected.querySelector(".fa-times");

    var elemNumber = _sizeDetailsSelected.parentElement.dataset.number;

    _selectedSize.textContent = formData.Size.toUpperCase() || "/";

    if (formData.Color) {
        _selectedColor.style.backgroundColor = formData.Color
    } else {
        _selectedColor.style.border = "none";
    }

    _selectedQunatity.textContent = formData.Stock;

    switch (formData.Availability) {
        case "black":
            _selectedAvailability.textContent = "Esaurito";
            break;
        case "red":
            _selectedAvailability.textContent = "In arrivo, 4 settimane";
            break;
        case "yellow":
            _selectedAvailability.textContent = "In arrivo, 2 settimane";
            break;
        case "green":
            _selectedAvailability.textContent = "Disponibile";
            break;
    };

    _form.style.display = "none";
    _sizeDetailsSelected.style.display = "flex";

    _modifyBtn.onclick = function () {
        _sizeDetailsSelected.style.display = "none";
        _form.style.display = "block";
    };

    _delete.onclick = function () {
        product.productDetails.splice(elemNumber, 1);
        _sizeDetailsSelected.style.display = "none";
        _form.style.display = "block";
    };
};

function showColorSample(ev) {
    var _parent = ev.target.parentElement;
    var _colorSample = _parent.querySelector("#product-manager .color-sample");

    _colorSample.style.backgroundColor = ev.target.value;
}

addSizeDetailEvents();

_addSizeDetail.onclick = function () {
    var _newSizeDetailsWrapper = _sizeDetailsWrapper.cloneNode(true);

    var formsNumber = document.querySelectorAll("#product-manager .size-details-wrapper").length;
    _newSizeDetailsWrapper.dataset.number = formsNumber;

    var _newSizeDetailsForm = _newSizeDetailsWrapper.querySelector("form");
    var _newSizeDetailsSelected = _newSizeDetailsWrapper.querySelector(".size-details-selected");

    var i = 0;

    /* resetta i campi input */
    while (_newSizeDetailsForm[i]) {

        if (_newSizeDetailsForm[i].type !== "submit") {
            _newSizeDetailsForm[i].value = "";
        }

        i++;
    };

    _newSizeDetailsForm.style.display = "block";
    _newSizeDetailsSelected.style.display = "none";

    _productDetails.insertBefore(_newSizeDetailsWrapper, this);

    addSizeDetailEvents();
};

/* --------------------------------------------- 
* Verifica correttezza campi input
*/

_submitProdManagerFormBtn.onclick = function () {
    var _invalid = document.querySelector("#" + this.id + " ~ .invalid-message");

    _invalid.innerHTML = "";

    var inputIdArr = ["#name", "#select-gender", "#select-category", "#add-new-category", "#select-type", "#add-new-type", "#price", "#discount", "#description"];
    var errMsg = "";

    inputIdArr.forEach(function (id) {
        var _formElem = document.querySelector("#product-manager " + id);

        if (!_formElem.checkValidity()) {

            switch (id) {
                case "#name":
                    var regExp = new RegExp(_formElem.pattern);

                    if (!regExp.test(_formElem.value)) {
                        errMsg = "Inserire un nome corretto";
                    }

                    if (!_formElem.value) {
                        errMsg = "è necessario inserire un nome";
                    }

                    if (regExp.test(_formElem.value) && _formElem.value) {
                        errMsg = "";
                    }

                    _invalid.innerHTML += "<span class='invalid-message'>" + errMsg + "</span>";

                    _formElem.parentElement.classList.add("required");

                    break;
                case "#select-gender":

                    if (!product.gender) {
                        errMsg = "è necessario inserire un sesso";
                    }

                    _invalid.innerHTML += "<span class='invalid-message'>" + errMsg + "</span>";

                    _formElem.parentElement.classList.add("required");

                    break;
                case "#select-category":

                    if (!product.category) {
                        errMsg = "è necessario inserire una categoria";
                    }

                    _invalid.innerHTML += "<span class='invalid-message'>" + errMsg + "</span>";

                    _formElem.parentElement.classList.add("required");

                    break;
                case "#add-new-category":

                    if (_categoryForm.classList.contains("show")) {
                        var regExp = new RegExp(_formElem.pattern);

                        if (!regExp.test(_formElem.value)) {
                            errMsg = "Inserire una categoria corretta";
                        }

                        if (!_formElem.value) {
                            errMsg = "è necessario inserire una categoria";
                        }

                        if (regExp.test(_formElem.value) && _formElem.value) {
                            errMsg = "";
                        }

                        _invalid.innerHTML += "<span class='invalid-message'>" + errMsg + "</span>";

                        _formElem.parentElement.classList.add("required");
                    }

                    break;
                case "#select-type":

                    if (!product.type) {
                        errMsg = "è necessario inserire una sezione";
                    }

                    _invalid.innerHTML += "<span class='invalid-message'>" + errMsg + "</span>";

                    _formElem.parentElement.classList.add("required");

                    break;
                case "#add-new-type":

                    if (_typeForm.classList.contains("show")) {
                        var regExp = new RegExp(_formElem.pattern);

                        if (!regExp.test(_formElem.value)) {
                            errMsg = "Inserire una sezione corretta";
                        }

                        if (!_formElem.value) {
                            errMsg = "è necessario inserire una sezione";
                        }

                        if (regExp.test(_formElem.value) && _formElem.value) {
                            errMsg = "";
                        }

                        _invalid.innerHTML += "<span class='invalid-message'>" + errMsg + "</span>";

                        _formElem.parentElement.classList.add("required");
                    }

                    break;
                case "#price":

                    if (!_formElem.value) {
                        errMsg = " è necessario inserire un prezzo";
                    }

                    _invalid.innerHTML += "<span class='invalid-message'>" + errMsg + "</span>";

                    _formElem.parentElement.classList.add("required");

                    break;
                case "#description":

                    if (!_formElem.value) {
                        errMsg = "è necessario inserire una descrizione del prodotto";
                    }

                    _invalid.innerHTML += "<span class='invalid-message'>" + errMsg + "</span>";

                    _formElem.parentElement.classList.add("required");

                    break;
            };
        };
    });

    if (!product.ProductID && (!product.NewImages || product.NewImages.length === 0)) {
        _imagesForm.classList.add("required");

        errMsg = "è necessario inserire almeno una immagine";
        _invalid.innerHTML += "<span class='invalid-message'>" + errMsg + "</span>";
    }

    if (product.ProductID && (!product.Images || product.Images.length === 0)) {
        _imagesForm.classList.add("required");

        errMsg = "è necessario inserire almeno una immagine";
        _invalid.innerHTML += "<span class='invalid-message'>" + errMsg + "</span>";
    }

    if (!product.productDetails || product.productDetails.length === 0) {
        _sizeDetailsWrapper.classList.add("required");

        errMsg = "è necessario inserire almeno una taglia";
        _invalid.innerHTML += "<span class='invalid-message'>" + errMsg + "</span>";
    }

    if (_invalid.innerHTML === "") {
        /* Modifica */
        if (productData && productData.ProductID) {
            var req = new Request("http://localhost/Admin/Gestione-prodotto/Modifica/" + productData.ProductID);

            if (product.NewImages.length > 0) {
                var formData = new FormData();

                for (var i = 0, file; file = product.NewImages[i]; i++) {
                    formData.append("Images[]", file);
                }

                var options = {
                    method: "POST",
                    body: formData
                };

                fetch(req, options).then(function (response) {
                    return response.json();
                }).then(function (data) {
                    var productData = data;

                    delete productData.NewImages;

                    var options2 = {
                        method: "POST",
                        heaers: {
                            "Accept": "application/json",
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(productData)
                    };

                    fetch(req, options2).then(function (response2) {
                        return response2.json();
                    }).then(function (data2) {
                        console.log(data2);
                    });
                });
            } else {
                delete product.NewImages;

                var options = {
                    method: "POST",
                    heaers: {
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(product)
                };

                fetch(req, options).then(function (response) {
                    return response.json();
                }).then(function (data) {
                    console.log(data);
                });
            }
        } else {
            var req = new Request("http://localhost/Admin/Gestione-prodotto/Aggiungi-prodotto");
            var formData = new FormData();

            for (var i = 0, file; file = product.NewImages[i]; i++) {
                formData.append("Images[]", file);
            }

            var options = {
                method: "POST",
                body: formData
            };

            fetch(req, options).then(function (response) {
                return response.json();
            }).then(function (data) {
                var imgsNames = data;

                product.Images = imgsNames;

                delete product.NewImages;

                var options2 = {
                    method: "POST",
                    heaers: {
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(product)
                };

                fetch(req, options2).then(function (response2) {
                    return response2.json();
                }).then(function (data2) {
                    console.log(1, data2);
                });
            });
        }
    }
};