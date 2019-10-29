(function () {
    'use strict';
    window.addEventListener('load', function () {
        var idArr = ["#email", "#password"];
        var _form = document.querySelector('#login form');
        
        _form.addEventListener('submit', function (event) {
            if (this.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
            }
            this.classList.add('was-validated');

            idArr.forEach(function (formElemId) {
                var elem = document.querySelector(formElemId);
                checkIfValid(elem, formElemId);
            });

            function checkIfValid(self, formElemId) {
                var errMsg;
                var _errElem = document.querySelector(formElemId + " ~ .invalid-feedback");

                if (formElemId === "#email") {
                    if (!self.validity.valid) {
                        errMsg = "Inserire una email corretta";
                        if (self.value.length === 0) {
                            errMsg = "Il campo input non può essere vuoto";
                        }
                    }
                }

                if (formElemId === "#password") {
                    if (!self.validity.valid) {
                        errMsg = "Inserire una password corretta";
                        if (self.value.length === 0) {
                            errMsg = "Il campo input non può essere vuoto";
                        }
                    }
                }

                _errElem.textContent = errMsg;
            };
        }, false);
    }, false);
})();