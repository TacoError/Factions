<?php namespace Taco\Factions\utils;

use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\Position;

class VectorUtils {

    /**
     * @param Vector3 $vector
     * @return string
     */
    public static function vecToString(Vector3 $vector) : string {
        return $vector->getFloorX() . ":" . $vector->getFloorY() . ":" . $vector->getFloorZ();
    }

    /**
     * @param string $vector
     * @return Vector3
     */
    public static function stringToVec(string $vector) : Vector3 {
        $vector = explode(":", $vector);
        return new Vector3((int)$vector[0], (int)$vector[1], (int)$vector[2]);
    }

    /**
     * @param Position $position
     * @return string
     */
    public static function positionToString(Position $position) : string {
        return $position->getFloorX() . ":"
            . $position->getFloorY() . ":"
            . $position->getFloorZ() . ":"
            . $position->getWorld()->getDisplayName();
    }

    /**
     * @param string $position
     * @return Position
     */
    public static function stringToPosition(string $position) : Position {
        $position = explode(":", $position);
        return new Position(
            (int)$position[0],
            (int)$position[1],
            (int)$position[2],
            Server::getInstance()->getWorldManager()->getWorldByName($position[3])
        );
    }

}