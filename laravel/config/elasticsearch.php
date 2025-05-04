<?php

return [
  'hosts' => [
    env('ELASTICSEARCH_SCHEME', 'http') . '://' .
    env('ELASTICSEARCH_HOST', 'localhost') . ':' .
    env('ELASTICSEARCH_PORT', 9200),
  ],
  'user' => env('ELASTICSEARCH_USER', null),
  'pass' => env('ELASTICSEARCH_PASS', null),
];