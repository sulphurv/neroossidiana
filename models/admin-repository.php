<?php

namespace NeroOssidiana {

    use PDO;

    class AdminRepository
    {
        private $db;

        public function __construct($database)
        {
            $this->db = $database;
        }

        public function login($formValues)
        {
            try {
                $qSelect = "SELECT AdminID, Name, Password FROM Admin";
                $qWhere = "WHERE Name = ?";

                $query = "$qSelect $qWhere";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$formValues["name"]]);
                $row = $stmt->fetch();

                if (!$row["Name"]) {
                    return ["loggedIn" => false, "error" => "Il nome non è corretto."];
                } else {
                    if (password_verify($formValues["password"], $row["Password"])) {

                        return ["loggedIn" => true, "error" => ""];
                    } else {
                        return ["loggedIn" => false, "error" => "La password non è corretta"];
                    }
                }
            } catch (PDOExeption $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        }

        public function getOrders($order)
        {
            $qSelect = "SELECT * FROM Orders";

            if ($order === "default") {
                $qOrder = "ORDER BY Shipped DESC";
            }

            $query = "$qSelect $qOrder";
            $stmt = $this->db->prepare($query);
            $stmt->execute([]);
            $clientOrders = $stmt->fetchAll();

            return $clientOrders;
        }

        public function getOrder($id)
        {
            $qSelect = "SELECT * FROM Orders";
            $qWhere = "WHERE OrderID = ?";

            $query = "$qSelect $qWhere";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
            $order = $stmt->fetch();

            return $order;
        }

        public function getFilteredOrders($filter)
        {
            $qSelect = "SELECT * FROM Orders";

            if ($filter === "shipped") {
                $qWhere = "WHERE Shipped = 1";
            }

            if ($filter === "pending") {
                $qWhere = "WHERE Shipped = 0";
            }

            $query = "$qSelect $qWhere";
            $stmt = $this->db->prepare($query);
            $stmt->execute([]);
            $clientOrders = $stmt->fetchAll();

            return $clientOrders;
        }

        public function getOrderedProducts($orderID)
        {
            $qSelect = "SELECT * FROM OrderedProducts";
            $qWhere = "WHERE OrderID = ?";
            $query3 = "$qSelect $qWhere";
            $stmt3 = $this->db->prepare($query3);
            $stmt3->execute([$orderID]);
            $orderedProdRows = $stmt3->fetchAll();

            foreach ($orderedProdRows as &$orderedProdRow) {
                $qSelect = "SELECT * FROM ProductDetails";
                $qWhere = "WHERE ProductDetailsID = ?";
                $query4 = "$qSelect $qWhere";
                $stmt4 = $this->db->prepare($query4);
                $stmt4->execute([$orderedProdRow["ProductDetailsID"]]);
                $productDetailsRows = $stmt4->fetchAll();

                foreach ($productDetailsRows as $productDetail) {

                    foreach ($productDetail as $key => $value) {
                        $orderedProdRow[$key] = $value;
                    }

                    $qSelect = "SELECT * FROM Products";
                    $qWhere = "WHERE ProductID = ?";
                    $query5 = "$qSelect $qWhere";
                    $stmt5 = $this->db->prepare($query5);
                    $stmt5->execute([$productDetail["ProductID"]]);
                    $productRows = $stmt5->fetch();

                    foreach ($productRows as $key2 => $value2) {

                        if ($key2 === "Size" || $key2 === "Color") {
                            continue;
                        }

                        $orderedProdRow[$key2] = $value2;
                    }
                }

                unset($orderedProdRow["ProductID"]);
            }

            return $orderedProdRows;
        }

