<?php namespace App\Repositories;

use Es;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;


class EventRepository_ElasticSearch implements EventRepository
{
    protected $indexName = "";

    public function __construct()
    {
        // TODO: move these into config based on the Project
        $this->indexName = "events";
    }

    public function mostRecent($maxCount = 0, array $filter = array())
    {
        // Construct ES query for most recent events
        // Examples of constructing queries using the ES PHP API:
        // https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/_search_operations.html
        $searchParams = [
            "size" => $maxCount,               // how many results *per shard* you want back
            "index" => $this->indexName,
            "body" => [
                "query" => [
                    "match_all" => []
                ],
                "fields" => [
                    "*",
                    "_source"
                ],
                "sort" => [
                    "@timestamp" => "desc"
                ] // sort
            ] // body
        ];

        $result = Es::search($searchParams);

        if (empty($result)) {
            $this->error("Bad result from ElasticSearch");
            return;
        }

        $items = $result['hits']['hits'];
        $fractal = new Manager();
        $collection = new Collection($items, function(array $item) {
            $event = $item['_source'];
            return [
                'id' => $item['_id'],
                'project_id' => $event['project_id'],
                'event_name' => $event['event_name'],
                'ts_received' => $event['meta']['ts_received'],
                'sender_ip' => $event['meta']['sender_ip']
            ];
        });
        $this->info(json_encode($fractal->createData($collection)->toArray(), JSON_PRETTY_PRINT));
        return $collection;
    }


    public function eventNameSummary(array $filter = array())
    {
        // time range: last 48 hours
        // http://php.net/manual/en/datetime.formats.relative.php
        $startTimestamp = strtotime("48 hours ago");
        $endTimestamp = time();
        $startTimestamp = 1461648577414;
        $endTimestamp = 1462253377414;
        $searchParams = [
            "size" => 0, // unlimited results per shard
            "index" => $this->indexName,
            "body" => [
                "query" => [
                    "filtered" => [
                        "query" => [
                            "match_all" => []
                        ],
                        "filter" => [
                            "bool" => [
                                "must" => [
                                    [
                                        "range" => [
                                            "@timestamp" => [
                                                "gte" => $startTimestamp,
                                                "lte" => $endTimestamp
                                            ]
                                        ] // range
                                    ]
                                ]
                            ] // bool
                        ] // filter
                    ] // filtered
                ], // query
                "aggs" => [
                    "unique_event" => [
                        "terms" => [
                            "field" => "event_name"
                        ]
                    ]
                ] // aggs
            ] // body
        ];

        $result = Es::search($searchParams);

        if (empty($result)) {
            $this->error("Bad result from ElasticSearch");
            return [];
        }

        $buckets = $result['aggregations']['unique_event']['buckets'];
        $fractal = new Manager();
        $collection = new Collection($buckets, function(array $bucket) {
            return [
                'event_name' => $bucket['key'],
                'count'      => $bucket['doc_count']
            ];
        });
        $this->info(json_encode($fractal->createData($collection)->toArray(), JSON_PRETTY_PRINT));
        return $collection;
    }




} 