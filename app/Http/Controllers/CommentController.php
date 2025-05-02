<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ElasticsearchService;

class CommentController extends Controller
{
    protected ElasticsearchService $elasticsearch;

    public function __construct()
    {
        $this->elasticsearch = new ElasticsearchService('comments');
    }

    public function store(Request $request)
    {
        $data = $request->only(['post_id', 'user_id', 'text', 'created_at']);
        $result = $this->elasticsearch->create($data);

        return response()->json($result);
    }

    public function show(string $id)
    {
        $comment = $this->elasticsearch->read($id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        return response()->json($comment);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->only(['text']);
        $this->elasticsearch->update($id, $data);

        return response()->json(['message' => 'Comment updated']);
    }

    public function destroy(string $id)
    {
        $this->elasticsearch->delete($id);

        return response()->json(['message' => 'Comment deleted']);
    }

    public function search(Request $request)
    {
        $query = [
            'match' => [
                'text' => $request->input('query'),
            ],
        ];

        $results = $this->elasticsearch->search($query);

        return response()->json($results);
    }
}
