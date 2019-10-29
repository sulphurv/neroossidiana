var _paymentForm = document.querySelector("#payment form");
var _paypalCard = document.querySelector("#paypal-card");
var _radios = _paymentForm.querySelectorAll(".radio");
var _submitError = document.querySelector("#submit-error");

_radios.forEach(function (elem) {
    elem.addEventListener("click", function() {
        var parent = this.parentElement;
        while(parent.parentElement) {
            if (parent.classList.contains("card")) {
                parent.classList.remove("card-error");
                break;
            }
            parent = parent.parentElement;
        }
        _submitError.classList.add("remove");
    });
});

_paymentForm.onsubmit = function () {
    var count = 0;
    var radioError = false;

    _radios.forEach(function (elem) {
        if (elem.checked) {
            count++;
        }
    });

    if (count === 0) {
        if (!radioError) {
            radioError = true;
        }
    } else {
        radioError = false;
    }

    if (radioError) {
        _submitError.classList.remove("remove");
        _paypalCard.classList.add("card-error");
        return false;
    } else {
        _paypalCard.classList.remove("card-error");
        return true;
    }
};