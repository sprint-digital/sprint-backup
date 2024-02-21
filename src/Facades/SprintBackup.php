<?php

namespace Sprintdigital\SprintBackup\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Sprintdigital\SprintBackup\SprintBackup
 */
class SprintBackup extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Sprintdigital\SprintBackup\SprintBackup::class;
    }
}
