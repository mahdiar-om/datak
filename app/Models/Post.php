<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Elastic\Elasticsearch\ClientBuilder;

class Post extends Model
{
    protected $connection = 'elasticsearch';

    protected $fillable = [
        'user_id',
        'caption',
        'media_url',
        'created_at',
        'updated_at',
    ];
    
    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }

}

