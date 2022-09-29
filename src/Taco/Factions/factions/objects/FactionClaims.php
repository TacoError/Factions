<?php namespace Taco\Factions\factions\objects;

use pocketmine\math\Vector3;
use pocketmine\world\World;
use Taco\Factions\utils\ChunkUtils;

class FactionClaims {

    /** @var array<string, array<int>> */
    private array $claimed;

    public function __construct(array $claimed) {
        $this->claimed = $claimed;
    }

    /**
     * @param Vector3 $pos
     * @param World $world
     * @return void
     */
    public function addClaim(Vector3 $pos, World $world) : void {
        [$x, $z] = ChunkUtils::getRealXZ($pos);
        $this->claimed[$world->getDisplayName()][] = World::chunkHash($x, $z);
    }

    /**
     * Returns whether another faction, or admin has already claimed
     * that spot.
     *
     * @param Vector3 $pos
     * @param World $world
     * @return bool
     */
    public function canClaimAt(Vector3 $pos, World $world) : bool {
        //TODO
        return true;
    }

    /*** @return array */
    public function getClaims() : array {
        return $this->claimed;
    }

}