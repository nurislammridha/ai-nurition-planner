<?php

namespace App\Traits;

trait SystemTrait
{
    public function increaseTimeoutAndRequest($timeLimit = 300, $memoryLimit = '512M')
    {
        set_time_limit($timeLimit);
        ini_set('memory_limit', $memoryLimit);
    }
}
