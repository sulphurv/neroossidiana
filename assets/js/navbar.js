var dropDData = {};

var req = new Request("http://neroossidiana.com/Navbar-data");
var options = {
    method: "GET"
};

fetch(req, options).then(function (response) {
    return response.json();
}).then(function (data) {
    dropDData = data;
    createDropDown();
    stage1(null);
});

var _dropDCont = document.querySelector("#dropd-cont");

function createDropDown(resolve) {
    var html = "";
    for (var gender in dropDData) {
        html +=
            `<div class="dropdown" id=${gender.toLowerCase() + "-dropdown"}>
                <div class="dropd-section">
                    <div class="category">`;

        for (var category in dropDData[gender]) {
            html +=
                        `<div data-gender=${gender} data-category=${category}>
                            <a class="underline" href=${"/Prodotti/" + gender + "/" + category + "/Page1"}>${category}</a>
                            <i class="fa fa-chevron-right" aria-hidden="true"></i>
                        </div>`;
        }
        html +=
                    `</div>
                    <div class="links"></div>
                </div>
                <div id="dropd-img"></div>
            </div>`;
    }

    _dropDCont.innerHTML = html;

    var _categories = document.querySelectorAll(".category div");

    _categories.forEach(function (_elem) {
        addLinks(_elem, "load");
        _elem.addEventListener("mouseenter", function () { addLinks(this, null); });
    });

    function addLinks(_category, event) {
        var gender = _category.getAttribute("data-gender");
        var category = _category.getAttribute("data-category");

        var _links = _category.parentElement.nextElementSibling;
        _links.innerHTML = "";
        var html = "";

        for (var type of dropDData[gender][category]) {
            html +=
                `<div class="link">
                        <a href=${"/Prodotti/" + gender + "/" + category + "/" + type.split(" ").join("-") + "/Page1"}>${type}</a>
                    </div>`;
        }

        if (event === "load" && _links.firstChild) {
            return;
        }

        _links.innerHTML = html;
    };

    var _male = document.querySelector("#male");
    var _female = document.querySelector("#female");
    var _maleDropdown = document.querySelector("#uomo-dropdown");
    var _femaleDropdown = document.querySelector("#donna-dropdown");
    var _genderNav = document.querySelector("#gender-nav");
    var _dropdCont = document.querySelector("#dropd-cont");

    var isAnim = false;
    var timeoutId;

    function mouseEnter(_elemToShow, _elemToHide) {
        _elemToShow.classList.remove("remove");

        _elemToHide.classList.add("remove");
        /* clearTimeout(timeoutId);

        if (isAnim) {
            _elemToShow.classList.remove("hide");
            isAnim = false;
        }

        _elemToShow.classList.remove("remove");
        _elemToShow.classList.add("dropd-fade-in");

        _elemToHide.classList.add("remove");
        _elemToHide.classList.remove("dropd-fade-in"); */
    };

    function mouseLeave(_elemToHide) {
        _elemToHide.classList.add("remove");
        /* if (isAnim) return;

        isAnim = true;

        _elemToHide.classList.add("hide");
        _elemToHide.classList.remove("dropd-fade-in");
        timeoutId = setTimeout(function () {
            _elemToHide.classList.remove("hide");
            _elemToHide.classList.add("remove");
            isAnim = false;
        }, 350); */
    };

    function showDropD(_elem) {
        _dropdCont.classList.remove("remove");
    }

    function hideAll() {
        var elemArr = [_maleDropdown, _femaleDropdown];

        _dropdCont.classList.add("remove");

        elemArr.forEach(function (_section) {
            _section.classList.add("remove");
            /*  _section.classList.add("hide");
             _section.classList.remove("dropd-fade-in");
             timeoutId = setTimeout(function () {
                 _section.classList.remove("hide");
                 _section.classList.add("remove");
                 isAnim = false;
             }, 350); */
        });
    };

    _male.onmouseenter = function () { mouseEnter(_maleDropdown, _femaleDropdown); }
    _female.onmouseenter = function () { mouseEnter(_femaleDropdown, _maleDropdown); }
    _genderNav.onmouseenter = function (ev) { showDropD(this); }
    _genderNav.onmouseleave = function (ev) { hideAll(); }

    _categoriesInner = _dropDCont.querySelectorAll(".category div");
    _maleCatInn = Array.prototype.slice.call(_categoriesInner)
                      .filter(function (_elem) { return _elem.getAttribute("data-gender") === "Uomo" });
    _femaleCatInn = Array.prototype.slice.call(_categoriesInner)
                        .filter(function (_elem) { return _elem.getAttribute("data-gender") === "Donna" });

    [_maleCatInn, _femaleCatInn].forEach(function (_nodeArray) {
        _nodeArray.forEach(function (_innerElem, index) {
            
            if (index === 0) {
                _innerElem.classList.add("active-category");
            }

            _innerElem.onmouseenter = function () {
                _nodeArray.forEach(function (_elem) {
                    _elem.classList.remove("active-category");
                });
    
                this.classList.add("active-category");
            }
        });
    });

    
};

