<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Services\ElasticsearchService;
use App\Services\NotificationService;

class PostController extends Controller
{
    protected ElasticsearchService $elasticsearch;

    public function __construct()
    {
        $this->elasticsearch = new ElasticsearchService('posts');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'caption' => 'required|string',
            'media_url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['created_at'] = Carbon::now()->toIso8601String();
        $data['updated_at'] = Carbon::now()->toIso8601String();

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

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'caption' => 'nullable|string',
            'media_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['updated_at'] = Carbon::now()->toIso8601String();

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