<?php namespace Taco\Factions\factions\commands\invites;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use Taco\Factions\commands\CoreSubCommand;
use Taco\Factions\factions\FactionManager;
use Taco\Factions\factions\objects\FactionPermissionTypes;
use Taco\Factions\Main;
use Taco\Factions\utils\Format;

class FactionInviteCommand extends CoreSubCommand {

    public function __construct(private FactionManager $manager) {
        parent::__construct("invite", "Invite a player to your faction. [player]");
    }

    public function execute(Player|CommandSender $sender, array $args = []) : void {
        if (!$sender instanceof Player) return;
        if (is_null($faction = $this->manager->getPlayerFaction($sender))) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You must be in a faction to disband a faction.");
            return;
        }
        $member = $faction->getMemberFromName($sender->getName());
        if (is_null($member) || !$member->getRole()->hasPermission(FactionPermissionTypes::PERMISSION_INVITE)) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You don't have permission to invite a player to your faction!");
            return;
        }
        if (is_null($player = Server::getInstance()->getPlayerByPrefix($args[0]))) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "That player is not online or doesn't exist.");
            return;
        }
        if ($faction->hasInvite($player->getName()) && !$faction->getInvite($player->getName())->getTimeSinceSent() > Main::$config["faction-invite-length"]) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "That player already has a invite to your faction.");
            return;
        }
        if ($sender->getName() == $player->getName()) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You cannot invite yourself.");
            return;
        }
        $faction->invite($sender, $player);
        $faction->sendMessageToOnlineMembers(Format::PREFIX_FACTIONS . "e" . $sender->getName()." has invited " . $player->getName() . " to your faction.");
        $sender->sendMessage(Format::PREFIX_FACTIONS_GOOD . "Successfully invited the player \"" . $player->getName() . "\"!");
        $player->sendMessage(Format::PREFIX_FACTIONS . "eYou have been invited to the faction \"" . $faction->getName() . " [" . $faction->getTag() . "]\"!");
    }

}