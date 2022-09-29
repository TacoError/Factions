<?php namespace Taco\Factions\utils;

use pocketmine\math\Vector3;
use pocketmine\world\format\Chunk;

class ChunkUtils {

    /**
     * Returns a array as array<x, z> containing the realXZ
     *
     * @param Vector3 $pos
     * @return array<int>
     */
    public static function getRealXZ(Vector3 $pos) : array {
        return [
            $pos->getFloorX() >> Chunk::COORD_BIT_SIZE,
            $pos->getFloorZ() >> Chunk::COORD_BIT_SIZE
        ];
    }

}