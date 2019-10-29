<?php
namespace NeroOssidiana {

    class AccountRepository
    {
        private $db;

        public function __construct($database)
        {
            $this->db = $database;
        }

        public function createAccount($formValues)
        {
            $formValues["email"] = filter_var($formValues["email"], FILTER_SANITIZE_EMAIL);

            try {
                $qSelect = "SELECT Username, Email FROM Users";
                $qWhere = "WHERE Email = ?";

                $query = "$qSelect $qWhere";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$formValues["email"]]);
                $row = $stmt->fetch();

                if (!$row["Email"]) {
                    $qInsert = "INSERT INTO Users (Email, Password, JoinDate)";
                    $qValues = "VALUES (?, ?, ?)";

                    $password = password_hash($formValues["password"], PASSWORD_DEFAULT);
                    $date = date_create();
                    $date = date_format($date, "Y/m/d");

                    $query = "$qInsert $qValues";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute([$formValues["email"], $password, $date]);

                    $userID = $this->db->lastInsertId();

                    $_SESSION["User"]["ID"] = $userID;
                    $_SESSION["User"]["Email"] = $formValues["email"];
                    $_SESSION["User"]["Username"] = $row["Username"];
                    $_SESSION["User"]["JoinDate"] = $date;

                    return ["loggedIn" => true, "error" => ""];
                } else {
                    return ["loggedIn" => false, "error" => "Questa mail è già utilizzata da un altro profilo."];
                }
            } catch (PDOExeption $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        }

        public function login($formValues)
        {
            $formValues["email"] = filter_var($formValues["email"], FILTER_SANITIZE_EMAIL);

            try {
                $qSelect = "SELECT UserID, Username, Email, JoinDate, Password FROM Users";
                $qWhere = "WHERE Email = ?";

                $query = "$qSelect $qWhere";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$formValues["email"]]);
                $row = $stmt->fetch();

