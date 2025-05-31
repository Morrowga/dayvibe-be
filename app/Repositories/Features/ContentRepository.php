<?php

namespace App\Repositories\Features;

use App\Models\Content;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Features\ContentRepositoryInterface;

class ContentRepository implements ContentRepositoryInterface
{
    use CRUDResponses;

    public function index() {
        try {
            $contents = Content::with(['media'])->get();
            return $this->success('Fetched Contents', $contents);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $newContent = Content::create($request->all());

            if ($request->hasFile('image')) {
                $newContent->addMedia($request->file('image'))->toMediaCollection('news_image');
            }

            DB::commit();
            return $this->success('Content is created', $newContent);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

    public function update(Request $request, Content $content)
    {
        DB::beginTransaction();
        try {
            $updateContent = $content->update($request->all());

            if ($request->has('delete_image')) {
                $media = $content->getFirstMedia('news_image');
                if ($media) {
                    $media->delete();
                }
            }

            if ($request->hasFile('image')) {
                $content->addMedia($request->file('image'))->toMediaCollection('news_image');
            }

            DB::commit();
            return $this->success('Content is updated', $content);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(),500);
        }
    }

    public function delete(Content $content)
    {
        try {
            $content->delete();
            return $this->success('Content is deleted', []);
        } catch (\Throwable $th) {
            return $this->error($e->getMessage(),500);
        }
    }

}
