<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Str;

class ElasticsearchService
{
    protected $client;
    protected string $index;

    public function __construct(string $index)
    {
        $hosts = config('elasticsearch.hosts');
        $this->index = $index;

        $this->client = ClientBuilder::create()
            ->setHosts($hosts)
            ->build();

        $this->index = $index;
    }

    public function create(array $data): array
    {
        $id = $data['id'] ?? Str::uuid()->toString();
        $this->client->index([
            'index' => $this->index,
            'id'    => $id,
            'body'  => $data,
        ]);
        return ['id' => $id];
    }

    public function read(string $id): ?array
    {
        try {
            $resp = $this->client->get([
                'index' => $this->index,
                'id'    => $id,
            ]);
            return $resp['_source'] ?? null;
        } catch (\Elastic\Elasticsearch\Exception\ClientResponseException $e) {
            return $e->getCode() === 404 ? null : throw $e;
        }
    }

    public function update(string $id, array $data): bool
    {
        $this->client->update([
            'index' => $this->index,
            'id'    => $id,
            'body'  => ['doc' => $data],
        ]);
        return true;
    }

    public function delete(string $id): bool
    {
        $this->client->delete([
            'index' => $this->index,
            'id'    => $id,
        ]);
        return true;
    }

    public function search(array $query, int $size = 10, int $from = 0): array
    {
        $resp = $this->client->search([
            'index' => $this->index,
            'body'  => ['query' => $query, 'size' => $size, 'from' => $from],
        ]);
        return $resp['hits']['hits'] ?? [];
    }
}
