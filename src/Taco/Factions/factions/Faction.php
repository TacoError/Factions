<?php namespace Taco\Factions\factions;

use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\Server;
use Taco\Factions\factions\objects\FactionBank;
use Taco\Factions\factions\objects\FactionClaims;
use Taco\Factions\factions\objects\FactionInvite;
use Taco\Factions\factions\objects\FactionMember;
use Taco\Factions\utils\Format;

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

    /** @var FactionManager */
    private FactionManager $manager;

    /** @var array<int, Item> */
    private array $vaultItems;

    /** @var bool */
    private bool $vaultOpened = false;

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
        FactionBank $bank,
        FactionManager $manager,
        array $vaultItems
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
        $this->manager = $manager;
        $this->vaultItems = $vaultItems;
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

    /**
     * @param bool $state
     * @return void
     */
    public function setVaultState(bool $state) : void {
        $this->vaultOpened = $state;
    }

    /*** @return bool */
    public function getVaultState() : bool {
        return $this->vaultOpened;
    }

    /*** @return array|string[] */
    public function getAllies() : array {
        return $this->allies;
    }

    /*** @return array|string[] */
    public function getEnemies() : array {
        return $this->enemies;
    }

    /**
     * @param array $items
     * @return void
     */
    public function setVaultItems(array $items) : void {
        $this->vaultItems = $items;
    }

    /*** @return array|Item[] */
    public function getVaultItems() : array {
        return $this->vaultItems;
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
     * Invite a player to your faction
     *
     * @param Player $from
     * @param Player $player
     * @return void
     */
    public function invite(Player $from, Player $player) : void {
        $this->invites[] = new FactionInvite($from->getName(), $player->getName(), time());
    }

    /**
     * Joins a player to the faction
     *
     * @param Player $player
     * @return void
     */
    public function acceptInvite(Player $player) : void {
        $this->members[] = new FactionMember(
            $player->getName(),
            $this->manager->getDefaultRole(),
        );
        $this->manager->removeInviteInstances($player);
        $this->sendMessageToOnlineMembers(Format::PREFIX_FACTIONS . "e" . $player->getName() . " has joined the faction.");
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasInvite(string $name) : bool {
        return in_array($name, array_map(fn($invite) => $invite->getWho(), $this->invites));
    }

    /**
     * Removes a name from the invites
     *
     * @param string $name
     * @return void
     */
    public function unsetFromInvites(string $name) : void {
        $this->invites = array_filter($this->invites, fn($invite) => $invite->getWho() !== $name);
    }

    /**
     * @param string $name
     * @return FactionInvite
     */
    public function getInvite(string $name) : FactionInvite {
        return array_filter($this->invites, fn($invite) => $invite->getWho() == $name)[0];
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