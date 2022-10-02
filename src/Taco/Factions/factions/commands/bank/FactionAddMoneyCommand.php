<?php namespace Taco\Factions\factions\commands\bank;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Taco\Factions\commands\CoreSubCommand;
use Taco\Factions\factions\FactionManager;
use Taco\Factions\factions\objects\FactionPermissionTypes;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;

class FactionAddMoneyCommand extends CoreSubCommand {

    public function __construct(private FactionManager $manager) {
        parent::__construct("addmoney", "Add to your factions balance.");
    }

    public function execute(Player|CommandSender $sender, array $args = []) : void {
        if (!$sender instanceof Player) return;
        if (is_null($faction = $this->manager->getPlayerFaction($sender))) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You must be in a faction to use this command.");
            return;
        }
        if (!$faction->getMemberFromName($sender->getName())->getRole()->hasPermission(FactionPermissionTypes::PERMISSION_BANK_ADD)) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You do not have permission to add to your factions balance.");
            return;
        }
        if (count($args) < 1) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "Please provide a amount to add.");
            return;
        }
        $amount = $args[0];
        if (!is_numeric($amount) || $amount < 1) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "The amount must be a numeric and above 0.");
            return;
        }
        $amount = abs($amount);
        if (Manager::getSessionManager()->getSession($sender)->getBalance() < $amount) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You do not have enough money.");
            return;
        }
        $faction->getBank()->addBalance($sender, $amount);
        $sender->sendMessage(Format::PREFIX_FACTIONS_GOOD . "You have added $" . Format::intToPrefix($amount) . " to your factions balance.");
        $faction->sendMessageToOnlineMembers(Format::PREFIX_FACTIONS . "e" . $sender->getName() . " has given $" . Format::intToPrefix($amount) . " to the faction balance.");
    }

}