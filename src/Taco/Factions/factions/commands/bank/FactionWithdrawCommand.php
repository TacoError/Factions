<?php namespace Taco\Factions\factions\commands\bank;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Taco\Factions\commands\CoreSubCommand;
use Taco\Factions\factions\FactionManager;
use Taco\Factions\factions\objects\FactionPermissionTypes;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;

class FactionWithdrawCommand extends CoreSubCommand {

    public function __construct(private FactionManager $manager) {
        parent::__construct("withdraw", "Withdraw from your factions balance.");
    }

    public function execute(Player|CommandSender $sender, array $args = []) : void {
        if (!$sender instanceof Player) return;
        if (is_null($faction = $this->manager->getPlayerFaction($sender))) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You must be in a faction to use this command.");
            return;
        }
        if (!$faction->getMemberFromName($sender->getName())->getRole()->hasPermission(FactionPermissionTypes::PERMISSION_BANK_TAKE)) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You do not have permission to withdraw from your factions balance.");
            return;
        }
        if (count($args) < 1) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "Please provide a amount to withdraw.");
            return;
        }
        $amount = $args[0];
        if (!is_numeric($amount) || $amount < 1) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "The amount must be a numeric and above 0.");
            return;
        }
        $amount = abs($amount);
        if ($faction->getBank()->getBalance() < $amount) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "Your faction does not have enough money to withdraw.");
            return;
        }
        $faction->getBank()->takeBalancePlayer($sender, $amount);
        $sender->sendMessage(Format::PEFIX_FACTIONS_GOOD . "You have taken $" . Format::intToPrefix($amount) . " from the faction balance.");
        $faction->sendMessageToOnlineMembers(Format::PREFIX_FACTIONS . "e" . $sender->getName() . " has taken $" . Format::intToPrefix($amount) . " from the faction balance.");
    }

}