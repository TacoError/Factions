<?php namespace Taco\Factions\utils;

class TextUtils {

    /**
     * Used for checking factions names, and faction tags
     *
     * @param string $text
     * @param string $textName
     * @param int $minLength
     * @param int $maxLength
     * @param string $prefix
     * @return string|null
     */
    public static function validateRegularText(string $text, string $textName, int $minLength, int $maxLength, string $prefix = "") : ?string {
        if (preg_match("/[^A-Za-z0-9]/", $text)) {
            return $prefix . "Your " . $textName . " can only contain real english letters.";
        }
        if (strlen($text) < $minLength || strlen($text) > $maxLength) {
            return $prefix . "Your " . $textName . " must be more than " . $minLength . " and less than " . $maxLength . " characters";
        }
        return null;
    }

}