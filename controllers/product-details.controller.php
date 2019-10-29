<?php

namespace NeroOssidiana {

    use NeroOssidiana\ProductDetailsModel as PDModel;
    use NeroOssidiana\MyProductsRepository as ProductsRepo;
    use \Slim\Views\PhpRenderer as ViewRenderer;

    class ProductDetailsController
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

            $productData = $this->repository->getProductData($args["ProductID"]);
            $product = $productData["product"];

            /* IMPORTANTE: la pagina è temporanea sarà necessario ritornarci su */
            if (!$product) {
                header("Location: /page-not-found");
                die();
            }

            $unique = [];

            foreach ($productData["productDetails"] as $prodDetails) {
                if ($prodDetails["Availability"] === "black") {
                    array_push($unique, $prodDetails["Availability"]);
                }
            }

            /* Se il prodotto è esaurito redirigi alla homepage */
            if (count($unique) === count($productData["productDetails"])) {
                header("Location: /");
                die();
            }

            $product = PDModel::buildProductArrays($product);
            $endPrice = $product["Discount"] ? $product["Price"] - ($product["Price"] * ($product["Discount"] / 100)) : $product["Price"];
            $product["EndPrice"] = $endPrice;

            $suggestedProducts = $this->repository->getCarouselProducts("WHERE Gender = ? AND NOT ProductID = ?", [$product["Gender"], $product["ProductID"]]);

            foreach ($suggestedProducts as $key => $suggestedProduct) {
                $suggestedProducts[$key] = PDModel::buildProductArrays($suggestedProduct);
                $endPrice2 = $suggestedProduct["Discount"] ? $suggestedProduct["Price"] - ($suggestedProduct["Price"] * ($suggestedProduct["Discount"] / 100)) : $suggestedProduct["Price"];
                $suggestedProducts[$key]["EndPrice"] = $endPrice2;
            }

            shuffle($suggestedProducts);
            $arr = [
                "product" => $product,
                "prodDetails" => $productData["productDetails"],
                "suggestedProds" => $suggestedProducts
            ];

            if ($request->isGet()) {
                $arr["ordered"] = false;
            }

            if ($request->isPost()) {
                $formValues = $request->getParsedBody();
                $prodDetailsId = $formValues["ProductDetailsID"];

                $selectedProdArr = array_filter($productData["productDetails"], function ($row) use ($prodDetailsId) {
                    return $row["ProductDetailsID"] === $prodDetailsId;
                });

                $selectedProd = end($selectedProdArr);

                Cart::addItem($product, $selectedProd);

                $arr["ordered"] = true;
            }

            $response = $this->view->render($response, 'product-details.phtml', $arr);

            return $response;
        }
    }
}
