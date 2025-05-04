<?php

use Illuminate\Support\Facades\Route;
use Elastic\Elasticsearch\ClientBuilder;
use App\Http\Controllers\PostController;


Route::get('/', function () {
    return view('welcome');
});



Route::get('/es-test', function () {
    $host = env('ELASTICSEARCH_SCHEME', 'http') . '://' . env('ELASTICSEARCH_HOST', 'localhost') . ':' . env('ELASTICSEARCH_PORT', 9200);

    $client = ClientBuilder::create()
        ->setHosts([$host])
        ->build();

    try {
        $response = $client->info();
        return response()->json($response->asArray());
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

