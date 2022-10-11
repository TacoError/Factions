<?php namespace Taco\Factions\factions\commands\base;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Taco\Factions\commands\CoreSubCommand;
use Taco\Factions\factions\FactionManager;
use Taco\Factions\utils\Format;

class FactionInfoCommand extends CoreSubCommand {

    public function __construct(private FactionManager $manager) {
        parent::__construct("info", "See a factions info");
    }

    public function execute(Player|CommandSender $sender, array $args = []) : void {
        if (!$sender instanceof Player) return;
        $faction = $this->manager->getPlayerFaction($sender);
        if (count($args) < 1) {
            if (is_null($faction)) {
                $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "Please provide a faction.");
                return;
            }
            $faction->sendInfo($sender);
            return;
        }
        if (is_null($faction = $this->manager->getFactionFromName($args[0]))) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "That faction doesn't exist.");
            return;
        }
        $faction->sendInfo($sender);
    }

}