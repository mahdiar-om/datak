<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Services\ElasticsearchService;
use App\Services\NotificationService;

class CommentController extends Controller
{
    protected ElasticsearchService $elasticsearch;

    public function __construct()
    {
        $this->elasticsearch = new ElasticsearchService('comments');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|string',
            'user_id' => 'required|integer',
            'text' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['created_at'] = Carbon::now()->toIso8601String();
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

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

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
