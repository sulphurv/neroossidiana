<?php
namespace NeroOssidiana {
    class CheckoutRepository
    {
        private $db;

        public function __construct($customerbase)
        {
            $this->db = $customerbase;
        }

        public function placeOrder()
        {
            try {
                $customer = $_SESSION["Customer"];
                $order = $_SESSION["Order"];
                $cart = $_SESSION["Cart"];

                if (!$_SESSION["loggedIn"] || count($customer) == 0) {
                    $qInsert = "INSERT INTO Customers(FirstName, LastName, Email, Address1, Address2, City, Zipcode, Country, Phone)";
                    $qValues = "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $valuesArr = [$customer["FirstName"], $customer["LastName"], $customer["Email"], ($customer["Address1"] . " " . $customer["Address1Num"]), $customer["Address2"], $customer["City"], $customer["ZipCode"], $customer["Country"], $customer["Phone"]];
                    # inserisci le informazione sul cliente nel database
                    $query = "$qInsert $qValues";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute($valuesArr);

                    # ottiemi l'ultimo id generato da MYSQL, il quale verrà utilizzato per definire la "foreign key" nel row che andremo a creare nella tabella "Orders".
                    $customerID = $this->db->lastInsertId();
                } else {
                    $customerID = $customer["CustomerID"];
                }

                $qInsert = "INSERT INTO Orders(CustomerID, Date, Amount, Shipped)";
                $qValues = "VALUES (?, ?, ?, ?)";
                $valuesArr = [$customerID, $order["date"], $order["total"], 0];
                $query = "$qInsert $qValues";
                $stmt = $this->db->prepare($query);
                $stmt->execute($valuesArr);
                
                $orderID = $this->db->lastInsertId();

                $qInsert = "INSERT INTO OrderedProducts(OrderID, ProductDetailsID, Size, Color, Quantity)";
                $qValues = "VALUES(?, ?, ?, ?, ?)";

                $qUpdate = "UPDATE ProductDetails";
                $qSet = "SET Stock = ?";
                $qWhere  = "WHERE ProductDetailsID = ?";

                foreach ($cart as $cartItem) {
                    $valuesArr = [$orderID, $cartItem->product["ProductDetailsID"], $cartItem->product["Size"], $cartItem->product["Color"], $cartItem->quantity];
                    $query = "$qInsert $qValues";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute($valuesArr);

                    /* Aggiorna la quantità disponibile in magazzino del prodotto */
                    if ($cartItem->product["Stock"] > 0) {
                        $newStockVal = $cartItem->product["Stock"] - $cartItem->quantity;
                        $valuesArr2 = [$newStockVal, $cartItem->product["ProductDetailsID"]];
                    } else {
                        if ($cartItem->product["Availability"] === "black") {
                            echo "Error: Product unavailable.";
                            exit;
                        }

                        if ($cartItem->product["Availability"] !== "green" && $cartItem->product["Availability"] !== "black") {
                            $valuesArr2 = [0, $cartItem->product["ProductDetailsID"]];
                        }
                    }

                    if ($newStockVal === 0 && $cartItem->product["Availability"] === "green") {
                        $qSet = "SET Stock = ?, Availability = ?";
                        $valuesArr2 = [0, "black", $cartItem->product["ProductDetailsID"]];
                    }

                    $query2 = "$qUpdate $qSet $qWhere";
                    $stmt = $this->db->prepare($query2);
                    $stmt->execute($valuesArr2);
                }
            } catch (PDOExeption $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        }
    }
}

