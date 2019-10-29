<?php
namespace NeroOssidiana {

    class Order
    {
        public function add($total)
        {
            $date = date_create();
            $currDate = date_format($date, "Y/m/d");
            $_SESSION["Order"] = ["date" => $currDate, "total" => $total];
        }

        public function clear() {
            $_SESSION["Order"] = [];
        }
     }
}


