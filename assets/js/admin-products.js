/* document.querySelector(""); */
var _select = document.querySelector("#admin-products select");
var _orderArrowUp = document.querySelector("#admin-products #up");
var _orderArrowDown = document.querySelector("#admin-products #down");
var _orderBtn = document.querySelector("#admin-products #admin-tools #btn-cont-tool1 a");
var _filterInput = document.querySelector("#admin-products #filter-input");
var _inputCont = document.querySelector("#admin-products #input-cont");
var _dropdown = document.querySelector("#admin-products #dropdown");

$('.product-images-owlcar').owlCarousel({
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
    }
    /* onTranslated: sgstInfo */
});

var events = ["click", "keyup"];

events.forEach(function (event) {
    _filterInput.addEventListener(event, function (ev) {
        if (!this.value) {
            _dropdown.style.display = "none";
            _dropdown.innerHTML = "";
            return;
        } else {
            _dropdown.style.display = "block";
        }

        /*
        * Inoltra una richiesta "GET" per un file tipo json al controller "SearchController" il quale 
        * risponderà con un file json contenente i nomi dei prodotti che iniziano con la parola inserita
        * nella query della url.
         */
        var req = new Request("http://localhost/Admin/Prodotti/Suggerimento/" + this.value);
        var options = {
            method: "GET",
            headers: {
                "Content-Type": "application/json"
            }
        }

        fetch(req, options).then(function (response) {
            return response.json();
        }).then(function (data) {
            var html = "";
            var regExp = new RegExp('^' + data.keyword.toLowerCase());
            var gender, category = false;
            var unique = [];
            var hints = [];

            console.log(data.hints);

            data.hints.forEach(function (value) {
                if (html && (gender || category)) {
                    return;
                }

                for (var key in value) {
                    var val = value[key].toLowerCase();
                    var pTxt = "";
                    var spanTxt = "";

                    if (regExp.test(val)) {
                        var index = unique.indexOf(val);

                        if (index < 0) {
                            pTxt = value[key];
                            spanTxt = key;

                            switch (spanTxt) {
                                case "ProductID":
                                    spanTxt = "N°";
                                    break;
                                case "Name":
                                    spanTxt = "Nome";
                                    break;
                                case "Gender":
                                    gender = true;
                                    spanTxt = "Genere";
                                    break;
                                case "Category":
                                    category = true;
                                    spanTxt = "Categoria";
                                    break;
                                case "Type":
                                    spanTxt = "Tipo";
                                    break;
                                case "Availability":
                                    spanTxt = "Disponibilità";
                                    break;
                                default:
                                    break;
                            }

                            hints.push({ pTxt: pTxt, spanTxt: spanTxt });
                            unique.push(val);
                        }
                    }
                }
            });

            var gen = [], ctg = [], tp = [], num = [], name = [], availability = [];

            hints.forEach(function (obj) {
                switch (obj.spanTxt) {
                    case "Disponibilità":
                        availability.push(obj);
                        break;
                    case "N°":
                        num.push(obj);
                        break;
                    case "Nome":
                        name.push(obj);
                        break;
                    case "Genere":
                        gen.push(obj);
                        break;
                    case "Categoria":
                        ctg.push(val);
                        break;
                    case "Tipo":
                        tp.push(obj);
                        break;
                    default:
                        break;
                }
            });

            hints = num.concat(availability, gen, ctg, tp, name);
            hints.forEach(function (hint) {
                html += `<div class="hint-elem text-dark">
                        <p data-value="${hint.pTxt}">${hint.pTxt}<span>(${hint.spanTxt})</span></p>
                    </div>`;
            });

            _dropdown.innerHTML = html;

            var _hintElements = _dropdown.querySelectorAll(".hint-elem");
            _hintElements.forEach(function (_hintItem) {
                _hintItem.addEventListener("click", function () {
                    _p = this.querySelector("p");
                    _filterInput.value = _p.dataset.value;
                    _dropdown.style.display = "none";
                    _dropdown.innerHTML = "";
                });
            });
        });
    });
});

_inputCont.onmouseenter = function () {
    if (!_filterInput.value) {
        _dropdown.style.display = "none";
        _dropdown.innerHTML = "";
        return;
    } else {
        _dropdown.style.display = "block";
    }
}

_inputCont.onmouseleave = function () {
    if (_dropdown.style.display === "block") {
        _dropdown.style.display = "none";
    }
}