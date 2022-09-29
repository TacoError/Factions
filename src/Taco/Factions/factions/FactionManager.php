<?php namespace Taco\Factions\factions;

use JsonException;
use pocketmine\utils\Config;
use Taco\Factions\factions\objects\FactionBank;
use Taco\Factions\factions\objects\FactionClaims;
use Taco\Factions\factions\objects\FactionInvite;
use Taco\Factions\factions\objects\FactionMember;
use Taco\Factions\factions\objects\FactionPermissionTypes;
use Taco\Factions\factions\objects\FactionRole;
use Taco\Factions\Main;

class FactionManager {

    /** @var array<Faction> */
    private array $factions;

    /** @var Config */
    private Config $store;

    public function __construct() {
        $this->store = new Config(Main::getInstance()->getDataFolder() . "factions.yml", Config::YAML);

        $roles = [
            new FactionRole(
                "Member",
                [
                    FactionPermissionTypes::PERMISSION_BUILD,
                    FactionPermissionTypes::PERMISSION_PLACE
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
                    $roles[$memberData["role"]] ?? $roles["Member"],
                );
            }

            $this->factions[] = new Faction(
                $name,
                $data["description"],
                $data["tag"],
                $invites,
                $members,
                new FactionClaims($data["claims"]),
                $data["allies"],
                $data["enemies"],
                $data["power"],
                new FactionBank($data["balance"])
            );
        }
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

            $store->set($faction->getName(), [
                "description" => $faction->getDescription(),
                "tag" => $faction->getTag(),
                "invites" => $faction->getInvites(),
                "members" => $members,
                $faction->getClaimManager()->getClaims(),
                $faction->getAllies(),
                $faction->getEnemies(),
                $faction->getPower(),
                $faction->getBank()->getBalance()
            ]);
        }
        $store->save();
    }

}