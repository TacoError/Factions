<?php namespace Taco\Factions\factions;

use JsonException;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use Taco\Factions\factions\commands\FactionCommand;
use Taco\Factions\factions\objects\FactionBank;
use Taco\Factions\factions\objects\FactionClaims;
use Taco\Factions\factions\objects\FactionInvite;
use Taco\Factions\factions\objects\FactionMember;
use Taco\Factions\factions\objects\FactionPermissionTypes;
use Taco\Factions\factions\objects\FactionRole;
use Taco\Factions\Main;
use Taco\Factions\utils\Format;

class FactionManager {

    /** @var array<string, Faction> */
    private array $factions = [];

    /** @var Config */
    private Config $store;

    /** @var array<FactionRole> */
    private array $factionRoles;

    public function __construct() {
        $this->store = new Config(Main::getInstance()->getDataFolder() . "factions.yml", Config::YAML);

        $this->factionRoles = [
            "Member" => new FactionRole("Member",
                [
                    FactionPermissionTypes::PERMISSION_BUILD,
                    FactionPermissionTypes::PERMISSION_PLACE
                ]
            ),
            "Captain" => new FactionRole("Captain",
                [
                    FactionPermissionTypes::PERMISSION_REMOVE_CLAIM,
                    FactionPermissionTypes::PERMISSION_ADD_CLAIM,
                    FactionPermissionTypes::PERMISSION_BANK_TAKE,
                    FactionPermissionTypes::PERMISSION_BANK_ADD,
                    FactionPermissionTypes::PERMISSION_BUILD,
                    FactionPermissionTypes::PERMISSION_PLACE,
                    FactionPermissionTypes::PERMISSION_INVITE
                ]
            ),
            "Owner" => new FactionRole("Owner",
                [
                    FactionPermissionTypes::PERMISSION_ALL
                ]
            )
        ];

        foreach ($this->store->getAll() as $name => $data) {
            $invites = [];
            foreach ($data["invites"] as $invData) {
                $invites[] = new FactionInvite(
                    $invData["from"],
                    $invData["to"],
                    $invData["sent"]
                );
            }

            $members = [];
            foreach ($data["members"] as $memberName => $memberData) {
                $members[] = new FactionMember(
                    $memberName,
                    $this->factionRoles[$memberData["role"]] ?? $this->factionRoles["Member"],
                );
            }

            $items = [];
            foreach ($data["vault"] as $pos => $item) {
                $items[$pos] = Item::jsonDeserialize($item);
            }

            $this->factions[$name] = new Faction(
                $name,
                $data["description"],
                $data["tag"],
                $invites,
                $members,
                new FactionClaims($data["claims"]),
                $data["allies"],
                $data["enemies"],
                $data["power"],
                new FactionBank($data["balance"]),
                $this,
                $items
            );
        }
    }

    /*** @return void */
    public function prepare() : void {
        Server::getInstance()->getCommandMap()->registerAll("Factions", [
            new FactionCommand()
        ]);
    }

    /*** @throws JsonException */
    public function save() : void {
        $store = $this->store;
        foreach (array_keys($store->getAll()) as $key) {
            $store->remove($key);
        }
        foreach ($this->factions as $faction) {
            $members = [];
            foreach ($faction->getMembers() as $member) {
                $members[$member->getName()] = [
                    "role"  => $member->getRole()->getName()
                ];
            }

            $items = [];
            foreach ($faction->getVaultItems() as $pos => $item) {
                $items[$pos] = $item->jsonSerialize();
            }

            $store->set($faction->getName(), [
                "description" => $faction->getDescription(),
                "tag" => $faction->getTag(),
                "invites" => $faction->getInvites(),
                "members" => $members,
                "claims" => $faction->getClaimManager()->getClaims(),
                "allies" => $faction->getAllies(),
                "enemies" => $faction->getEnemies(),
                "power" => $faction->getPower(),
                "balance" => $faction->getBank()->getBalance(),
                "vault" => $items
            ]);
        }
        $store->save();
    }

    /**
     * Disbands a faction
     *
     * @param Player $owner
     * @return void
     */
    public function disbandFaction(Player $owner) : void {
        $faction = $this->getPlayerFaction($owner);
        $faction->sendMessageToOnlineMembers(Format::PREFIX_FACTIONS . "eYour faction leader has disbanded the faction.");
        unset($this->factions[$faction->getName()]);
    }

    /**
     * Creates a faction
     *
     * @param string $name
     * @param string $tag
     * @param string $ownerName
     * @return void
     */
    public function createFaction(string $name, string $tag, string $ownerName) : void {
        $this->factions[$name] = new Faction(
            $name,
            "A Generic Faction Description",
            $tag,
            [],
            [new FactionMember($ownerName, $this->factionRoles["Owner"])],
            new FactionClaims([]),
            [],
            [],
            50,
            new FactionBank(0),
            $this,
            []
        );
    }

    /**
     * Returns a faction by its name, or null if it
     * doesn't exist
     *
     * @param string $name
     * @return Faction|null
     */
    public function getFactionFromName(string $name) : ?Faction {
        if (isset($this->factions[$name])) return $this->factions[$name];
        return null;
    }

    /*** @return FactionRole */
    public function getCaptainRole() : FactionRole {
        return $this->factionRoles["Captain"];
    }

    /**
     * Returns a players faction, otherwise null
     *
     * @param Player $player
     * @return Faction|null
     */
    public function getPlayerFaction(Player $player) : ?Faction {
        foreach ($this->factions as $faction) {
            $names = array_map(fn($member) => $member->getName(), $faction->getMembers());
            if (!in_array($player->getName(), $names)) continue;
            return $faction;
        }
        return null;
    }

    /*** @return FactionRole */
    public function getDefaultRole() : FactionRole {
        return $this->factionRoles["Member"];
    }

    /**
     * If there is a faction with the said tag, it will return
     * the faction, otherwise null
     *
     * @param string $tag
     * @return Faction|null
     */
    public function getFactionFromTag(string $tag) : ?Faction {
        $tag = strtoupper($tag);
        foreach ($this->factions as $faction) {
            if ($faction->getTag() !== $tag) continue;
            return $faction;
        }
        return null;
    }

    /**
     * Returns whether the player has a invite to a faction
     *
     * @param Player $player
     * @param string $faction
     * @return bool
     */
    public function hasInviteFromFaction(Player $player, string $faction) : bool {
        return $this->factions[$faction]->hasInvite($player->getName());
    }

    /**
     * Removes all current faction invites from a player
     *
     * @param Player $player
     * @return void
     */
    public function removeInviteInstances(Player $player) : void {
        foreach ($this->factions as $faction) {
            $faction->unsetFromInvites($player->getName());
        }
    }

    /*** @return array<string, Faction> */
    public function getFactions() : array {
        return $this->factions;
    }

    /*** @return FactionRole */
    public function getLeaderRole() : FactionRole {
        return $this->factionRoles["Owner"];
    }

    /**
     * @param Position $pos
     * @return string|null
     */
    public function getClaimAt(Position $pos) : ?string {
        foreach($this->factions as $name => $faction) {
            if ($faction->getClaimManager()->hasClaimAt($pos->asVector3(), $pos->getWorld())) return $name;
        }
        return null;
    }

    /**
     * @param int $hash
     * @param string $world
     * @return Faction|null
     */
    public function getFactionFromChunkHash(int $hash, string $world) : ?Faction {
        foreach ($this->factions as $faction) {
            if ($faction->getClaimManager()->hasClaimAtChunkHash($hash, $world)) return $faction;
        }
        return null;
    }

}