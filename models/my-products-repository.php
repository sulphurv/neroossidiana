<?php

namespace NeroOssidiana {

    class MyProductsRepository
    {
        private $db;

        public function __construct($database)
        {

            $this->db = $database;
        }

        public function getProducts($params, $itemsPerPage)
        {
            try {
                $startPos = (((int) $params["Number"] - 1) * $itemsPerPage);

                $qSelect = "SELECT ProductID, Name, Price, Images, Discount FROM Products";
                $qWhere = "";
                $qTake = "LIMIT $itemsPerPage OFFSET $startPos";

                if (isset($params["Gender"])) {

                    $qWhere = "WHERE Gender = ?";
                    $paramsArr = [$params["Gender"]];

                    if (isset($params["Category"])) {

                        if (strpos($params["Category"], "-")) {
                            $params["Category"] = implode(" ", explode("-", $params["Category"]));
                        }

                        $qWhere = "WHERE Gender = ? AND Category = ?";
                        $paramsArr = [$params["Gender"], $params["Category"]];

                        if (isset($params["Type"])) {

                            if (strpos($params["Type"], "-")) {
                                $params["Type"] = implode(" ", explode("-", $params["Type"]));
                            }

                            $qWhere = "WHERE Gender = ? AND Category = ? AND Type = ?";
                            $paramsArr = [$params["Gender"], $params["Category"], $params["Type"]];
                        }
                    }
                }

                $query = "$qSelect $qWhere $qTake";
                $stmt = $this->db->prepare($query);
                $stmt->execute($paramsArr);
                $products = $stmt->fetchAll();

                foreach ($products as &$product) {
                    $qSelect = "SELECT Availability FROM ProductDetails";
                    $qWhere2 = "WHERE ProductID = ?";

                    $query = "$qSelect $qWhere2";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute([$product["ProductID"]]);
                    $availabilityDetails = $stmt->fetchAll();

                    $unique = [];

                    foreach ($availabilityDetails as $availability) {
                        if ($availability["Availability"] === "black") {
                            array_push($unique, $availability);
                        }
                    }

                    if (count($unique) === count($availabilityDetails)) {
                        $product["OutOfStock"] = true;
                    } else {
                        $product["OutOfStock"] = false;
                    }
                }

                $qCount = "SELECT COUNT(ProductID) FROM Products";

                $query = "$qCount $qWhere";
                $stmt2 = $this->db->prepare($query);
                $stmt2->execute($paramsArr);
                $productsTotalNum = $stmt2->fetch();

                return ["products" => $products, "productsTotalNum" => $productsTotalNum["COUNT(ProductID)"]];
            } catch (PDOExeption $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        }

        public function getProductData($id)
        {
            try {
                $query = "SELECT * FROM Products WHERE ProductID = ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$id]);
                $product = $stmt->fetch();

                $query = "SELECT * FROM ProductDetails WHERE ProductID = ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$id]);
                $productDetails = $stmt->fetchAll();
                return ["product" => $product, "productDetails" => $productDetails];
            } catch (PDOExeption $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        }

        public function getCarouselProducts($condition, $array)
        {
            try {
                $query = "SELECT ProductID, Name, Price, Images, Discount FROM Products " . $condition;
                $stmt = $this->db->prepare($query);
                $stmt->execute($array);
                $products = $stmt->fetchAll();

                foreach ($products as &$product) {
                    $qSelect = "SELECT Availability FROM ProductDetails";
                    $qWhere = "WHERE ProductID = ?";

                    $query = "$qSelect $qWhere";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute([$product["ProductID"]]);
                    $availabilityDetails = $stmt->fetchAll();
                    
                    $unique = [];

                    foreach ($availabilityDetails as $availability) {
                        if ($availability["Availability"] === "black") {
                            array_push($unique, $availability);
                        }
                    }

                    if (count($unique) === count($availabilityDetails)) {
                        $product["OutOfStock"] = true;
                    } else {
                        $product["OutOfStock"] = false;
                    }
                }

                return $products;
            } catch (PDOExeption $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        }

        public function getSearchedProducts($value)
        {
            try {
                $query = "SELECT ProductID, Name, Price, Images, Discount FROM Products
                 WHERE Name LIKE ?
                 OR Gender LIKE ? 
                 OR Category LIKE ? 
                 OR Type LIKE ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute(["%" . $value . "%", "%" . $value . "%", "%" . $value . "%", "%" . $value . "%"]);
                $products = $stmt->fetchAll();

                foreach ($products as &$product) {
                    $qSelect = "SELECT Availability FROM ProductDetails";
                    $qWhere2 = "WHERE ProductID = ?";

                    $query = "$qSelect $qWhere2";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute([$product["ProductID"]]);
                    $availabilityDetails = $stmt->fetchAll();

                    $unique = [];

                    foreach ($availabilityDetails as $availability) {
                        if ($availability["Availability"] === "black") {
                            array_push($unique, $availability);
                        }
                    }

                    if (count($unique) === count($availabilityDetails)) {
                        $product["OutOfStock"] = true;
                    } else {
                        $product["OutOfStock"] = false;
                    }
                }

                return $products;
            } catch (PDOExeption $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        }
    }
}
