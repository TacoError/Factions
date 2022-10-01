<?php namespace Taco\Factions\factions\commands\base;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Taco\Factions\commands\CoreSubCommand;
use Taco\Factions\factions\FactionManager;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;

class FactionDisbandCommand extends CoreSubCommand {

    public function __construct(private FactionManager $manager) {
        parent::__construct("disband", "Disband your faction.");
    }

    public function execute(Player|CommandSender $sender, array $args = []) : void {
        if (!$sender instanceof Player) return;
        if (is_null($faction = Manager::getFactionManager()->getPlayerFaction($sender))) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You must be in a faction to disband a faction.");
            return;
        }
        $member = $faction->getMemberFromName($sender->getName());
        if (is_null($member) || !$member->getRole()->hasPermission(-1)) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You don't have permission to disband this faction!");
            return;
        }
        $this->manager->disbandFaction($sender);
    }


}