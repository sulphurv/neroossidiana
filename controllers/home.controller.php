<?php 
namespace NeroOssidiana {

    use NeroOssidiana\HomeModel;
    use \Slim\Views\PhpRenderer as ViewRenderer;
    use NeroOssidiana\MyProductsRepository as ProductsRepo;

    class HomeController
    {
        protected $view;
        private $repository;

        public function __construct(ViewRenderer $view, ProductsRepo $repo)
        {
            $this->view = $view;
            $this->repository = $repo;
        }

        public function __invoke($request, $response, $args)
        {
            $homeModel = new HomeModel();

            $promoProducts = $this->repository->getCarouselProducts("WHERE Discount IS NOT NULL", []);
            $suggestedProducts = $this->repository->getCarouselProducts("WHERE Gender = ? LIMIT 12", ["Uomo"]);
            
            foreach($promoProducts as $key => $promoProduct) {
                $promoProducts[$key] = $homeModel->buildProductArrays($promoProduct);
                $endPrice2 = $promoProduct["Discount"] ? $promoProduct["Price"] - ($promoProduct["Price"] * ($promoProduct["Discount"] / 100)) : $promoProduct["Price"];
                $promoProducts[$key]["EndPrice"] = $endPrice2;
            }

            foreach($suggestedProducts as $key => $suggestedProduct) {
                $suggestedProducts[$key] = $homeModel->buildProductArrays($suggestedProduct);
                $endPrice2 = $suggestedProduct["Discount"] ? $suggestedProduct["Price"] - ($suggestedProduct["Price"] * ($suggestedProduct["Discount"] / 100)) : $promoProduct["Price"];
                $suggestedProducts[$key]["EndPrice"] = $endPrice2;
            }
            
            shuffle($promoProducts);
            shuffle($suggestedProducts);

            $response = $this->view->render($response, 'home.phtml', ["promoProducts" => $promoProducts, "suggestedProducts" => $suggestedProducts]);
            return $response;
        }
    }
}