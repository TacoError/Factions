<?php namespace Taco\Factions\enchants;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Book;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use Taco\Factions\enchants\types\SwordHitEnchant;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;

class EnchantListener implements Listener {

    public function onEntityDamage(EntityDamageByEntityEvent $event) : void {
        $killer = $event->getDamager();
        $hit = $event->getEntity();
        if (!$killer instanceof Player || !$hit instanceof Player) return;
        $item = $killer->getInventory()->getItemInHand();
        foreach ($item->getEnchantments() as $enchantment) {
            if (EnchantmentIdMap::getInstance()->toId($enchantment) < 50) continue;
            if ($enchantment instanceof SwordHitEnchant) {
                $enchantment->onHit($killer, $hit, $item->getEnchantmentLevel($enchantment));
            }
        }
    }

    public function onTransaction(InventoryTransactionEvent $event) : void {
        $transaction = $event->getTransaction();
        $player = $transaction->getSource();
        $actions = array_values($transaction->getActions());
        if (count($actions) !== 2) return;
        $bookAction = $actions[0];
        $itemAction = $actions[1];
        if (!$bookAction instanceof SlotChangeAction || !$itemAction instanceof SlotChangeAction) return;
        [$book, $item] = [$bookAction->getTargetItem(), $itemAction->getTargetItem()];
        if ($book->getId() !== ItemIds::BOOK) {
            [$book, $item] = [$itemAction->getTargetItem(), $bookAction->getTargetItem()];
            [$bookAction, $itemAction] = [$itemAction, $bookAction];
            if ($book->getId() !== ItemIds::BOOK) return;
        }
        $ecm = Manager::getEnchantManager();
        if (!$ecm->canHoldAnotherEnchant($item) && !$item instanceof Book) {
            return;
        }
        if (is_null($chance = $book->getNamedTag()->getTag("bookChance"))) return;
        $event->cancel();
        $bookAction->getInventory()->setItem($bookAction->getSlot(), VanillaItems::AIR());
        $c = mt_rand(0, 100);
        if ($c > $chance->getValue()) {
            $player->sendMessage(Format::PREFIX_ENCHANT . "cYour enchant failed!");
            $itemAction->getInventory()->setItem($itemAction->getSlot(), VanillaItems::AIR());
            return;
        }
        $enchant = $ecm->getEnchantmentFromName($book->getNamedTag()->getString("enchantmentBook"));
        $item->addEnchantment(new EnchantmentInstance($enchant, $book->getNamedTag()->getInt("enchantmentBookLevel")));
        $item = $ecm->setItem($item);
        $item = $ecm->brandItem($item);
        $itemAction->getInventory()->setItem($itemAction->getSlot(), $item);
        $player->sendMessage(Format::PREFIX_ENCHANT . "aYour enchant succeeded.");
    }

}