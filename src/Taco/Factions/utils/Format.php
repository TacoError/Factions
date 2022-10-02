<?php namespace Taco\Factions\utils;

class Format {

    public const PREFIX_FACTIONS = "§r§7[§cFactions§7] §";
    public const PREFIX_FACTIONS_GOOD = self::PREFIX_FACTIONS . "a";
    public const PREFIX_FACTIONS_BAD = self::PREFIX_FACTIONS . "c";
    public const PREFIX_KITS = "§r§7[§cKits§7] §";
    public const PREFIX_ECO = "§r§7[§cEco§7] §";

    public static function intToPrefix($input) : string {
        if (!is_numeric($input)) return "0";
        $suffixes = array("", "K", "M", "B", "T", "QD", "QT");
        $suffixIndex = 0;
        while(abs($input) >= 1000 && $suffixIndex < sizeof($suffixes)) {
            $suffixIndex++;
            $input /= 1000;
        }
        return (
            $input > 0
                ? floor($input * 1000) / 1000
                : ceil($input * 1000) / 1000
            )
            . $suffixes[$suffixIndex];
    }

}