<?php

namespace NeroOssidiana {

    use \Slim\Views\PhpRenderer as ViewRenderer;
    use NeroOssidiana\AdminRepository as AdminRepo;
    use NeroOssidiana\AdminProductsModel;

    class AdminController
    {
        protected $view;
        private $repository;
        private $model;

        public function __construct(ViewRenderer $view, AdminRepo $repo)
        {
            $this->view = $view;
            $this->repository = $repo;
            $this->model = new AdminProductsModel();
        }

        public function __invoke($request, $response, $args)
        {
            if ($request->isGet()) {
                $arr = ["error" => ""];
            }

            if ($request->isPost()) {
                $formValues = $request->getParsedBody();
                $result = $this->repository->login($formValues);

                if ($result["loggedIn"]) {
                    $_SESSION["isAdmin"] = true;
                    header("Location: /Admin/Ordini");
                    exit;
                } else {
                    $arr = ["error" => $result["error"]];
                }
            }

            $response = $this->view->render($response, 'admin-login.phtml', $arr);
            return $response;
        }

        public function Orders($request, $response, $args)
        {
            if (isset($args["Action"])) {

                if ($args["Action"] === "In-lavorazione") {
                    $clientOrders = $this->repository->getFilteredOrders("pending");
                }

                if ($args["Action"] === "Spediti") {
                    $clientOrders = $this->repository->getFilteredOrders("shipped");
                }
            } else {
                $clientOrders = $this->repository->getOrders("default");
            }

            $response = $this->view->render($response, 'admin-orders.phtml', ["clientOrders" => $clientOrders]);
            return $response;
        }

        public function OrderDetails($request, $response, $args)
        {
            if ($request->isPost()) {
                $formValues = $request->getParsedBody();

                if (isset($formValues["is-shipped"])) {
                    $this->repository->markAsShipped($args["OrderID"]);

                    return $response->withRedirect("/Admin/Ordini");
                }
            }

            $orderedProducts = $this->repository->getOrderedProducts($args["OrderID"]);
            $isOrderShipped = $this->repository->isOrderShipped($args["OrderID"]);
            $orderData = $this->repository->getOrder($args["OrderID"]);

            $arr = ["orderData" => $orderData, "orderedProducts" => $orderedProducts, "orderID" => $args["OrderID"], "isShipped" => $isOrderShipped["Shipped"]];

            $response = $this->view->render($response, 'admin-order-details.phtml', $arr);

            return $response;
        }

        public function Products($request, $response, $args)
        {
            $products = $this->repository->getProducts();

            if (isset($args["Action"])) {
                $queryParams = $request->getQueryParams();

                if ($args["Action"] === "Ordina") {
                    $column = htmlspecialchars($queryParams["column"]);
                    $direction = htmlspecialchars($queryParams["direction"]);

                    if ($column === "Availability") {
                        $products = $this->model->OrderProductsByColor($products, $direction);

                        $arr["order"] = ["column" => $column, "direction" => $direction];
                    } else {
                        $products = $this->repository->getProducts($column, $direction);

                        $arr["order"] = ["column" => $column, "direction" => $direction];
                    }
                }

                if ($args["Action"] === "Filtra") {

                    if (!isset($queryParams["keyword"]) && isset($queryParams["color"])) {
                        $color = htmlspecialchars($queryParams["color"]);

                        $products = $this->model->FilterProductsByColor($products, $color);

                        $arr["filter"] = ["color" => $color];
                    }

                    if (isset($queryParams["keyword"]) && !isset($queryParams["color"])) {
                        $keyword = htmlspecialchars($queryParams["keyword"]);

                        $products = $this->repository->getFilteredProducts($keyword);

                        $arr["filter"] = ["keyword" => $keyword];
                    }

                    if (isset($queryParams["keyword"]) && isset($queryParams["color"])) {
                        $keyword = htmlspecialchars($queryParams["keyword"]);
                        $color = htmlspecialchars($queryParams["color"]);

                        $products = $this->repository->getFilteredProducts($keyword);
                        $products = $this->model->FilterProductsByColor($products, $color);

                        $arr["filter"] = ["keyword" => $keyword, "color" => $color];
                    }

                    if ($args["Action"] === "Rimuovi") {
                        $formValues = $request->getParsedBody();
                        $this->repository->deleteProduct($formValues["ProductID"]);
                    }
                }
            }

            $arr["products"] = $products;

            $response = $this->view->render($response, 'admin-products.phtml', $arr);
            //$response = $this->view->render($response, 'test.phtml', $arr);
            return $response;
        }

