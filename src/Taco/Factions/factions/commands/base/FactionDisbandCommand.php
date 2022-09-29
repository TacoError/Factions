<?php namespace Taco\Factions\factions\commands\base;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Taco\Factions\commands\CoreSubCommand;
use Taco\Factions\factions\FactionManager;

class FactionDisbandCommand extends CoreSubCommand {

    public function __construct(private FactionManager $manager) {
        parent::__construct("disband", "Disband your faction.");
    }

    public function execute(Player|CommandSender $sender, array $args = []) : void {
        // TODO: Implement execute() method.
    }


}