                if (!$row["Email"]) {
                    return ["loggedIn" => false, "error" => "L'email inserita non esiste"];
                } else {
                    if (password_verify($formValues["password"], $row["Password"])) {
                        $_SESSION["User"]["ID"] = $row["UserID"];
                        $_SESSION["User"]["Email"] = $row["Email"];
                        $_SESSION["User"]["Username"] = $row["Username"];
                        $_SESSION["User"]["JoinDate"] = $row["JoinDate"];

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

        public function logout()
        {
            $_SESSION["loggedIn"] = false;
            $_SESSION["User"] = [];
            $_SESSION["Customer"] = [];
            $_SESSION["CustomerOrders"] = [];
        }

        public function insertAddress($formValues)
        {
            $user = $_SESSION["User"];

            try {
                $qSelect = "SELECT UserID FROM Customers";
                $qWhere = "WHERE UserID = ?";

                $query = "$qSelect $qWhere";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$user["ID"]]);
                $row = $stmt->fetch();

                /* se è presente un account e un indirizzo ad esso associato aggiornalo */
                if ($user["ID"] && $row["UserID"]) {
                    $qUpdate = "UPDATE Customers";
                    $qSet = "SET FirstName = ?, LastName = ?, Email = ?, Address1 = ?, Address2 = ?, City = ?, Zipcode = ?, Country = ?, Phone = ?, UserID = ?";
                    $qWhere = "WHERE UserID = ?";

                    $valuesArr = [$formValues["FirstName"], $formValues["LastName"], $user["Email"], ($formValues["Address1"] . " " . $formValues["Address1Num"]), $formValues["Address2"], $formValues["City"], $formValues["ZipCode"], $formValues["Country"], $formValues["Phone"], $user["ID"], $user["ID"]];
                    $query = "$qUpdate $qSet $qWhere";
                    /* se è presente un account ma non un indirizzo ad esso associato aggiungilo */
                } else if ($user["ID"] && !$row) {
                    $qInsert = "INSERT INTO Customers(FirstName, LastName, Email, Address1, Address2, City, Zipcode, Country, Phone, UserID)";
                    $qValues = "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    $valuesArr = [$formValues["FirstName"], $formValues["LastName"], $user["Email"], ($formValues["Address1"] . " " . $formValues["Address1Num"]), $formValues["Address2"], $formValues["City"], $formValues["ZipCode"], $formValues["Country"], $formValues["Phone"], $user["ID"]];
                    $query = "$qInsert $qValues";
                    /* inserisci un indirizzo non associato ad un account */
                } else {
                    $qInsert = "INSERT INTO Customers(FirstName, LastName, Email, Address1, Address2, City, Zipcode, Country, Phone, UserID)";
                    $qValues = "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    $valuesArr = [$formValues["FirstName"], $formValues["LastName"], $user["Email"], ($formValues["Address1"] . " " . $formValues["Address1Num"]), $formValues["Address2"], $formValues["City"], $formValues["ZipCode"], $formValues["Country"], $formValues["Phone"], null];
                    $query = "$qInsert $qValues";
                }

                $stmt = $this->db->prepare($query);
                $stmt->execute($valuesArr);
            } catch (PDOExeption $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        }

        public function getUserData()
        {
            $userID = $_SESSION["User"]["ID"];

            try {
                $qSelect = "SELECT * FROM Customers";
                $qWhere = "WHERE UserID = ?";

                $query = "$qSelect $qWhere";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$userID]);
                $row = $stmt->fetch();

                if ($row) {
                    foreach ($row as $key => $value) {
                        if ($key === "UserID") {
                            continue;
                        }
                        $_SESSION["Customer"][$key] = $value;
                    }

                    $address = preg_split('/ \d+/', $_SESSION["Customer"]["Address1"]);
                    $addressNum = preg_split('/([A-Za-z] ?)+/', $_SESSION["Customer"]["Address1"], -1, PREG_SPLIT_NO_EMPTY);
                    $_SESSION["Customer"]["Address1"] = $address[0];
                    $_SESSION["Customer"]["Address1Num"] = $addressNum[0];

                    $qSelect = "SELECT * FROM Orders";
                    $qWhere = "WHERE CustomerID = ?";
                    $query2 = "$qSelect $qWhere";
                    $stmt2 = $this->db->prepare($query2);
                    $stmt2->execute([$row["CustomerID"]]);
                    $orderRows = $stmt2->fetchAll();

                    if (count($orderRows) > 0) {
                        $i = 0;

                        foreach ($orderRows as $orderRow) {
                            $qSelect = "SELECT * FROM OrderedProducts";
                            $qWhere = "WHERE OrderID = ?";
                            $query3 = "$qSelect $qWhere";
                            $stmt3 = $this->db->prepare($query3);
                            $stmt3->execute([$orderRow["OrderID"]]);
                            $orderedProdRows = $stmt3->fetchAll();

                            foreach ($orderedProdRows as &$orderedProdRow) {
                                $qSelect = "SELECT * FROM ProductDetails";
                                $qWhere = "WHERE ProductDetailsID = ?";
                                $query4 = "$qSelect $qWhere";
                                $stmt4 = $this->db->prepare($query4);
                                $stmt4->execute([$orderedProdRow["ProductDetailsID"]]);
                                $productDetailsRows = $stmt4->fetchAll();

                                foreach ($productDetailsRows as $productDetail) {

                                    foreach ($productDetail as $key2 => $value2) {
                                        $orderedProdRow[$key2] = $value2;
                                    }

                                    $qSelect = "SELECT * FROM Products";
                                    $qWhere = "WHERE ProductID = ?";
                                    $query4 = "$qSelect $qWhere";
                                    $stmt4 = $this->db->prepare($query4);
                                    $stmt4->execute([$productDetail["ProductID"]]);
                                    $productRows = $stmt4->fetchAll();

                                    foreach ($productRows as $product) {

                                        foreach ($product as $key3 => $value3) {

                                            if ($key3 === "Color" || $key3 === "Size") {
                                                continue;
                                            }

                                            $orderedProdRow[$key3] = $value3;
                                        }
                                    }
                                }

                                unset($orderedProdRow["ProductID"]);
                            }

                            $_SESSION["CustomerOrders"][$i]["Number"] = $orderRow["OrderID"];
                            $_SESSION["CustomerOrders"][$i]["Amount"] = $orderRow["Amount"];
                            $_SESSION["CustomerOrders"][$i]["Date"] = $orderRow["Date"];
                            $_SESSION["CustomerOrders"][$i]["Shipped"] = $orderRow["Shipped"];
                            $_SESSION["CustomerOrders"][$i]["Products"] = $orderedProdRows;

                            $i++;
                        }
                    } else {
                        $_SESSION["CustomerOrders"] = [];
                    }
                } else {
                    $_SESSION["Customer"] = [];
                }
            } catch (PDOExeption $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        }

