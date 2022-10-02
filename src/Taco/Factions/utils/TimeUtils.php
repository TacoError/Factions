<?php namespace Taco\Factions\utils;

class TimeUtils {

    /**
     * Converts a int time to hh:mm:ss
     *
     * @param int $time
     * @return string
     */
    public static function intToHhMmSs(int $time) : string {
        return sprintf(
            "%02d:%02d:%02d",
            ($time/ 3600),
            ($time / 60 % 60),
            $time % 60
        );
    }

    /**
     * Returns a int in seconds as mm:dd:hh:mm
     *
     * @param int $ss
     * @return string
     */
    public static function intToDDdHhMm(int $ss) : string {
        $m = floor(($ss % 3600) / 60);
        $h = floor(($ss % 86400) / 3600);
        $d = floor(($ss % 2592000) / 86400);
        return sprintf("%02d:%02d:%02d", $d, $h, $m);
    }

}