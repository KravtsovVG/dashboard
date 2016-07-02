<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\EventRepository;

class EventsQuery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:query {endpoint} {--other=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(EventRepository $eventRepository)
    {
        /*
        $options = $this->option();
        $arguments = $this->argument();
        $this->info("Opts: ".print_r($options,true));
        $this->info("Args: ".print_r($arguments,true));
        */

        $results = null;
        switch ($this->argument('endpoint')) {
            case 'recent':
                $results = $eventRepository->mostRecent(20);
                break;
            case 'histogram':
                $filter = [
                    'startTimestamp' => strtotime("2 days ago"),
                    'endTimestamp' => time(),
                    'interval' => 'hour'
                ];
                $results = $eventRepository->histogram($filter);
                break;
            default:
                $this->error("Unknown action!");
        }

        $fractal = new \League\Fractal\Manager();
        return $this->info(json_encode($fractal->createData($results)->toArray(), JSON_PRETTY_PRINT));

    }
}
