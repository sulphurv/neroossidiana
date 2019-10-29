(function () {
    'use strict';
    window.addEventListener('load', function () {
        var idArr = ["#email", "#password", "#password-confirm"];
        var _form = document.querySelector('#signup form');

        _form.addEventListener('submit', function (event) {
            if (this.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
            }
            this.classList.add('was-validated');

            idArr.forEach(function (formElemId) {
                var _elem = _form.querySelector(formElemId);
                checkIfValid(_elem, formElemId);
            });

            function checkIfValid(_self, formElemId) {
                var errMsg;
                var _errElem = _form.querySelector(formElemId + " ~ .invalid-feedback");

                if (formElemId === "#email") {

                    if (!_self.validity.valid) {
                        errMsg = "Inserire una email corretta";

                        if (_self.value.length === 0) {
                            errMsg = "Il campo input non può essere vuoto";
                        }
                    }
                }

                if (formElemId === "#password" || formElemId === "#password-confirm") {

                    if (!_self.validity.valid) {
                        errMsg = "Inserire una password corretta";

                        if (_self.value.length === 0) {
                            errMsg = "Il campo input non può essere vuoto";
                        }
                    }
                }

                if (formElemId === "#password-confirm") {
                    var _pwd = _form.querySelector("#password");

                    if (_pwd.value && _self.value) {

                        if (_pwd.value !== _self.value) {
                            _self.classList.add("invalid-input");
                            _errElem.style.display = "block";
                            setTimeout(function () { _errElem.textContent = "La password non combacia" }, 50);
                            event.preventDefault();
                            event.stopPropagation();
                        } else {
                            _self.classList.remove("invalid-input");
                            _errElem.style.display = "none";
                            _errElem.textContent = "";
                        }
                    }
                }

                _errElem.textContent = errMsg;
            };
        }, false);
    }, false);
})();