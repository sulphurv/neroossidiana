(function () {
    'use strict';
    window.addEventListener('load', function () {
        var idArr = ["#username", "#email", "#password-old", "#password-new", "#password-new-confirm"];
        var _form = document.querySelector('#profile-settings-form');
        var _newPswd = _form.querySelector("#password-new");
        var _newPswdConf = _form.querySelector("#password-new-confirm");
        var _passRmStep1 = document.querySelector("#step-1");
        var _confirmRmBtn = _passRmStep1.querySelector("#confirm-remove");
        var _passRmStep2 = document.querySelector("#step-2");
        var _closeStep2Btn = document.querySelector("#step-2-close");
        var valid = true;

        _form.addEventListener('submit', function (event) {
            /* Fare attenzione! Se si deve modificare l'array "idArr" ricordarsi di aggiornare
             il metodo slice in modo che includa gli id dei campi input tipo password. */
            var pwd = idArr.slice(2, 5);
            var i = 0;

            pwd.forEach(function (pwdId) {
                var _pwdElem = document.querySelector("#profile-settings " + pwdId);
                if (_pwdElem.value) {
                    pwd.map(function (pwdId2) {
                        var _pwdElem2 = document.querySelector("#profile-settings " + pwdId2);
                        _pwdElem2.setAttribute("required", "");
                    });
                    return;
                } else {
                    i++;
                }
            });

            // se tutti i campi input tipo password sono vuoti ed è presente un attributo tipo "required", rimuovilo.
            if (i === pwd.length) {
                pwd.map(function (pwdId) {
                    var _pwdElem = document.querySelector("#profile-settings " + pwdId);
                    if (_pwdElem.hasAttribute("required")) {
                        _pwdElem.removeAttribute("required");
                    }
                });
            }

            // se tutti i campi input tipo password possiedono un valore, ma la nuova password di conferma non combacia mostra un errore. 
            if (i === 0) {
                var _errElem = _form.querySelector("#password-new-confirm + .invalid-feedback");

                if (_newPswd.value !== _newPswdConf.value) {
                    _newPswdConf.classList.add("invalid-input");
                    _errElem.style.display = "block";
                    setTimeout(function () { _errElem.textContent = "La password non combacia" }, 50);
                    valid = false;
                } else {
                    _newPswdConf.classList.remove("invalid-input");
                    _errElem.style.display = "none";
                    _errElem.textContent = "";
                    valid = true;
                }
            }

            if (this.checkValidity() === false || !valid) {
                event.preventDefault();
                event.stopPropagation();
            }

            this.classList.add('was-validated');

            idArr.forEach(function (formElemId) {
                var _elem = document.querySelector(formElemId);
                checkIfValid(_elem, formElemId);
            });

            function checkIfValid(_self, formElemId) {
                var regExp, errMsg;
                var _errElem = _form.querySelector(formElemId + " ~ .invalid-feedback");

                if (formElemId === "#username") {
                    regExp = new RegExp(_self.pattern);

                    if (!_self.validity.valid) {
                        if (!regExp.test(_self.value)) {
                            errMsg = "Inserire dei valori corretti";
                        }
                        if (_self.value.length === 0) {
                            errMsg = "Il campo input non può essere vuoto";
                        }
                    }
                }

                if (formElemId === "#email") {
                    if (!_self.validity.valid) {
                        errMsg = "Inserire una email corretta";
                        if (_self.value.length === 0) {
                            errMsg = "Il campo input non può essere vuoto";
                        }
                    }
                }

                if (formElemId === "#password-old" || formElemId === "#password-new" || formElemId === "#password-new-confirm") {
                    if (!_self.validity.valid) {
                        errMsg = "Inserire una password corretta";
                        if (_self.value.length === 0) {
                            errMsg = "Il campo input non può essere vuoto";
                        }
                    }
                }

                _errElem.textContent = errMsg;
            };
        }, false);

        _form.onkeydown = function () {
            var _submitBtn = this.querySelector(".btn");
            if (_submitBtn.hasAttribute("disabled")) {
                _submitBtn.removeAttribute("disabled");
            }
        };

        _confirmRmBtn.onclick = function () {
            _passRmStep1.classList.add("remove");
            _passRmStep2.classList.remove("remove");
        }

        _closeStep2Btn.onclick = function () {
            $('#remove-profile-modal').modal('hide');
            setTimeout(function () {
                _passRmStep1.classList.remove("remove");
                _passRmStep2.classList.add("remove");
            }, 500);
        }
    }, false);
})();