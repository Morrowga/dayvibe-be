<?php

namespace App\Repositories\Features;

use App\Models\Brand;
use App\Models\Trend;
use App\Models\Content;
use App\Models\Product;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Features\SearchRepositoryInterface;

class SearchRepository implements SearchRepositoryInterface
{
    use ApiResponses;

    public function searchBrands() {
        try {
            $query = request()->query('q');
            $take = request()->query('take', 10);

            $sanitizedQuery = str_replace(' ', '', $query);

            $brands = Brand::with(['categories','products', 'contents.keywords'])
            ->whereRaw('LOWER(REPLACE(name, " ", "")) LIKE ?', ["%{$sanitizedQuery}%"])
            ->orWhereRaw('LOWER(REPLACE(description, " ", "")) LIKE ?', ["%{$sanitizedQuery}%"])
            ->orWhereRaw('LOWER(REPLACE(slug, " ", "")) LIKE ?', ["%{$sanitizedQuery}%"])
            ->orWhereHas('categories', function($q) use ($sanitizedQuery) {
                $q->whereRaw('LOWER(REPLACE(name_en, " ", "")) LIKE ?', ["%{$sanitizedQuery}%"])
                  ->orWhereRaw('LOWER(REPLACE(name_mm, " ", "")) LIKE ?', ["%{$sanitizedQuery}%"]); // Assuming 'name_mm' is a field in categories
            })
            ->orWhereHas('contents', function($q) use ($sanitizedQuery) {
                $q->whereRaw('MATCH(title) AGAINST(?)', [$sanitizedQuery])
                  ->orWhereRaw('LOWER(REPLACE(content, " ", "")) LIKE ?', ["%{$sanitizedQuery}%"])
                  ->orWhereRaw('LOWER(REPLACE(description, " ", "")) LIKE ?', ["%{$sanitizedQuery}%"])
                  ->orWhereRaw('LOWER(REPLACE(slug, " ", "")) LIKE ?', ["%{$sanitizedQuery}%"])
                  ->orWhereHas('keywords', function($q) use ($sanitizedQuery) {
                      $q->whereRaw('LOWER(REPLACE(name, " ", "")) LIKE ?', ["%{$sanitizedQuery}%"]);
                  });
            })
            ->paginate($take);

            return $this->apiResponse('Fetched Brands', 200, $brands);
        } catch (\Exception $e) {
            return $this->apiResponse($e->getMessage(), 500);
        }
    }

    public function searchContents() {
        try {
            $query = request()->query('q');
            $take = request()->query('take', 10);

            $sanitizedQuery = str_replace(' ', '', $query);

            $contents = Content::with(['keywords', 'brand'])
                ->whereRaw('MATCH(title) AGAINST(?)', [$sanitizedQuery])
                ->orWhereRaw('LOWER(REPLACE(content, " ", "")) LIKE ?', ["%{$sanitizedQuery}%"])
                ->orWhereRaw('LOWER(REPLACE(description, " ", "")) LIKE ?', ["%{$sanitizedQuery}%"])
                ->orWhereRaw('LOWER(REPLACE(slug, " ", "")) LIKE ?', ["%{$sanitizedQuery}%"])
                ->orWhereHas('keywords', function($q) use ($sanitizedQuery) {
                    $q->whereRaw('LOWER(REPLACE(name, " ", "")) LIKE ?', ["%{$sanitizedQuery}%"]);
                })
                ->paginate($take);


            return $this->apiResponse('Fetched Contents', 200, $contents);
        } catch (\Exception $e) {
            return $this->apiResponse($e->getMessage(), 500);
        }
    }

    public function contentDetail($displayUrl)
    {
        try {
            $content = Content::where('display_url', $displayUrl)->with('brand.products')->first();

            if(!$content)
            {
                return $this->apiResponse('Not Found', 404, []);
            }

            return $this->apiResponse('Fetched Content Detail', 200, $content);
        } catch (\Exception $e) {
            return $this->apiResponse($e->getMessage(), 500);
        }

    }

    public function trends()
    {
        try {
            $trends = Trend::with('content.brand')->orderBy('priority', 'ASC')->get();

            return $this->apiResponse('Fetched Trends', 200, $trends);
        } catch (\Exception $e) {
            return $this->apiResponse($e->getMessage(), 500);
        }

    }
}


