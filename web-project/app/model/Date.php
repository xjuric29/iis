<?php


namespace App\Model;


use Tracy\Debugger;

class Date
{
    public static function convertDateIntervalToSeconds($interval) {
        /**Convert DateInterval object to seconds.
         * @param $interval: DateInterval object. */
        return 31556926 * $interval->y
            + 2629743 * $interval->m
            + 86400 * $interval->d
            + 3600 * $interval->h
            + 60 * $interval->i
            + $interval->s;
    }

    public static function converSecondsToDateInterval($seconds) {
        /**Convert seconds to DateInterval object.
         * @param $seconds: Integer. */
        // Really nasty solution but there is not another way. PHP time objects are very dummy.
        $dt = new \DateTime;
        $dt->setTimestamp($seconds);

        $dt_zero = new \DateTime;
        $dt_zero->setTimestamp(0);

        return $dt->diff($dt_zero);
    }

    public static function convertDateIntervalToHoursMinutes($interval) {
        /**Convert DateInterval object to string in format HH:MM.
         * @param $interval: DateInterval object. */
        $seconds = self::convertDateIntervalToSeconds($interval);

        $h = (string)intdiv($seconds, 3600);
        $seconds -= $h * 3600;

        $m = (string)intdiv($seconds, 60);

        // Format to 2 places at least.
        if(strlen($h) == 1) $h = '0' . $h;
        if(strlen($m) == 1) $m = '0' . $m;


        return $h . ':' . $m;
    }

    public static function convertHoursMinutesToDateInterval($time) {
        /**Convert time string to DateInterval object.
         * @param $time: String with time in format format HH:MM. */
        $parsedString = explode(':', $time);

        $h = (int)$parsedString[0];
        $m = (int)$parsedString[1];

        $seconds = $h * 3600 + $m * 60;

        return self::converSecondsToDateInterval($seconds);
    }
}