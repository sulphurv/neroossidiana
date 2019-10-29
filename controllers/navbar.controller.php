<?php
namespace NeroOssidiana {
    use \Slim\Views\PhpRenderer as ViewRenderer;
    use NeroOssidiana\NavbarRepository as NavbarRepo;

    class NavbarController
    {
        protected $view;
        private $repository;

        public function __construct(ViewRenderer $view, NavbarRepo $repo)
        {
            $this->view = $view;
            $this->repository = $repo;
        }

        public function __invoke($request, $response, $args)
        {
            $navbarData = $this->repository->getNavbarData();

            return $response->withJson($navbarData);
        }

        public function GetHint($request, $response, $args)
        {
            $suggestions = $this->repository->getHint($args["Keyword"]);

            return $response->withJson($suggestions);
        }
    }
}
