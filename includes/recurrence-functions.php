<?php

/**
 * Generate recurrence dates based on the recurrence settings.
 *
 * @param string $start_date The start date of the event.
 * @param string $recurrence_type The type of recurrence (daily, weekly, monthly, yearly).
 * @param int $recurrence_interval The interval for the recurrence.
 * @param string $recurrence_end_date The end date for the recurrence.
 * @return array An array of recurrence dates.
 */

function generate_recurrence_dates($start_date, $recurrence_type, $recurrence_interval, $recurrence_end_date)
{
    $dates = [];
    $current_date = strtotime($start_date);
    $end_date = strtotime($recurrence_end_date);

    while ($current_date <= $end_date) {
        $dates[] = date('Y-m-d', $current_date);
        switch ($recurrence_type) {
            case 'daily':
                $current_date = strtotime("+$recurrence_interval days", $current_date);
                break;
            case 'weekly':
                $current_date = strtotime("+$recurrence_interval weeks", $current_date);
                break;
            case 'monthly':
                $current_date = strtotime("+$recurrence_interval months", $current_date);
                break;
        }
    }

    return $dates;
}
