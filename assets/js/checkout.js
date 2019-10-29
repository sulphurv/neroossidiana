

(function () {
    'use strict';
    window.addEventListener('load', function () {
        var idArr = ["#first-name", "#last-name", "#email", "#address", "#address-num", "#address2", "#city", "#post-code", "#countries", "#telephone"];

        var _checkoutForm = document.querySelector("#checkout-form");

        _checkoutForm.addEventListener('submit', function (event) {
            if (_checkoutForm.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
            }
            _checkoutForm.classList.add('was-validated');

            idArr.forEach(function (formElemId) {
                var elem = document.querySelector(formElemId);
                checkIfValid(elem, formElemId);
            });
        }, false);

        idArr.forEach(function (formElemId) {
            document.querySelector(formElemId)
                .addEventListener("input", function () {
                    var self = this;
                    checkIfValid(self, formElemId);
                });
        });

        function checkIfValid(self, formElemId) {
            var regExp, errMsg;
            var _errElem = document.querySelector(formElemId + " ~ .invalid-feedback");

            if (formElemId === "#email") {
                if (!self.validity.valid) {
                    errMsg = "Inserire una email corretta";
                    if (self.value.length === 0) {
                        errMsg = "Il campo input non può essere vuoto";
                    }
                }
            }

            if (formElemId === "#first-name" || formElemId === "#last-name" || formElemId === "#address" || formElemId === "#city") {
                regExp = new RegExp(self.pattern);
                if (!self.validity.valid) {
                    if (!regExp.test(self.value)) {
                        errMsg = "Inserire dei valori corretti";
                    }
                    if (self.value.length === 0) {
                        errMsg = "Il campo input non può essere vuoto";
                    }
                }
            }

            if (formElemId === "#address-num" || formElemId === "#post-code") {
                regExp = new RegExp(self.pattern);
                if (!self.validity.valid) {
                    if (!regExp.test(self.value)) {
                        errMsg = "Inserire dei valori corretti";
                    }
                    if (self.value.length === 0) {
                        errMsg = "Il campo input non può essere vuoto";
                    }
                }
            }

            if (formElemId === "#address2") {
                regExp = new RegExp(self.pattern);
                if (!self.validity.valid) {
                    if (!regExp.test(self.value)) {
                        errMsg = "Inserire dei valori corretti";
                    }
                    if (self.value.length === 0) {
                        errMsg = "Il campo input non può essere vuoto";
                    }
                }
            }

            if (formElemId === "#telephone") {
                regExp = new RegExp(self.pattern);
                if (!self.validity.valid) {
                    if (!regExp.test(self.value)) {
                        errMsg = "Inserire dei valori corretti";
                    }
                    if (self.value.length === 0) {
                        errMsg = "Il campo input non può essere vuoto";
                    }
                }
            }

            _errElem.textContent = errMsg;
        }
    }, false);
})();