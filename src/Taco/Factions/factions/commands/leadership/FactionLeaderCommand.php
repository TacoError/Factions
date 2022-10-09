<?php namespace Taco\Factions\factions\commands\leadership;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use Taco\Factions\commands\CoreSubCommand;
use Taco\Factions\factions\FactionManager;
use Taco\Factions\factions\objects\FactionPermissionTypes;
use Taco\Factions\utils\Format;

class FactionLeaderCommand extends CoreSubCommand {

    public function __construct(private FactionManager $manager) {
        parent::__construct("leader", "Transfer leadership of your faction.");
    }

    public function execute(Player|CommandSender $sender, array $args = []) : void {
        if (!$sender instanceof Player) return;
        if (is_null($faction = $this->manager->getPlayerFaction($sender))) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You must be in a faction to use this command.");
            return;
        }
        if (!$faction->getMemberFromName($sender->getName())->getRole()->hasPermission(FactionPermissionTypes::PERMISSION_ALL)) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You cannot do this.");
            return;
        }
        if (is_null($player = Server::getInstance()->getPlayerByPrefix($args[0]))) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "This player is not online or doesn't exist.");
            return;
        }
        if (is_null($member = $faction->getMemberFromName($player->getName()))) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "That player is not in your faction.");
            return;
        }
        $member->setRole($this->manager->getLeaderRole());
        $faction->getMemberFromName($sender->getName())->setRole($this->manager->getLeaderRole());
        $sender->sendMessage(Format::PREFIX_FACTIONS_GOOD . "You have transferred leadership to: " . $member->getName() . ".");
    }

}