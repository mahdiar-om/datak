<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ElasticsearchService;
use App\Services\NotificationService;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Traits\TimestampTrait;

class PostController extends Controller
{
    protected ElasticsearchService $elasticsearch;
    use TimestampTrait;

    public function __construct()
    {
        $this->elasticsearch = new ElasticsearchService('posts');
    }

    public function store(StorePostRequest $request)
    {
        $data = $request->validated();
        $this->applyTimestamps($data);

        $result = $this->elasticsearch->create($data);
        app(NotificationService::class)->checkAndNotify('post', $data + ['id' => $result['id']]);

        return response()->json($result, 201);
    }

    public function show(string $id)
    {
        $post = $this->elasticsearch->read($id);
        return $post ? response()->json($post) :
            response()->json(['message' => 'Post not found'], 404);
    }

    public function update(UpdatePostRequest $request, string $id)
    {
        $data = $request->validated();
        $this->applyTimestamps($data);

        $this->elasticsearch->update($id, $data);

        return response()->json(['message' => 'Post updated']);
    }

    public function destroy(string $id)
    {
        $this->elasticsearch->delete($id);
        return response()->json(['message' => 'Post deleted'], 204);
    }

    public function search(Request $request)
    {
        $query = ['match' => ['caption' => $request->input('query', '')]];
        $results = $this->elasticsearch->search($query);
        return response()->json($results);
    }
}