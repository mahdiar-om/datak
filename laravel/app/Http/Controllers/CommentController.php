<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ElasticsearchService;
use App\Services\NotificationService;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\StorePostRequest;
use App\Traits\TimestampTrait;

class CommentController extends Controller
{
    protected ElasticsearchService $elasticsearch;
    use TimestampTrait;


    public function __construct()
    {
        $this->elasticsearch = new ElasticsearchService('comments');
    }

    public function store(StoreCommentRequest $request)
    {
        $data = $request->validated();
        $this->applyTimestamps($data);


        $result = $this->elasticsearch->create($data);
        app(NotificationService::class)->checkAndNotify('comment', $data + ['id' => $result['id']]);
        return response()->json($result, 201);
    }

    public function show(string $id)
    {
        $comment = $this->elasticsearch->read($id);
        return $comment ? response()->json($comment) :
            response()->json(['message' => 'Comment not found'], 404);
    }

    public function update(StorePostRequest $request, string $id)
    {
        $data = $request->validated();
        $this->applyTimestamps($data);

        $this->elasticsearch->update($id, $data);
        return response()->json(['message' => 'Comment updated']);
    }

    public function destroy(string $id)
    {
        $this->elasticsearch->delete($id);
        return response()->json(['message' => 'Comment deleted'], 204);
    }

    public function search(Request $request)
    {
        $query = ['match' => ['text' => $request->input('query', '')]];
        $results = $this->elasticsearch->search($query);
        return response()->json($results);
    }
}
