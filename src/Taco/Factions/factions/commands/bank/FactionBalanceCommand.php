<?php namespace Taco\Factions\factions\commands\bank;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Taco\Factions\commands\CoreSubCommand;
use Taco\Factions\factions\FactionManager;
use Taco\Factions\utils\Format;

class FactionBalanceCommand extends CoreSubCommand {

    public function __construct(private FactionManager $manager) {
        parent::__construct("balance", "See your factions balance");
    }

    public function execute(Player|CommandSender $sender, array $args = []) : void {
        if (!$sender instanceof Player) return;
        if (is_null($faction = $this->manager->getPlayerFaction($sender))) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You must be in a faction to use this command.");
            return;
        }
        $sender->sendMessage(Format::PREFIX_FACTIONS_GOOD . "Your factions balance: $" . Format::intToPrefix($faction->getBank()->getBalance()));
    }

}