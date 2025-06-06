<?php

namespace App\Providers;

use App\Repositories\ScannerRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Store\SizeRepository;
use App\Repositories\Store\OrderRepository;
use App\Interfaces\ScannerRepositoryInterface;
use App\Repositories\Features\BrandRepository;
use App\Repositories\Features\TrendRepository;
use App\Repositories\Features\SearchRepository;
use App\Repositories\Features\ContentRepository;
use App\Repositories\Features\KeywordRepository;
use App\Repositories\Features\ProductRepository;
use App\Interfaces\Store\SizeRepositoryInterface;
use App\Repositories\Features\CategoryRepository;
use App\Interfaces\Store\OrderRepositoryInterface;
use App\Repositories\Store\StoreProductRepository;
use App\Repositories\Store\StoreLocationRepository;
use App\Interfaces\Features\BrandRepositoryInterface;
use App\Interfaces\Features\TrendRepositoryInterface;
use App\Interfaces\Features\SearchRepositoryInterface;
use App\Interfaces\Features\ContentRepositoryInterface;
use App\Interfaces\Features\KeywordRepositoryInterface;
use App\Interfaces\Features\ProductRepositoryInterface;
use App\Interfaces\Features\CategoryRepositoryInterface;
use App\Repositories\Features\ContentContractRepository;
use App\Interfaces\Store\StoreProductRepositoryInterface;
use App\Interfaces\Store\StoreLocationRepositoryInterface;
use App\Interfaces\Features\ContentContractRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BrandRepositoryInterface::class, BrandRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(KeywordRepositoryInterface::class, KeywordRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ContentRepositoryInterface::class, ContentRepository::class);
        $this->app->bind(SearchRepositoryInterface::class, SearchRepository::class);
        $this->app->bind(ContentContractRepositoryInterface::class, ContentContractRepository::class);
        $this->app->bind(TrendRepositoryInterface::class, TrendRepository::class);
        $this->app->bind(ScannerRepositoryInterface::class, ScannerRepository::class);

        //store
        $this->app->bind(SizeRepositoryInterface::class, SizeRepository::class);
        $this->app->bind(StoreProductRepositoryInterface::class, StoreProductRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(StoreLocationRepositoryInterface::class, StoreLocationRepository::class);
    }

/**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
