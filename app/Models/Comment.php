<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Elastic\Elasticsearch\ClientBuilder;


class Comment extends Model
{
    protected $connection = 'elasticsearch';

    protected $fillable = [
        'post_id',
        'user_id',
        'text',
        'created_at',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }
}
