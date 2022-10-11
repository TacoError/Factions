<?php namespace Taco\Factions\crates\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;

class SetCratePositionCommand extends Command {

    public function __construct() {
        parent::__construct("setcratepos", "Set a crates position.");
        $this->setPermission("core.crates");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (count($args) < 1) {
            $sender->sendMessage(Format::PREFIX_CRATE . "cUsage: /setcratepos (name)");
            return;
        }
        if (is_null($crate = Manager::getCrateManager()->getCrateFromName($args[0]))) {
            $sender->sendMessage(Format::PREFIX_CRATE . "cInvalid crate.");
            return;
        }
        Manager::getCrateManager()->settingPositions[$sender->getName()] = $crate->getName();
        $sender->sendMessage(Format::PREFIX_CRATE . "aYou are now in setting position mode.");
    }

}