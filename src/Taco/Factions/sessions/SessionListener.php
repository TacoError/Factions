<?php namespace Taco\Factions\sessions;

use JsonException;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use Taco\Factions\Manager;
use Taco\Factions\utils\ChatTypes;

class SessionListener implements Listener {

    public function onLogin(PlayerLoginEvent $event) : void {
        $player = $event->getPlayer();
        Manager::getSessionManager()->openSession($player);
    }

    /*** @throws JsonException */
    public function onQuit(PlayerQuitEvent $event) : void {
        $player = $event->getPlayer();
        Manager::getSessionManager()->closeSession($player);
    }

    public function onChat(PlayerChatEvent $event) : void {
        $player = $event->getPlayer();
        $faction = Manager::getFactionManager()->getPlayerFaction($player);
        $session = Manager::getSessionManager()->getSession($player);
        $fancy = Manager::getGroupManager()->getHighestGroupAuthority($session->getGroups());
        if ($session->getChatType() == ChatTypes::CHAT_PUBLIC || is_null($faction)) {
            $event->setFormat((is_null($faction) ? "" : "§7[§f" . $faction->getTag() . "§r§7] ")."[" . $fancy->getFancyName() . "§r§7] §f" . $player->getName() . " §8» §7" . $event->getMessage());
            return;
        }
        $event->cancel();
        if ($session->getChatType() == ChatTypes::CHAT_FACTION) {
            $faction->sendMessageToOnlineMembers("§7[§eFaction§7] §f" . $player->getName() . " §7» §f" . $event->getMessage());
            return;
        }
        $faction->sendMessageAllies("§7[§eAllies§7] §7(" . $faction->getTag() . ") §f" . $player->getName() . " §7»§f " . $event->getMessage());
    }

}