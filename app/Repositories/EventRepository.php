<?php namespace App\Repositories;


interface EventRepository
{
    /*
     * Returns an array of events based on the filter.
     */
    public function mostRecent($maxCount = 0, array $filter = array());

    /*
     * Returns an array of event_names and frequency counts for the filter timeslice
     */
    public function eventNameSummary(array $filter = array());

}