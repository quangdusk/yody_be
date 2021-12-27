<?php

namespace App\Console\RabbitMQ;

class SetVariable
{
    protected $exchange = "exchange-yody";
    protected $exchangeRetries = "exchange-yody-retries";
    protected  $number_of_times = [
        1 => 5000,
        2 => 30000,
        3 => 60000
    ];
}