var _menu1 = document.querySelector("#side-menu");
var _overlay = document.querySelector("#overlay");
var _input = document.querySelector("#menu-toggle input");
var _backElem1 = _menu1.querySelector(".back");
var _searchInput = document.querySelector("#search-section input");
var _searchDropD = document.querySelector("#suggestions-cont");

var navSection = [];
var isAnim = false;

_searchDropD.style.width = _searchInput.offsetWidth + 1 + "px";

var _menu2 = _menu1.cloneNode(true);
_menu2.id = "side-menu-2";
_menu2.classList = "menu bottom";

_menu1.insertAdjacentElement("afterend", _menu2);

function stage1(dir) {
    // la variabile "dropDData" viene creata all'interno della pagina "navbar.phtml" passando un array associatico php, convertito in formato json, a questa variable.
    resetAndCreateMenu(_menu1, stage2, dropDData, "fw");

    // backwards
    if (dir === "bw") {
        slide("in-out");
    }
}

function stage2(param, dir) {
    if (dir === "fw") {
        resetAndCreateMenu(_menu2, stage3, dropDData[param], "fw");

        slide("out-in");
    }

    if (dir === "bw") {
        resetAndCreateMenu(_menu1, stage3, dropDData[param], "bw");

        slide("in-out");
    }
}

function stage3(array) {
    resetAndCreateMenu(_menu2, null, array, null);

    slide("out-in");
}

function goBack() {
    if (isAnim) {
        return;
    }

    if (navSection.length === 2) {
        navSection.pop();

        stage2(navSection[0], "bw");

        return;
    }

    if (navSection.length === 1) {
        navSection.pop();

        stage1("bw");

        return;
    }
}

function createMenuList(_ul, callback, data, dir) {
    var _li = document.createElement("li");
    var _a = document.createElement("a");

    var link = "/Prodotti";
    navSection.forEach(function (elem) {
        link += "/" + elem;
    });
    link += "/" + data;

    _a.setAttribute("href", link + "/Page1");
    _a.classList.add("text-dark");
    _a.textContent = data;

    _li.appendChild(_a);

    if (callback) {
        var _i = document.createElement("i");
        _i.classList = "fa fa-arrow-circle-o-right next";
        _i.onclick = function () {

            if (isAnim) {
                return;
            }
            navSection.push(data);

            if (navSection.length === 2) {
                data = dropDData[navSection[0]][navSection[1]];
            }

            callback(data, dir);
        }

        _li.appendChild(_i);
    }

    _ul.appendChild(_li);
}

