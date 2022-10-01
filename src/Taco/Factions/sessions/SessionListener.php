<?php namespace Taco\Factions\sessions;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use Taco\Factions\Manager;

class SessionListener implements Listener {

    public function onLogin(PlayerLoginEvent $event) : void {
        $player = $event->getPlayer();
        Manager::getSessionManager()->openSession($player);
    }

    public function onQuit(PlayerQuitEvent $event) : void {
        $player = $event->getPlayer();
        Manager::getSessionManager()->closeSession($player);
    }

}