<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Tag;
use App\Services\TagService;

class TagController extends BaseController
{
    protected TagService $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'per_page' => $request->get('per_page', 30),
            'page' => $request->get('page', 1),
            'tag_id' => $request->get('tag_id') ?? null,
            'category' => $request->get('category') ?? null,
        ];

        $result = $this->tagService->getAllTags($filters);
        return $this->getJsonResponse($result['success'], $result['message'], $result['data']);
    }

    public function getByName(Request $request)
    {
        $result = $this->tagService->getTagByName($request->name, $request->category);
        return $this->getJsonResponse($result['success'], $result['message'], $result['data']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $result = $this->tagService->createTag(
            $request->name,
            $request->color ?? '#007bff',
            $request->category ?? null,
            $user->id
        );

        return $this->getJsonResponse($result['success'], $result['message'], $result['data']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tag = $this->tagService->getTag($id);
        return $this->getJsonResponse(true, 'OK', $tag);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $result = $this->tagService->updateTag(
            $id,
            $request->name,
            $request->color,
            $request->category ?? null,
            $user
        );

        return $this->getJsonResponse($result['success'], $result['message'], $result['data']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $user = $request->user();
        $result = $this->tagService->deleteTag($id, $user);
        return $this->getJsonResponse($result['success'], $result['message'], null);
    }

    /**
     * Get tags with profile count
     *
     * @return \Illuminate\Http\Response
     */
    public function getTagsWithCount()
    {
        $tags = $this->tagService->getTagsWithProfileCount();
        return $this->getJsonResponse(true, 'OK', $tags);
    }
}
