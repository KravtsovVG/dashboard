<?php

namespace App\Http\Controllers;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\EventRepository;
use Illuminate\Support\Facades\Artisan;

class EventsController extends Controller
{
    protected $eventRepo;

    public function __constructor(EventRepository $eventRepository)
    {
        $this->eventRepo = $eventRepository;
    }

    public function recent()
    {
        // TODO: what project are we on?
        // TODO: apply any filters to the recent query, like most recent by event_name
        $options = array();
        $results = $this->eventRepo->mostRecent(20);
        return response()->json($results);
    }

    public function histogram()
    {
        $options = array();
        // TODO: have defaults, but allow override from Request params
        $options['startTimestamp'] = strtotime("48 hours ago");
        $options['endTimestamp'] = time();
        $options['interval'] = 'hour';
        $results = $this->eventRepo->histogram($options);
        return response()->json($results);
    }

    public function getRecentEvents() {

        try {
            $events = Artisan::call('events:query', [
                        'endpoint' => 'recent'
            ]);
            print_r(Artisan::output());
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }

}
