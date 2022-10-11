<?php namespace Taco\Factions\factions\commands\base;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Taco\Factions\commands\CoreSubCommand;
use Taco\Factions\factions\FactionManager;
use Taco\Factions\Manager;
use Taco\Factions\utils\ChatTypes;
use Taco\Factions\utils\Format;

class FactionChatCommand extends CoreSubCommand {

    public function __construct(private FactionManager $manager) {
        parent::__construct("chat", "Switch your chat mode.");
    }

    public function execute(Player|CommandSender $sender, array $args = []) : void {
        if (!$sender instanceof Player) return;
        if (is_null($this->manager->getPlayerFaction($sender))) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You must be in a faction to use that command.");
            return;
        }
        $session = Manager::getSessionManager()->getSession($sender);
        if ($session->getChatType() == ChatTypes::CHAT_PUBLIC) {
            $session->setChatType(ChatTypes::CHAT_FACTION);
            $sender->sendMessage(Format::PREFIX_FACTIONS_GOOD . "You are now in faction chat.");
        } else if ($session->getChatType() == ChatTypes::CHAT_FACTION) {
            $session->setChatType(2);
            $sender->sendMessage(Format::PREFIX_FACTIONS_GOOD . "You are now in faction-ally chat.");
        } else {
            $session->setChatType(ChatTypes::CHAT_PUBLIC);
            $sender->sendMessage(Format::PREFIX_FACTIONS_GOOD . "You are now in public chat.");
        }
    }

}