<?php namespace Taco\Factions\factions\commands\invites;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Taco\Factions\commands\CoreSubCommand;
use Taco\Factions\factions\FactionManager;
use Taco\Factions\Main;
use Taco\Factions\utils\Format;

class FactionAcceptInviteCommand extends CoreSubCommand {

    public function __construct(private FactionManager $manager) {
        parent::__construct("join", "Join a faction.");
    }

    public function execute(Player|CommandSender $sender, array $args = []) : void {
        if (!$sender instanceof Player) return;
        if (!is_null($this->manager->getPlayerFaction($sender))) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You must leave your current faction to join a new faction.");
            return;
        }
        if (count($args) < 1) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You must provide a faction to join.");
            return;
        }
        if (is_null($faction = $this->manager->getFactionFromName($args[0]))) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "There is no faction with that name.");
            return;
        }
        if (!$this->manager->hasInviteFromFaction($sender, $args[0])) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You do not have a invite from that faction!");
            return;
        }
        if ($faction->getInvite($sender->getName())->getTimeSinceSent() > Main::$config["faction-invite-length"]) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "That invite has expired.");
            return;
        }
        $faction->acceptInvite($sender);
    }

}