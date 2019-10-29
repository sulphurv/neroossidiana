(function () {
    'use strict';
    window.addEventListener('load', function () {
        
        var idArr = ["#name", "#email", "#object", "#message"];
        var _form = document.querySelector('#contact-us form');
        
        _form.addEventListener('submit', function (event) {

            if (this.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            this.classList.add('was-validated');

            idArr.forEach(function (formElemId) {
                var elem = document.querySelector(formElemId);
                console.log(elem)
                checkIfValid(elem, formElemId);
            });

            function checkIfValid(_self, formElemId) {
                var regExp, errMsg;
                var _errElem = _form.querySelector(formElemId + " ~ .invalid-feedback");

                if (formElemId === "#name") {
                    regExp = new RegExp(_self.pattern);

                    console.log(_self.validity);
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

                if (formElemId === "#object") {

                    if (!_self.validity.valid) {

                        if (_self.value.length === 0) {
                            errMsg = "Il campo input non può essere vuoto";
                        }
                    }
                }

                if (formElemId === "#message") {

                    if (!_self.validity.valid) {

                        if (_self.value.length === 0) {
                            errMsg = "Il campo input non può essere vuoto";
                        }
                    }
                }

                _errElem.textContent = errMsg;
            };
        }, false);
    }, false);
})();