function resetAndCreateMenu(_menu, callback, obj, dir, block) {
    block = block || false;
    var _menuList = _menu.querySelector(".menu-list");

    resetMenu(_menuList);

    // crea elementi
    if (Array.isArray(obj)) {
        for (var data of obj) {
            (function (_m, c, d, di) { createMenuList(_m, c, d, di); })(_menuList, callback, data, dir);
        }
    } else {
        for (var data in obj) {
            (function (_m, c, d, di) { createMenuList(_m, c, d, di); })(_menuList, callback, data, dir);
        }
    }

    if (block) return;

    setTimeout(function () {
        if (dir === "fw" || !dir) {
            resetAndCreateMenu(_menu1, callback, obj, dir, true);
        }

        if (dir === "bw") {
            resetAndCreateMenu(_menu2, callback, obj, dir, true);
        }
    }, 500);
}

function resetMenu(_menuL) {
    var _backElem = _menuL.querySelector(".back");

    while (_menuL.firstChild) {
        _menuL.removeChild(_menuL.firstChild);
    }

    if (navSection.length > 0) {
        _backElem.children[0].classList.remove("remove");
    } else {
        _backElem.children[0].classList.add("remove");
    }

    _menuL.appendChild(_backElem);
}

function switchPos(_elem1, _elem2) {
    //move to top
    _elem1.classList.add("top");
    _elem1.classList.remove("bottom");

    // move to bottom
    _elem2.classList.remove("top");
    _elem2.classList.add("bottom");
}

function slide(dir) {
    isAnim = true;

    // forward
    if (dir === "out-in") {
        _menu2.classList = "menu menu-slide-out-in";
    }
    // backward
    if (dir === "in-out") {
        _menu2.classList = "menu menu-slide-in-out";
    }

    switchPos(_menu2, _menu1);
    setTimeout(function () {
        isAnim = false;
        _menu2.classList = "menu";
        switchPos(_menu1, _menu2);
    }, 500);
}

_input.checked = false;

_input.onclick = function () {
    if (this.checked) {
        _overlay.classList.remove("remove");
        _menu1.classList = "menu top menu-slide-out-in forwards";
    } else {
        _overlay.classList.add("remove");
        _menu1.classList = "menu top menu-slide-in-out forwards";
    }
};

_overlay.onclick = function () {
    _input.checked = false;
    _overlay.classList.add("remove");
    _menu1.classList = "menu top menu-slide-in-out forwards";
    _menu2.classList = "menu bottom menu-slide-in-out forwards";
};

_backElem1.onclick = function () { goBack(); };




["click", "keyup"].forEach(function (event) {
    _searchInput.addEventListener(event, function (ev) {
        if (!this.value) {
            _searchDropD.style.display = "none";
            _searchDropD.innerHTML = "";
            return;
        } else {
            _searchDropD.style.display = "block";
        }

        /*
        * Inoltra una richiesta "GET" per un file tipo json al controller "SearchController" il quale 
        * risponderà con un file json contenente i nomi dei prodotti che iniziano con la parola inserita
        * nella query della url.
         */
        var req = new Request("http://localhost/Cerca/" + this.value);
        var options = {
            method: "GET"
        }

        fetch(req, options).then(function (response) {
            return response.json();
        }).then(function (data) {
            var html = "";
            data.forEach(function (value) {
                html += `<a class="search-item text-dark" href="/Dettagli-Prodotto/${value[0]}">
                            <p>${value[2]}<span>(${value[1]})</span></p>
                        </a>`;
            });
            _searchDropD.innerHTML = html;
        });
    });
});

/*
* Cliccando sul qualsiasi parte della pagina che non sia la finestra dropdown della ricerca prodotto
* o l'input di ricerca, l'evento innescherà la chiusura dell'elemento.
*/
document.onclick = function (ev) {
    var _parentElem = ev.target.parentElement;
    var out = false;

    if (ev.target.id !== "search-input") {

        while (_parentElem) {

            if (_parentElem.id !== "suggestions-cont") {
                _parentElem = _parentElem.parentElement;
                out = true;
            } else {
                out = false;
                break;
            }
        }
    }

    if (out) {
        _searchDropD.style.display = "none";
        _searchDropD.innerHTML = "";
    }
}