<?PHP 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;               
use App\Services\ElasticsearchService;

class PostController extends Controller
{
    protected ElasticsearchService $elasticsearch;

    public function __construct()
    {
        $this->elasticsearch = new ElasticsearchService('posts');
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'user_id', 'caption', 'media_url', 'created_at', 'updated_at'
        ]);

        $result = $this->elasticsearch->create($data);
        return response()->json($result, 201);
    }

    public function show(string $id)
    {
        $post = $this->elasticsearch->read($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        return response()->json($post);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->only(['caption', 'media_url', 'updated_at']);
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