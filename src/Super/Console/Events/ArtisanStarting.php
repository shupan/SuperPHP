<?php

namespace Super\Console\Events;

class ArtisanStarting
{
    /**
     * The Artisan application instance.
     *
     * @var \Super\Console\Application
     */
    public $artisan;

    /**
     * Create a new event instance.
     *
     * @param  \Super\Console\Application  $artisan
     * @return void
     */
    public function __construct($artisan)
    {
        $this->artisan = $artisan;
    }
}
