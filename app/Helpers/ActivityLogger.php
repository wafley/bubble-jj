<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\Facades\Activity as ActivityFacade;

class ActivityLogger
{
    /**
     * Record activities
     *
     * @param string $description
     * @param Model|null $causer
     * @param Model|array|null $properties
     * @param string|null $logName
     * @return Activity|null
     */
    public static function log(
        string $description,
        ?Model $causer = null,
        $properties = null,
        ?string $logName = null
    ): ?Activity {
        $activity = ActivityFacade::event($description);

        if ($causer) {
            $activity->causedBy($causer);
        }

        if ($properties) {
            $activity->withProperties($properties);
        }

        if ($logName) {
            $activity->useLog($logName);
        }

        return $activity->log($description);
    }

    /**
     * Record activities for a specific model
     *
     * @param Model $subject
     * @param string $description
     * @param Model|null $causer
     * @param array|null $properties
     * @param string|null $logName
     * @return Activity|null
     */
    public static function logFor(
        Model $subject,
        string $description,
        ?Model $causer = null,
        ?array $properties = null,
        ?string $logName = null
    ): ?Activity {
        $activity = ActivityFacade::performedOn($subject)->event($description);

        if ($causer) {
            $activity->causedBy($causer);
        }

        if ($properties) {
            $activity->withProperties($properties);
        }

        if ($logName) {
            $activity->useLog($logName);
        }

        return $activity->log($description);
    }
}
