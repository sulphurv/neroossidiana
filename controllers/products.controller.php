<?php

namespace NeroOssidiana {

    use NeroOssidiana\MyProductsRepository as ProductsRepo;
    use \Slim\Views\PhpRenderer as ViewRenderer;

    class ProductsController
    {
        protected $view;
        private $repository;

        public function __construct(ViewRenderer $view, ProductsRepo $repository)
        {
            $this->view = $view;
            $this->repository = $repository;
        }

        public function __invoke($request, $response, $args)
        {
            $ch = curl_init();
            $options = array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => "http://neroossidiana.com/Navbar-data",
                CURL_PORT => 8080
            );
            curl_setopt_array($ch, $options);
            $result = curl_exec($ch);
            curl_close($ch);

            /* in alternativa a cURL si può utilizzare il metodo utilizzato sotto */
            // $result = file_get_contents("http://neroossidiana.com/Navbar-data");

            $navData = json_decode($result, true);

            $genderCount = 0;
            $categoryCount = 0;
            $typesCount = 0;



            if (isset($args["Gender"])) {

                $args["Gender"] = filter_var($args["Gender"], FILTER_SANITIZE_STRING);

                foreach ($navData as $gender => $categories)

                    if ($args["Gender"] === $gender)
                        $genderCount = 1;

                if (isset($args["Category"])) {

                    $args["Category"] = filter_var($args["Category"], FILTER_SANITIZE_STRING);

                    foreach ($categories as $category => $typesArr) {

                        if ($args["Category"] === $category)
                            $categoryCount = 1;

                        if (isset($args["Type"])) {

                            $args["Type"] = filter_var($args["Type"], FILTER_SANITIZE_STRING);

                            foreach ($typesArr as $type) {
                                if (preg_match('/-+/', $args["Type"]))
                                    $args["Type"] = implode(" ", explode("-", $args["Type"]));

                                if ($args["Type"] === $type)
                                    $typesCount = 1;
                            }
                        }
                    }
                }
            } else {
                return $response->withRedirect("/page-not-found");
            }

            if (isset($args["Gender"]) && !$genderCount) {
                return $response->withRedirect("/page-not-found");
            } else if (isset($args["Category"]) && !$categoryCount) {
                return $response->withRedirect("/page-not-found");
            } else if (isset($args["Type"]) && !$typesCount) {
                return $response->withRedirect("/page-not-found");
            }

            $itemsPerPage = 16;
            $productsData = $this->repository->getProducts($args, $itemsPerPage);

            $pagingInfo = [
                "itemsPerPage" => $itemsPerPage,
                "currentPage" => $args["Number"],
                "totalItems" => $productsData["productsTotalNum"],
                "currentUrl" => $request->getUri()->getPath()
            ];

            $response = $this->view->render($response, 'products.phtml', [
                "products" => $productsData["products"],
                "pagingInfo" => $pagingInfo,
                "routePar" => $args,
                "navData" => $navData
            ]);
            return $response;
        }

        public function Search($request, $response, $args)
        {
            $value = $request->getParsedBody();

            $term = filter_var($value["Search"], FILTER_SANITIZE_STRING);

            $products = $this->repository->getSearchedProducts($term);

            $response = $this->view->render($response, 'products.phtml', [
                "products" => $products,
                "pagingInfo" => null,
                "routePar" => null,
                "navData" => null
            ]);
            return $response;
        }
    }
}
