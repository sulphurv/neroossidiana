<?php
namespace NeroOssidiana {
    use PDO;

    class NavbarRepository
    {
        private $db;

        public function __construct($database)
        {

            $this->db = $database;
        }

        public function getNavbarData()
        {
            try {
                /*
                * Struttura dell'array associativo "$dropDData":
                *
                $dropDData = [
                    "Uomo" => ["Abbigliamento" => ["T-shirt", "Camicie", "Cuffie", "etc" ], "etc" => ["etc" ]],
                    "Donna" => ["Abbigliamento" => ["T-shirt", "Felpe", "Cuffie", "etc" ], "etc" => ["etc" ]]
                ];
                */

                $dropDData = ["Uomo" => [], "Donna" => []];
                foreach ($dropDData as $gender => &$data) {
                    $query = 'SELECT DISTINCT Category FROM Products WHERE Gender="' . $gender . '"';
                    $stmt = $this->db->prepare($query);
                    $stmt->execute();
                    $categories = $stmt->fetchAll();
                    foreach ($categories as $category) {
                        $query2 = 'SELECT DISTINCT Type FROM Products WHERE (Gender="' . $gender . '" OR Gender="Unisex") AND Category="' . $category["Category"] . '"';
                        $stmt = $this->db->prepare($query2);
                        $stmt->execute();
                        $types = $stmt->fetchAll();
                        $arr = [];
                        foreach ($types as $type) {
                            array_push($arr, $type["Type"]);
                        }
                        $data[$category["Category"]] = $arr;
                    }
                }
                return $dropDData;
            } catch (PDOExeption $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        }

        public function getHint($word)
        {
            try {
                $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_NUM);
                $query = "SELECT ProductID, Gender, Name FROM Products WHERE Name LIKE :keyword";
                $keyword = $word . "%";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':keyword', $keyword, PDO::PARAM_STR);
                $stmt->execute();
                $suggestions = $stmt->fetchAll();
                return $suggestions;
            } catch (PDOExeption $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
        }
    }
}