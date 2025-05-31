<?php

namespace App\Http\Controllers\Features;

use App\Models\Size;
use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Features\Category\CategoryRequest;
use App\Interfaces\Features\CategoryRepositoryInterface;
use App\Http\Requests\Features\Category\CategoryUpdateRequest;

class CategoryController extends Controller
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = $this->categoryRepository->index();

        return Inertia::render('Category/Index',[
            "categories" => $categories['data']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sizes = Size::get();

        return Inertia::render('Category/CreateEdit', [
            "sizes" => $sizes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $newCategory = $this->categoryRepository->store($request);

        return redirect()->route('categories.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $category->load('sizes');

        $sizes = Size::get();

        return Inertia::render('Category/CreateEdit', [
            "category" => $category,
            "sizes" => $sizes
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $updateCategory = $this->categoryRepository->update($request, $category);

        return redirect()->route('categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $deleteCategory = $this->categoryRepository->delete($category);

        return redirect()->route('categories.index');
    }
}
