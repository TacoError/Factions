<?php namespace Taco\Factions\crates;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Server;
use Taco\Factions\Manager;

class CrateListener implements Listener {

    public function onInteract(PlayerInteractEvent $event) : void {
        $player = $event->getPlayer();
        $item = $event->getItem();
        if (is_null($crate = Manager::getCrateManager()->getCrateAt($event->getBlock()->getPosition()))) {
            return;
        }
        if (!$crate->isValidKey($item)) {
            if ($event->getAction() !== PlayerInteractEvent::LEFT_CLICK_BLOCK) return;
            $crate->openDropsMenu($player);
            return;
        }
        $event->cancel();
        $player->getInventory()->setItemInHand($item->setCount($item->getCount() - 1));
        Server::getInstance()->dispatchCommand(
            new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()),
            str_replace("{name}", $player->getName(), $crate->getRandomReward())
        );
    }

}