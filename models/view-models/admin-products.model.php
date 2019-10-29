<?php

namespace NeroOssidiana {

    use Slim\Http\UploadedFile;

    class AdminProductsModel
    {
        public function OrderProductsByColor($products, $direction)
        {
            function filter($productsArr, $status)
            {
                $result = [];

                foreach ($productsArr as $product) {
                    if ($product["Availability"] === $status) {
                        array_push($result, $product);
                    }
                }

                return $result;
            };

            $black = filter($products, "black");
            $grey = filter($products, "grey");
            $orange = filter($products, "orange");
            $purple = filter($products, "purple");
            $green = filter($products, "green");

            if ($direction === "DESC") {
                $products = array_merge($black, $grey, $orange, $purple, $green);
            }

            if ($direction === "ASC") {
                $products = array_merge($green, $purple, $orange, $grey, $black);
            }

            return $products;
        }

        public function FilterProductsByColor($products, $color) {
            return array_filter($products, function ($product) use ($color){
                return $product["Availability"] === $color;
            });
        }

        public function moveUploadedFile($directory, UploadedFile $uploadedFile)
        {
            /* Crea nome file casuale */
            
            /* $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
            $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
            $filename = sprintf('%s.%0.8s', $basename, $extension);

            $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

            return $filename; */

            $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $uploadedFile->getClientFilename());

            return $uploadedFile->getClientFilename();
        }
    }
}
