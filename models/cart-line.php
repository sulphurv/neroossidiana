<?php

namespace NeroOssidiana {

    class Cart
    {
        public function addItem($product, $prodOptions)
        {
            if ($product["Availability"] === "black") {
                return;
            }
            # i nomi delle proprietà dell'array associativo devono essere uguali alle corrispettive proprietà all'interno di "$products"
            foreach ($prodOptions as $option => $value) {
                $product[$option] = $value;
            }

            //Console::log($product);

            foreach ($_SESSION["Cart"] as $line) {
                # se l'id del prodotto è lo stesso e non c'è differenza tra le opzioni selezionate, aumenta la quantità di quel prodotto.
                if ($line->product["ProductID"] === $product["ProductID"]) {

                    if ($line->product["ProductDetailsID"] == $product["ProductDetailsID"]) {
                        $nLine = $line;
                        $line->quantity += 1;
                    }
                }
            }

            Console::log($product);

            if (!isset($nLine) || count($_SESSION["Cart"]) === 0) {
                $cartLine = new CartLine();
                $cartLine->product = $product;
                $cartLine->quantity = 1;
                array_push($_SESSION["Cart"], $cartLine);
            }
        }

        public function updateQuantity($formValues)
        {
            $_SESSION["Cart"][$formValues["index"]]->quantity = (int) $formValues["quantity"];
        }

        public function removeItem($index)
        {
            array_splice($_SESSION["Cart"], $index, 1);
        }

        public function clear()
        {
            $_SESSION["Cart"] = [];
        }

        public function getTotal()
        {
            $tot = 0;
            foreach ($_SESSION["Cart"] as $cartItem) {
                $tot += ($cartItem->product["EndPrice"] * $cartItem->quantity);
            }
            return $tot;
        }
    }

    class CartLine
    {
        public $product;
        public $quantity;
    }
}
