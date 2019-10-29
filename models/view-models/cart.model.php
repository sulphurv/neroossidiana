<?php
namespace NeroOssidiana {
    
    class CartModel
    {
        public function getDeliveryDate()
        {
            foreach ($_SESSION["Cart"] as $cartItem) {

                if ($cartItem->product["Availability"] == "red") {
                    $availability = "red";
                    break;
                }

                if ($cartItem->product["Availability"] == "yellow") {
                    $availability = "yellow";
                }

                if ($cartItem->product["Availability"] == "green") {
                    
                    if (isset($availability) && $availability == "yellow") {
                        continue;
                    }
                    $availability = "green";
                }
            }

            switch ($availability) {
                case "green":
                    $time = "+1 week";
                    break;
                case "yellow":
                    $time = "+2 weeks";
                    break;
                case "red":
                    $time = "+4 weeks";
                    break;
            }

            $date = date_create();
            $d = date_format($date, "l");

            # se il giorno di oggi è un sabato o una domenica aggiungi rispettivamente 2 giorni e 1 giorno alla data di spedizione del pacco.
            if ($d == "Saturday" || $d == "Sunday") {
                if ($d == "Saturday") {
                    date_add($date, date_interval_create_from_date_string("+2 days"));
                }
                if ($d == "Sunday") {
                    date_add($date, date_interval_create_from_date_string("+1 days"));
                }
            }

            date_add($date, date_interval_create_from_date_string($time));
            $d = date_format($date, "l");

            # se il pacco si prevede arrivare di sabato o domenica aggiungi rispettivamente 2 giorni e 1 giorno alla data di arrivo effettivo del pacco.
            if ($d == "Saturday" || $d == "Sunday") {
                if ($d == "Saturday") {
                    date_add($date, date_interval_create_from_date_string("+2 days"));
                }
                if ($d == "Sunday") {
                    date_add($date, date_interval_create_from_date_string("+1 days"));
                }
            }

            $minDateOfArrival = implode("/", explode("-", date_format($date, "d-m")));

            date_add($date, date_interval_create_from_date_string("+4 days"));
            $d = date_format($date, "l");

            if ($d == "Saturday" || $d == "Sunday") {
                if ($d == "Saturday") {
                    date_add($date, date_interval_create_from_date_string("+2 days"));
                }
                if ($d == "Sunday") {
                    date_add($date, date_interval_create_from_date_string("+1 days"));
                }
            }

            $maxDateOfArrival = implode("/", explode("-", date_format($date, "d-m")));

            $str = "La consegna avverrà tra il $minDateOfArrival ed il $maxDateOfArrival.";

            return $str;
        }
    }
}
