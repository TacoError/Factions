<?php namespace Taco\Factions\factions\objects;

use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\World;
use Taco\Factions\Manager;
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
     * @param Vector3 $pos
     * @param World $world
     * @return void
     */
    public function removeClaimAt(Vector3 $pos, World $world) : void {
        [$x, $z] = ChunkUtils::getRealXZ($pos);
        unset($this->claimed[$world->getDisplayName()][World::chunkHash($x, $z)]);
    }

    /**
     * Returns if the faction has claim at
     *
     * @param Vector3 $pos
     * @param World $world
     * @return bool
     */
    public function hasClaimAt(Vector3 $pos, World $world) : bool {
        [$x, $z] = ChunkUtils::getRealXZ($pos);
        if (isset($this->claimed[$world->getDisplayName()][World::chunkHash($x, $z)])) {
            return true;
        }
        return false;
    }

    /**
     * Returns whether another faction, or admin has already claimed
     * that spot.
     *
     * @param Vector3 $pos
     * @param World $world
     * @param string $fName
     * @return bool
     */
    public function canClaimAt(Vector3 $pos, World $world, string $fName) : bool {
        foreach (Manager::getFactionManager()->getFactions() as $faction) {
            if ($faction->getClaimManager()->hasClaimAt($pos, $world) && $faction->getName() !== $fName) {
                return false;
            }
        }
        return false;
    }

    /*** @return array */
    public function getClaims() : array {
        return $this->claimed;
    }

}