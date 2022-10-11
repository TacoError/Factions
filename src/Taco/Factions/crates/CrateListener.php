<?php namespace Taco\Factions\crates;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;
use Taco\Factions\utils\VectorUtils;

class CrateListener implements Listener {

    public function onInteract(PlayerInteractEvent $event) : void {
        $player = $event->getPlayer();
        $cm = Manager::getCrateManager();
        $pos = $event->getBlock()->getPosition();
        if (isset($cm->settingPositions[$player->getName()])) {
            $event->cancel();
            if (!is_null($cm->getCrateAt($pos))) {
                $cm->removePosition(VectorUtils::positionToString($pos));
                unset($cm->settingPositions[$player->getName()]);
                $player->sendMessage(Format::PREFIX_CRATE . "aRemoved position.");
                return;
            }
            $cm->addPosition(VectorUtils::positionToString($pos), $type = $cm->settingPositions[$player->getName()]);
            $player->sendMessage(Format::PREFIX_CRATE . "aSet position for " . $type . ".");
            unset($cm->settingPositions[$player->getName()]);
            return;
        }
        if (is_null($crate = $cm->getCrateAt($pos))) return;
        $event->cancel();
        if ($event->getAction() == PlayerInteractEvent::LEFT_CLICK_BLOCK) {
            $crate->openRewardsMenu($player);
            return;
        }
        $item = $player->getInventory()->getItemInHand();
        if ($crate->isKeyForCrate($item)) {
            $player->getInventory()->setItemInHand($item->setCount($item->getCount() - 1));
            $player->sendMessage(Format::PREFIX_CRATE . "aYou have received a reward!");
            $crate->giveRandomRewardTo($player);
            return;
        }
        $player->sendMessage(Format::PREFIX_CRATE . "cThat key is not for this crate!");
    }

}
