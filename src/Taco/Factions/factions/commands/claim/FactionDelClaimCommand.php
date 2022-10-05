<?php namespace Taco\Factions\factions\commands\claim;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Taco\Factions\commands\CoreSubCommand;
use Taco\Factions\factions\FactionManager;
use Taco\Factions\factions\objects\FactionPermissionTypes;
use Taco\Factions\utils\Format;

class FactionDelClaimCommand extends CoreSubCommand {

    public function __construct(private FactionManager $manager) {
        parent::__construct("delclaim", "Remove a claim for your faction.");
    }

    public function execute(Player|CommandSender $sender, array $args = []) : void {
        if (!$sender instanceof Player) return;
        if (is_null($faction = $this->manager->getPlayerFaction($sender))) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You must be in a faction to use a few commands.");
            return;
        }
        $member = $faction->getMemberFromName($sender->getName());
        if (!$member->getRole()->hasPermission(FactionPermissionTypes::PERMISSION_REMOVE_CLAIM)) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You do not have permission to un claim land for your faction.");
            return;
        }
        if (!$faction->getClaimManager()->hasClaimAt($sender->getPosition(), $sender->getWorld())) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You do not have a claim here.");
            return;
        }
        $faction->getClaimManager()->removeClaimAt($sender->getPosition(), $sender->getWorld());
        $sender->sendMessage(Format::PREFIX_FACTIONS_GOOD . "You have unclaimed that chunk.");
    }

}