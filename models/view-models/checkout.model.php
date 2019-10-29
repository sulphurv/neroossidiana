<?php
namespace NeroOssidiana {
    
    class CheckoutModel
    {
        # IMPORTANTE: è necessario implementare ulteriormente questa funzione
        public function getShippingCosts($country = null)
        {
            if ($country == "italy" || !$country) {
                $cost = 6.95;
            }

            return $cost;
        }
    }
}
