<?php namespace App\Repositories;

use Es;
Use Log;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;


class EventRepository_ElasticSearch implements EventRepository
{
    protected $indexName = "";

    public function __construct()
    {
        // TODO: move these into config based on the Project

        // MAY NEED THIS!
        // https://packagist.org/packages/wizacha/aws-signature-middleware

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
            Log::error("Bad result from ElasticSearch");
            return array();
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
        Log::info(json_encode($fractal->createData($collection)->toArray(), JSON_PRETTY_PRINT));
        return $collection;
    }


    public function eventNameSummary(array $filter = array())
    {
        // time range: last 48 hours
        // http://php.net/manual/en/datetime.formats.relative.php
        // TODO: check filter validity, default to last 48h
        $esStartTime = $filter['startTimestamp'] * 1000;
        $esEndTime   = $filter['endTimestamp']   * 1000;
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
                                                "gte" => $esStartTime,
                                                "lte" => $esEndTime
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
            Log::error("Bad result from ElasticSearch");
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
        Log::info(json_encode($fractal->createData($collection)->toArray(), JSON_PRETTY_PRINT));
        return $collection;
    }

    public function histogram(array $filter = array())
    {
        // TODO: check filter validity, default to last 48h
        // TODO: set default interval
        // Available expressions for interval: year, quarter, month, week, day, hour, minute, second
        // Fractional values are allowed for seconds, minutes, hours, days and weeks. For example 1.5 hours: "1.5h"
        // https://www.elastic.co/guide/en/elasticsearch/reference/current/common-options.html#time-units
        $esStartTime = $filter['startTimestamp'] * 1000;
        $esEndTime   = $filter['endTimestamp']   * 1000;
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
                                                "gte" => $esStartTime,
                                                "lte" => $esEndTime
                                            ]
                                        ] // range
                                    ]
                                ]
                            ] // bool
                        ] // filter
                    ] // filtered
                ], // query
                "aggs" => [
                    "event_name_over_time" => [
                        "terms" => [
                            "field" => "event_name"
                        ],
                        "aggs" => [
                            "timeslice" => [
                                "date_histogram" => [
                                    "field" => "@timestamp",
                                    "interval" => $filter['interval'],
                                    "min_doc_count" => 0,
                                    "extended_bounds" => [
                                        "min" => $esStartTime,
                                        "max" => $esEndTime
                                    ]
                                ]
                            ]
                        ]
                    ]
                ] // aggs
            ] // body
        ];

        $result = Es::search($searchParams);

        if (empty($result)) {
            Log::error("Bad result from ElasticSearch");
            return [];
        }

        $buckets = $result['aggregations']['event_name_over_time']['buckets'];
        $fractal = new Manager();

        //THIS TRANSFORMER needs to return a nice nested array set of events, and time slices.

        $timesliceAxis = []; // collect unique slice timestamps
        // TODO: use nested collections?
        /*
        $collection = new Collection($buckets, function(array $bucket) use ($fractal,$timesliceAxis) {
            $sliceCollection = new Collection($bucket->timselice->buckets, function(array $sliceBucket) use ($timesliceAxis) {
                $epochTime = floor($sliceBucket['key']/1000);
                $timesliceAxis[$epochTime] = true;
                return [
                    $epochTime => $sliceBucket['doc_count']
                ];
            });
            return [
                'event_name' => $bucket['key'],
                'count'      => $bucket['doc_count'],
                'values'     => $fractal->createData($sliceCollection)->toArray()
            ];
        });
        */

        foreach ($buckets as $bucket) {
            $sliceCollection = new Collection($bucket->timselice->buckets, function(array $sliceBucket) use ($timesliceAxis) {
                $epochTime = intval($sliceBucket['key']/1000);
                $timesliceAxis[$epochTime] = true;
                return [
                    $epochTime => $sliceBucket['doc_count']
                ];
            });
            return [
                'event_name' => $bucket['key'],
                'count'      => $bucket['doc_count'],
                'values'     => $fractal->createData($sliceCollection)->toArray()
            ];
        }
        Log::info(json_encode($fractal->createData($collection)->toArray(), JSON_PRETTY_PRINT));
        return $collection;
    }




} 