        public function updateProfile($formValues)
        {
            $userID = $_SESSION["User"]["ID"];

            try {
                if ($formValues["Username"]) {
                    $qUpdate = "UPDATE Users";
                    $qSet = "SET Username = ?";
                    $qWhere = "WHERE UserID = ?";

                    $query = "$qUpdate $qSet $qWhere";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute([$formValues["Username"], $userID]);

                    $_SESSION["User"]["Username"] = $formValues["Username"];
                }

                if ($formValues["Email"]) {
                    $qUpdate = "UPDATE Users";
                    $qSet = "SET Email = ?";
                    $qWhere = "WHERE UserID = ?";

                    $query = "$qUpdate $qSet $qWhere";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute([$formValues["Email"], $userID]);

                    $_SESSION["User"]["Email"] = $formValues["Email"];
                }

                if ($formValues["Password-old"] && $formValues["Password-new"] && $formValues["Password-new-confirm"]) {
                    $qSelect = "SELECT Password FROM Users";
                    $qWhere = "WHERE UserID = ?";

                    $query = "$qSelect $qWhere";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute([$userID]);
                    $row = $stmt->fetch();

                    if (password_verify($formValues["Password-old"], $row["Password"])) {
                        $qUpdate = "UPDATE Users";
                        $qSet = "SET Password = ?";
                        $qWhere = "WHERE UserID = ?";

                        $password = password_hash($formValues["Password-new-confirm"], PASSWORD_DEFAULT);

                        $query = "$qUpdate $qSet $qWhere";
                        $stmt = $this->db->prepare($query);
                        $stmt->execute([$password, $userID]);
                    } else {
                        return ["error" => "La password inserita non è corretta."];
                    }
                }

                return ["error" => ""];
            } catch (PDOExeption $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        }

        public function deleteProfile($formValues)
        {
            try {
                $qSelect = "SELECT Password FROM Users";
                $qWhere = "WHERE Email = ?";

                $query = "$qSelect $qWhere";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$_SESSION["User"]["Email"]]);
                $row = $stmt->fetch();

                if (password_verify($formValues["Password"], $row["Password"])) {
                    $qSelect = "SELECT UserID FROM Customers";
                    $qWhere = "WHERE UserID = ?";

                    $query = "$qSelect $qWhere";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute([$formValues["UserID"]]);
                    $row = $stmt->fetch();

                    if ($row) {
                        $qUpdate = "UPDATE Customers";
                        $qSet = "SET FirstName = ?, LastName = ?, Email = ?, Address1 = ?, Address2 = ?, Phone = ?, UserID = ?";
                        $qWhere = "WHERE UserID = ?";

                        $query = "$qUpdate $qSet $qWhere";
                        $stmt = $this->db->prepare($query);
                        $stmt->execute(["REMOVED", "REMOVED", "REMOVED", "REMOVED", "REMOVED", 0, null, $formValues["UserID"]]);
                    }

                    $qDelete = "DELETE FROM Users";
                    $qWhere = "WHERE UserID = ?";

                    $query = "$qDelete $qWhere";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute([$formValues["UserID"]]);

                    return ["success" => true];
                } else {
                    return ["success" => false];
                }
            } catch (PDOExeption $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        }
    }
}