        public function GetHint($request, $response, $args)
        {
            $suggestions = $this->repository->getHint($args["Keyword"]);

            return $response->withJson($suggestions);
        }

        public function ManageProducts($request, $response, $args)
        {
            $arr = [];
            $uploadDir = "/opt/lampp/var/www/neroossidiana/assets/images";

            if ($args["Action"] === "Modifica") {
                $productData = $this->repository->getProduct($args["ProductID"]);

                $arr = ["productData" => $productData];
                $imgsNames = [];

                if ($request->isPost()) {
                    $uploadedImages = $request->getUploadedFiles();

                    if (count($uploadedImages) > 0) {

                        foreach ($uploadedImages['Images'] as $uploadedImage) {

                            if ($uploadedImage->getError() === UPLOAD_ERR_OK) {
                                $filename = $this->model->moveUploadedFile($uploadDir, $uploadedImage);

                                array_push($imgsNames, $filename);
                            }
                        }

                        $imgsNames = implode(", ", $imgsNames);

                        if ($productData["Images"]) {
                            $productData["Images"] .= ", " . $imgsNames;
                        } else {
                            $productData["Images"] = $imgsNames;
                        }

                        return $response->withJson($productData);
                    } else {
                        $json = $request->getBody();

                        $product = json_decode($json, true);

                        if (count($product["Images"]) > 0) {
                            $product["Images"] = implode(", ", $product["Images"]);
                        }

                        $result = $this->repository->updateProduct($product);

                        return $response->withJson($result);
                    }
                }
            }

            if ($args["Action"] === "Aggiungi-prodotto") {
                $arr = ["productData" => null];
                $imgsNames = [];

                if ($request->isPost()) {
                    $uploadedImages = $request->getUploadedFiles();

                    if (count($uploadedImages) > 0) {

                        foreach ($uploadedImages['Images'] as $uploadedImage) {

                            if ($uploadedImage->getError() === UPLOAD_ERR_OK) {
                                $filename = $this->model->moveUploadedFile($uploadDir, $uploadedImage);

                                array_push($imgsNames, $filename);
                            }
                        }

                        $imgsNames = implode(", ", $imgsNames);
/* 
                        if ($productData["Images"]) {
                            $productData["Images"] .= ", " . $imgsNames;
                        } else {
                            $productData["Images"] = $imgsNames;
                        } */

                        return $response->withJson($imgsNames);
                    } else {
                        $json = $request->getBody();

                        $product = json_decode($json, true);

                        $result = $this->repository->addNewProduct($product);

                        return $response->withJson($result);
                    }
                }
            }

            $response = $this->view->render($response, 'admin-product-manager.phtml', $arr);
            return $response;
        }

        public function FetchData($request, $response, $args)
        {
            if ($request->isPost()) {
                $body = $request->getBody();
                $json = json_decode($body, true);

                if (isset($json["Gender"]) && !isset($json["Category"])) {
                    $data = $this->repository->getCategories($json["Gender"]);
                }

                if (isset($json["Gender"]) && isset($json["Category"])) {
                    $data = $this->repository->getTypes($json["Gender"], $json["Category"]);
                }

                return $response->withJson($data);
            }
        }
    }
}
