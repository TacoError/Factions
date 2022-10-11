<?php namespace Taco\Factions\crates\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;

class EditCrateCommand extends Command {

    public function __construct() {
        parent::__construct("editcrate", "Edit a crates items.");
        $this->setPermission("core.crates");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender) || !$sender instanceof Player) return;
        if (count($args) < 1) {
            $sender->sendMessage(Format::PREFIX_CRATE . "cUsage: /editcrate (name)");
            return;
        }
        if (is_null($crate = Manager::getCrateManager()->getCrateFromName($args[0]))) {
            $sender->sendMessage(Format::PREFIX_CRATE . "cInvalid crate.");
            return;
        }
        $crate->openEditRewardsChest($sender);
    }

}