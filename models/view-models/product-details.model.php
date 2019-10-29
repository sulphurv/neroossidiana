<?php
namespace NeroOssidiana {
    
    class ProductDetailsModel
    {
        public function buildProductArrays($product)
        {
            foreach ($product as $key => $value) {
                if ($key === "Description") {
                    continue;
                }
                
                if (strpos($value, ",")) {
                    $arr = explode(", ", $value);
                    $product[$key] = $arr;
                }
            }

            # /* TEMPORANEO!
            is_array($product["Images"]) ? $product["Images"] : $product["Images"] = [$product["Images"]];

            if (isset($product["Size"])) {
                is_array($product["Size"]) ? $product["Size"] : $product["Size"] = [$product["Size"]];
            } else {
                $product["Size"] = [];
            }

            if (isset($product["Color"])) {
                is_array($product["Color"]) ? $product["Color"] : $product["Color"] = [$product["Color"]];
            } else {
                $product["Color"] = [];
            }
            # */

            return $product;
        }
    }
}


