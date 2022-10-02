<?php namespace Taco\Factions\kits\commands;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use Taco\Factions\Manager;

class EditKitCommand extends Command {

    public function __construct() {
        parent::__construct("editkit", "Edit a kits items. /editkit (name)");
        $this->setPermission("core.kits");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender) || !$sender instanceof Player) return;
        if (count($args) < 1) {
            $sender->sendMessage("Incorrect usage, correct usage: /editkit (name)");
            return;
        }
        $name = $args[0];
        $km = Manager::getKitManager();
        if (is_null($kit = $km->getKitFromName($name))) {
            $sender->sendMessage("There is no kit with that name.");
            return;
        }
        $sender->sendMessage("Opening kit editing menu");
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->send($sender, "Items for kit | Close when done");
        $inv = $menu->getInventory();
        foreach ($kit->getItems() as $item) {
            $inv->addItem($item);
        }
        $menu->setInventoryCloseListener(function(Player $player, Inventory $inventory) use ($km, $kit) : void {
            $km->editKitItems($kit->getName(), $inventory->getContents());
            $player->sendMessage("Edited kit named \"" . $kit->getName() . "\"");
        });
    }

}