<?php namespace Taco\Factions\factions\commands\claim;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Taco\Factions\commands\CoreSubCommand;
use Taco\Factions\factions\FactionManager;
use Taco\Factions\factions\objects\FactionPermissionTypes;
use Taco\Factions\utils\Format;

class FactionClaimCommand extends CoreSubCommand {

    public function __construct(private FactionManager $manager) {
        parent::__construct("claim", "Add a claim for your faction.");
    }

    public function execute(Player|CommandSender $sender, array $args = []) : void {
        if (!$sender instanceof Player) return;
        if (is_null($faction = $this->manager->getPlayerFaction($sender))) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You must be in a faction to use a few commands.");
            return;
        }
        $member = $faction->getMemberFromName($sender->getName());
        if (!$member->getRole()->hasPermission(FactionPermissionTypes::PERMISSION_ADD_CLAIM)) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You do not have permission to make a claim for your faction.");
            return;
        }
        if (!$faction->getClaimManager()->canClaimAt($sender->getPosition(), $sender->getWorld(), $faction->getName())) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You cannot claim here.");
            return;
        }
        $faction->getClaimManager()->addClaim($sender->getPosition(), $sender->getWorld());
        $sender->sendMessage(Format::PREFIX_FACTIONS_GOOD . "You have claimed that chunk.");
    }

}