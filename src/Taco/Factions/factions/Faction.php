<?php namespace Taco\Factions\factions;

use pocketmine\Server;
use Taco\Factions\factions\objects\FactionBank;
use Taco\Factions\factions\objects\FactionClaims;
use Taco\Factions\factions\objects\FactionInvite;
use Taco\Factions\factions\objects\FactionMember;

class Faction {

    /** @var string */
    private string $name;

    /** @var string */
    private string $description;

    /** @var string */
    private string $tag;

    /*** @var array<FactionInvite> */
    private array $invites;

    /*** @var array<FactionMember> */
    private array $members;

    /** @var FactionClaims */
    private FactionClaims $claims;

    /** @var array<string> */
    private array $allies;

    /** @var array<string> */
    private array $enemies;

    /** @var int */
    private int $power;

    /** @var FactionBank */
    private FactionBank $bank;

    public function __construct(
        string $name,
        string $description,
        string $tag,
        array $invites,
        array $members,
        FactionClaims $claims,
        array $allies,
        array $enemies,
        int $power,
        FactionBank $bank
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->tag = $tag;
        $this->invites = $invites;
        $this->members = $members;
        $this->claims = $claims;
        $this->allies = $allies;
        $this->enemies = $enemies;
        $this->power = $power;
        $this->bank = $bank;
    }

    /*** @return string */
    public function getName() : string {
        return $this->name;
    }

    /*** @return string */
    public function getDescription() : string {
        return $this->description;
    }

    /*** @return string */
    public function getTag() : string {
        return $this->tag;
    }

    /*** @return array|FactionInvite[] */
    public function getInvites() : array {
        return $this->invites;
    }

    /*** @return array|FactionMember[] */
    public function getMembers() : array {
        return $this->members;
    }

    /*** @return FactionClaims */
    public function getClaimManager() : FactionClaims {
        return $this->claims;
    }

    /*** @return array|string[] */
    public function getAllies() : array {
        return $this->allies;
    }

    /*** @return array|string[] */
    public function getEnemies() : array {
        return $this->enemies;
    }

    /*** @return int */
    public function getPower() : int {
        return $this->power;
    }

    /*** @return FactionBank */
    public function getBank() : FactionBank {
        return $this->bank;
    }

    /**
     * Sends a message to all online faction members
     *
     * @param string $message
     * @return void
     */
    public function sendMessageToOnlineMembers(string $message) : void {
        foreach ($this->getMembers() as $member) {
            if (is_null($player = Server::getInstance()->getPlayerExact($member->getName()))) continue;
            $player->sendMessage($message);
        }
    }

    /**
     * Returns a FactionMember if they exist otherwise
     * it returns null
     *
     * @param string $name
     * @return FactionMember|null
     */
    public function getMemberFromName(string $name) : ?FactionMember {
        foreach ($this->members as $member) {
            if ($member->getName() !== $name) continue;
            return $member;
        }
        return null;
    }

}