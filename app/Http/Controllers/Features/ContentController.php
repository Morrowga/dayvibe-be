<?php

namespace App\Http\Controllers\Features;

use Inertia\Inertia;
use App\Models\Brand;
use App\Models\Content;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Features\Content\ContentRequest;
use App\Interfaces\Features\ContentRepositoryInterface;

class ContentController extends Controller
{
    private ContentRepositoryInterface $contentRepository;

    public function __construct(ContentRepositoryInterface $contentRepository)
    {
        $this->contentRepository = $contentRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contents = $this->contentRepository->index();

        return Inertia::render('News/Content/Index',[
            "contents" => $contents['data']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = Brand::get();

        return Inertia::render('News/Content/CreateEdit',[
            "brands" => $brands
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContentRequest $request)
    {
        $newContent = $this->contentRepository->store($request);

        return redirect()->route('contents.index');
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
    public function edit(Content $content)
    {
        $brands = Brand::get();

        return Inertia::render('News/Content/CreateEdit',[
            "content" => $content,
            "brands" => $brands
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Content $content)
    {
        $updateContent = $this->contentRepository->update($request, $content);

        return redirect()->route('contents.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $content)
    {
        $deleteContent = $this->contentRepository->delete($content);

        return redirect()->route('contents.index');
    }
}
