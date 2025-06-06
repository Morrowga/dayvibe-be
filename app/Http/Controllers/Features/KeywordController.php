<?php

namespace App\Http\Controllers\Features;

use App\Models\Keyword;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Features\Keyword\KeywordRequest;
use App\Interfaces\Features\KeywordRepositoryInterface;

class KeywordController extends Controller
{
    private KeywordRepositoryInterface $keywordRepository;

    public function __construct(KeywordRepositoryInterface $keywordRepository)
    {
        $this->keywordRepository = $keywordRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $keywords = $this->keywordRepository->index();

        return $keywords;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KeywordRequest $request)
    {
        $newKeyword = $this->keywordRepository->store($request);

        return $newKeyword;
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KeywordRequest $request, Keyword $keyword)
    {
        $updateKeyword = $this->keywordRepository->update($request, $keyword);

        return $updateKeyword;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Keyword $keyword)
    {
        $deleteKeyword = $this->keywordRepository->delete($keyword);

        return $deleteKeyword;
    }
}
