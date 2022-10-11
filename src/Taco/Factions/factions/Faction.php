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

    /**
     * @param string $message
     * @return void
     */
    public function sendMessageAllies(string $message) : void {
        $this->sendMessageToOnlineMembers($message);
        foreach ($this->allies as $ally) {
            $faction = $this->manager->getFactionFromName($ally);
            if (is_null($faction)) continue;
            $faction->sendMessageToOnlineMembers($message);
        }
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
     * @param string $who
     * @return bool
     */
    public function isAlliedTo(string $who) : bool {
        return in_array($who, $this->allies);
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

    /**
     * @return string
     */
    public function getOwnersName() : string {
        foreach($this->members as $member) {
            if ($member->getRole()->hasPermission(-1)) {
                return $member->getName();
            }
        }
        return "";
    }


    /*** @return array<FactionMember> */
    public function getCaptains() : array {
        $list = [];
        foreach($this->members as $member) {
            if ($member->getRole()->getName() == "Captain") $list[] = $member;
        }
        return $list;
    }

    /*** @return array */
    public function getCaptainNames() : array {
        $list = [];
        foreach ($this->getCaptains() as $cap) {
            $list[] = $cap->getName();
        }
        return $list;
    }

    /*** @return array */
    public function getMembersRoleMember() : array {
        $list = [];
        foreach($this->members as $member) {
            if ($member->getRole()->getName() == "Member") $list[] = $member;
        }
        return $list;
    }

    /*** @return array */
    public function getMemberNames() : array {
        $list = [];
        foreach ($this->getMembersRoleMember() as $cap) {
            $list[] = $cap->getName();
        }
        return $list;
    }

    /**
     * Returns amount of claims
     *
     * @return int
     */
    public function getClaimCount() : int {
        $amt = 0;
        foreach ($this->getClaimManager()->getClaims() as $claims) {
            $amt += count($claims);
        }
        return $amt;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function sendInfo(Player $player) : void {
        $player->sendMessage("§r§e" . $this->getName() . "'s Info >");
        $player->sendMessage(" §fLeader§7: §e" . $this->getOwnersName());
        $player->sendMessage(" §fCaptains§7: §7[§e" . implode(",", $this->getCaptainNames()) . "§7]");
        $player->sendMessage(" §fMembers:§7: §7[§e" . implode(",", $this->getMemberNames()) . "§7]");
        $player->sendMessage(" §fOverall Members§7: §e" . count($this->members));
        $player->sendMessage(" §fBalance§7: §e$" . Format::intToPrefix($this->bank->getBalance()));
        $player->sendMessage(" §fAllies§7: §7[§e" . implode(",", $this->allies) . "§7]");
        $player->sendMessage(" §fClaimed Chunks§7: §e" . $this->getClaimCount());
        $player->sendMessage(" §fPower: §e" . $this->getPower());
    }

}