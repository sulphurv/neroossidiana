<?php
    namespace NeroOssidiana {
        
        class Pagination {

            public $totalItems;
            public $itemsPerPage;
            public $currentPage;
            public $totalPages;
            public $currentUrL;

            public function __construct($pageInfo) {
                
                $this->totalItems = $pageInfo["totalItems"];
                $this->itemsPerPage = $pageInfo["itemsPerPage"];
                $this->currentPage = $pageInfo["currentPage"];
                $this->currentUrl = $pageInfo["currentUrl"];

                $this->totalPages = isset($this->totalItems, $this->itemsPerPage) ? ceil((int)$this->totalItems / (int)$this->itemsPerPage) : 0;
            }

            public function create() {

                $maxElemNum = 2;
                $offset = $this->currentPage + $maxElemNum;
                $limit = $this->totalPages - $maxElemNum;
                $prevPage = $this->currentPage > 1 ? $this->currentPage - 1 : 1;
                $nextPage = $this->currentPage < $this->totalPages ? $this->currentPage + 1 : $this->currentPage;
                $prevDisabled = $this->currentPage == 1 ? "disabled" : "";
                $nextDisabled = $this->currentPage == $this->totalPages ? "disabled" : "";
                $pattern = '/Page[0-9]+$/';

                $htmlStart = '' .
                '<nav id="pagination-nav">' .
                    '<ul class="pagination justify-content-center">' .
                        '<li class="page-item ' . $prevDisabled . '">' .
                            '<a class="page-link" href="' . preg_replace($pattern, "Page$prevPage", $this->currentUrl) . '" aria-label="Previous">' .
                                '<span aria-hidden="true">&laquo;</span>' .
                            '</a>' .
                        '</li>';
                $htmlMid = '';
                $htmlEnd = '' .
                        '<li class="page-item ' . $nextDisabled . '">' .
                            '<a class="page-link" href="' . preg_replace($pattern, "Page$nextPage", $this->currentUrl) . '" aria-label="Next">' .
                                '<span aria-hidden="true">&raquo;</span>' .
                            '</a>' .
                        '</li>' .
                    '</ul>' .
                '</nav>';
                
                $startPos = ($offset < $this->totalPages) ? $this->currentPage : $limit;
                $endPos = ($offset < $this->totalPages) ? $offset : $this->totalPages;

                for ($i = ($startPos <= 0) ? 1 : $startPos; $i <= $endPos; $i++) {

                    $active = $this->currentPage == $i ? "active" : "";

                    $htmlMid .= '' .
                    '<li class="page-item ' . $active . '">' .
                        '<a class="page-link" href="' . preg_replace($pattern, "Page$i", $this->currentUrl) . '">' . $i . '</a>'.
                    '</li>';
                }

                echo $htmlStart . $htmlMid . $htmlEnd;
            }
        }
    }