        public function isOrderShipped($orderID)
        {
            $qSelect = "SELECT Shipped FROM Orders";
            $qWhere = "WHERE OrderID = ?";

            $query = "$qSelect $qWhere";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$orderID]);
            $shipped = $stmt->fetch();

            return $shipped;
        }

        public function markAsShipped($orderID)
        {
            try {
                $qUpdate = "UPDATE Orders";
                $qSet = "SET Shipped = 1";
                $qWhere = "WHERE OrderID = ?";

                $query = "$qUpdate $qSet $qWhere";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$orderID]);

                return true;
            } catch (PDOExeption $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        }

        public function getProducts($column = null, $direction = null)
        {
            try {

                if (!$column && !$direction) {
                    $query = "SELECT * FROM Products";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute([]);
                    $products = $stmt->fetchAll();
                }

                if ($column && $direction) {
                    $qSelect = "SELECT * FROM Products";
                    $qOrder = "ORDER BY $column $direction";

                    $query = "$qSelect $qOrder";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute([]);
                    $products = $stmt->fetchAll();
                }

                $products = $this->setProductsAvailability($products);
                return $products;
            } catch (PDOExeption $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        }

        public function setProductsAvailability($products)
        {
            foreach ($products as &$product) {
                $qSelect = "SELECT Availability, Size, Color FROM ProductDetails";
                $qWhere = "WHERE ProductID = ?";

                $query = "$qSelect $qWhere";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$product["ProductID"]]);
                $rows = $stmt->fetchAll();

                $availabilities = [];
                $details = [];

                foreach ($rows as $row) {
                    array_push($availabilities, $row["Availability"]);

                    array_push($details, [
                        "color" => isset($row["Color"]) ? $row["Color"] : null,
                        "size" => isset($row["Size"]) ? $row["Size"] : null,
                        "availability" => $row["Availability"]
                    ]);
                }

                $product["Details"] = $details;

                $uniques = array_count_values($availabilities);

                /* Esaurito */
                if (isset($uniques["black"]) && $uniques["black"] === count($rows)) {
                    $product["Availability"] = "black";
                }

                /* Taglia esaurita */
                if (isset($uniques["green"]) && isset($uniques["black"])) {
                    $product["Availability"] = "grey";
                }

                /* Prodotto in arrivo */
                if (!isset($uniques["green"]) && !isset($uniques["black"])) {
                    $product["Availability"] = "orange";
                }

                /* Prodotto disponibile, nuove taglie in arrivo */
                if (isset($uniques["green"]) && !isset($uniques["black"]) && $uniques["green"] !== count($rows)) {
                    $product["Availability"] = "purple";
                }

                /* Disponibile */
                if (isset($uniques["green"]) && $uniques["green"] === count($rows)) {
                    $product["Availability"] = "green";
                }
            }

            return $products;
        }

        public function getProduct($productID)
        {
            $qSelect = "SELECT * FROM Products";
            $qWhere = "WHERE ProductID = ?";

            $query = "$qSelect $qWhere";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$productID]);
            $product = $stmt->fetch();

            $qSelect = "SELECT * FROM ProductDetails";
            $qWhere = "WHERE ProductID = ?";

            $query = "$qSelect $qWhere";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$productID]);
            $product["productDetails"] = $stmt->fetchAll();

            return $product;
        }

        public function deleteProduct($productID)
        {
            try {
                $qDelete = "DELETE FROM Products";
                $qWhere = "WHERE ProductID = ?";

                $query = "$qDelete $qWhere";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$productID]);

                return ["success" => true];
            } catch (PDOExeption $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        }

        public function updateProduct($product)
        {
            try {
                $qUpdate = "UPDATE Products";
                $qSet = "SET";
                $qWhere = "WHERE ProductID = " . $product["ProductID"];
                $arr = [];
                $i = 0;

                foreach ($product as $key => $value) {
                    $i++;

                    if ($key === "productDetails" || $key === "ProductID") {
                        continue;
                    }

                    array_push($arr, $value);

                    if ($i === count($product) - 1) {
                        $qSet .= " $key = ?";
                        continue;
                    }

                    $qSet .= " $key = ?,";
                }

                $query = "$qUpdate $qSet $qWhere";
                $stmt = $this->db->prepare($query);
                $stmt->execute($arr);

                foreach ($product["productDetails"] as $productDetail) {

                    if (!isset($productDetail["ProductDetailsID"])) {
                        $qInsert = "INSERT INTO ProductDetails(ProductID, Size, Color, Stock, Availability)";
                        $qValues = "VALUES(?, ?, ?, ?, ?)";

                        $query = "$qInsert $qValues";
                        $stmt = $this->db->prepare($query);
                        $stmt->execute([$product["ProductID"], $productDetail["Size"], $productDetail["Color"], $productDetail["Stock"], $productDetail["Availability"]]);

                        continue;
                    }

                    $qUpdate = "UPDATE ProductDetails";
                    $qSet = "SET Size = ?, Color = ?, Stock = ?, Availability = ?";
                    $qWhere = "WHERE ProductDetailsID = ?";
                    $arr = [$productDetail["Size"], $productDetail["Color"], $productDetail["Stock"], $productDetail["Availability"], $productDetail["ProductDetailsID"]];
                    $i = 0;

                    $query = "$qUpdate $qSet $qWhere";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute($arr);
                }

                return ["success" => true];
            } catch (PDOExeption $e) {
                return ["error" => $e->getMessage()];
            }
        }

        public function addNewProduct($product)
        {
            try {
                $qInsert = "INSERT INTO Products(";
                $qValues = "VALUES (";
                $arr = [];
                $i = 0;

                foreach ($product as $key => $value) {
                    $i++;

                    if ($key === "productDetails") {
                        continue;
                    }

                    array_push($arr, $value);

                    if ($i === count($product)) {
                        $qInsert .= "$key)";
                        $qValues .= "?)";

                        break;
                    }

                    $qInsert .= "$key, ";
                    $qValues .= "?, ";
                }

                $query = "$qInsert $qValues";
                $stmt = $this->db->prepare($query);
                $stmt->execute($arr);

                $qLastInsertID =  "SELECT LAST_INSERT_ID()";

                $query = "$qLastInsertID";
                $stmt = $this->db->prepare($query);
                $stmt->execute();
                $result = $stmt->fetch();

                $id = $result["LAST_INSERT_ID()"];

                foreach ($product["productDetails"] as $productDetail) {
                    $qInsert = "INSERT INTO ProductDetails(ProductID, Size, Color, Stock, Availability)";
                    $qValues = "VALUES(?, ?, ?, ?, ?)";

                    $query = "$qInsert $qValues";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute([$id, $productDetail["Size"], $productDetail["Color"], $productDetail["Stock"], $productDetail["Availability"]]);
                }
                
                return $id;
            } catch (PDOExeption $e) {
                return ["error" => $e->getMessage()];
            }
        }

        public function getHint($word)
        {
            try {
                $qSelect = "SELECT ProductID, Name, Gender, Category, Type FROM Products";
                $qWhere = "WHERE ProductID LIKE :keyword OR 
                                 Name LIKE :keyword OR
                                 Gender LIKE :keyword OR
                                 Category LIKE :keyword OR
                                 Type LIKE :keyword";

                $query = "$qSelect $qWhere";
                $keyword = $word . "%";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':keyword', $keyword, PDO::PARAM_STR);
                $stmt->execute();
                $suggestions = $stmt->fetchAll();
                return ["keyword" => $word, "hints" => $suggestions];
            } catch (PDOExeption $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        }

        public function getFilteredProducts($word)
        {
            try {
                $qSelect = "SELECT * FROM Products";
                $qWhere = "WHERE ProductID LIKE :keyword OR 
                                 Name LIKE :keyword OR
                                 Gender LIKE :keyword OR
                                 Category LIKE :keyword OR
                                 Type LIKE :keyword";

                $query = "$qSelect $qWhere";
                $keyword = $word . "%";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':keyword', $keyword, PDO::PARAM_STR);
                $stmt->execute();
                $products = $stmt->fetchAll();

                $products = $this->setProductsAvailability($products);
                return $products;
            } catch (PDOExeption $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        }

        public function getCategories($gender)
        {
            $query = "SELECT DISTINCT Category FROM Products WHERE Gender = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$gender]);
            $categories = $stmt->fetchAll();

            return $categories;
        }

        public function getTypes($gender, $category)
        {
            $query = 'SELECT DISTINCT Type FROM Products WHERE (Gender = ? OR Gender = "Unisex") AND Category = ?';
            $stmt = $this->db->prepare($query);
            $stmt->execute([$gender, $category]);
            $types = $stmt->fetchAll();

            return $types;
        }
    }
}
