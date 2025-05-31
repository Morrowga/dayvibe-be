<?php

namespace App\Http\Controllers\Features;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\Features\SearchRepositoryInterface;

class SearchController extends Controller
{
    private SearchRepositoryInterface $searchRepository;

    public function __construct(SearchRepositoryInterface $searchRepository)
    {
        $this->searchRepository = $searchRepository;
    }

    public function searchBrands()
    {
        $brands = $this->searchRepository->searchBrands();

        return $brands;
    }


    public function searchContents()
    {
        $contents = $this->searchRepository->searchContents();

        return $contents;
    }

    public function contentDetail($displayUrl)
    {
        $content = $this->searchRepository->contentDetail($displayUrl);

        return $content;
    }

    public function trends()
    {
        $trends = $this->searchRepository->trends();

        return $trends;
    }
}
