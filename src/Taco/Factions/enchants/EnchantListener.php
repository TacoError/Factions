<?php namespace Taco\Factions\enchants;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\inventory\CallbackInventoryListener;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Book;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use Taco\Factions\enchants\types\ArmorToggleEnchant;
use Taco\Factions\enchants\types\CoreEnchant;
use Taco\Factions\enchants\types\SwordHitEnchant;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;

class EnchantListener implements Listener {

    public function onJoin(PlayerJoinEvent $event) : void {
        $player = $event->getPlayer();
        foreach ($player->getArmorInventory()->getContents() as $item) {
            foreach ($item->getEnchantments() as $enchantment) {
                if (!$enchantment instanceof ArmorToggleEnchant) continue;
                $enchantment->onEquip($player, $item->getEnchantmentLevel($enchantment));
            }
        }

        $onSlot = function(Inventory $inventory, int $slot, Item $old) use($player) : void {
            $new = $inventory->getItem($slot);
            if(!$new->equals($old, false, true)){
                foreach($new->getEnchantments() as $enchant){
                    $enchant = $enchant->getType();
                    if($enchant instanceof ArmorToggleEnchant) $enchant->onEquip($player, $new->getEnchantmentLevel($enchant));
                }
            }
            if(!$old->equals($new, false, true)){
                foreach($old->getEnchantments() as $enchant){
                    $enchant = $enchant->getType();
                    if ($enchant instanceof ArmorToggleEnchant) $enchant->onUnEquip($player);
                }
            }
        };
        $player->getArmorInventory()->getListeners()->add(new CallbackInventoryListener($onSlot, function(Inventory $inventory, array $oldContents) use($player, $onSlot) : void {
            foreach($oldContents as $slot => $old){
                if (!($oldItem ?? ItemFactory::air())->equals($inventory->getItem($slot), false)) {
                    $onSlot($inventory, $slot, $old);
                }
            }
        }));
    }

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
        $enchant = $ecm->getEnchantmentFromName($book->getNamedTag()->getString("enchantmentBook"));
        if (!$enchant instanceof CoreEnchant) return;
        if (!$ecm->canBeAppliedTo($enchant, $item)) {
            $player->sendMessage(Format::PREFIX_ENCHANT . "cThis enchant cannot be applied to this item.");
            return;
        }
        $event->cancel();
        $bookAction->getInventory()->setItem($bookAction->getSlot(), VanillaItems::AIR());
        $c = mt_rand(0, 100);
        if ($c > $chance->getValue()) {
            $player->sendMessage(Format::PREFIX_ENCHANT . "cYour enchant failed!");
            $itemAction->getInventory()->setItem($itemAction->getSlot(), VanillaItems::AIR());
            return;
        }
        $item->addEnchantment(new EnchantmentInstance($enchant, $book->getNamedTag()->getInt("enchantmentBookLevel")));
        $item = $ecm->setItem($item);
        $item = $ecm->brandItem($item);
        $itemAction->getInventory()->setItem($itemAction->getSlot(), $item);
        $player->sendMessage(Format::PREFIX_ENCHANT . "aYour enchant succeeded.");
    }

}