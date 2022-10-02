<?php namespace Taco\Factions\kits\commands;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use Taco\Factions\Manager;

class CreateKitCommand extends Command {

    public function __construct() {
        parent::__construct("createkit", "Create a kit. (name) (coolDown IN SECONDS) (permission [optional])");
        $this->setPermission("core.kits");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender) || !$sender instanceof Player) return;
        if (count($args) < 2) {
            $sender->sendMessage("Incorrect usage, correct usage: /createkit (name) (cooldown IN SECONDS) (permission [optional])");
            return;
        }
        $permission = "";
        if (count($args) > 2) {
            $permission = $args[2];
        }
        $sender->sendMessage("Opening kit creation menu");
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->send($sender, "Items for kit | Close when done");
        $menu->setInventoryCloseListener(function(Player $player, Inventory $inventory) use ($args, $permission) : void {
            Manager::getKitManager()->createKit($args[0], $args[1], $permission, $inventory->getContents());
            $player->sendMessage("Created kit named \"" . $args[0] . "\" with a coolDown of " . $args[1] . "s and " . count($inventory->getContents()) . " items");
        });
    }

}