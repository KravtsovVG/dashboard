<?php

use Monolog\Logger;

return array(
    'hosts' => array(
                    'https://search-mainevent-rbmzllx36v7ulzup4xndzq72uu.us-east-1.es.amazonaws.com:443'
                    ),
    'logPath' => storage_path() . '/logs/elasticsearch.log',
    'logLevel' => Logger::INFO
);
