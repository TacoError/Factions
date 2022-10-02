<?php namespace Taco\Factions\factions\commands\base;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Taco\Factions\commands\CoreSubCommand;
use Taco\Factions\factions\commands\FactionCommand;
use Taco\Factions\utils\Format;

class FactionHelpCommand extends CoreSubCommand {

    public function __construct(private FactionCommand $parent) {
        parent::__construct("help", "See a list of faction commands. [page]");
    }

    public function execute(Player|CommandSender $sender, array $args = []) : void {
        $commands = $this->parent->getSubCommands();
        $page = abs(count($args) > 0 ? $args[0] : 1);
        if (!is_numeric($page)) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "The page must be a number.");
            return;
        }

        if (($page * 10) < count($commands)) {
            if ($page !== 1) $display = array_slice($commands, $page * 10);
            else $display = $commands;
        }  else $display = array_slice($commands, count($commands) - (count($commands) % 10), count($commands) - 1);

        $message = ["§7[§cFactions Help§r§7] §7(Page 1 / " . (max((int)(count($commands) / 10), 1)) . ")"];
        $count = 0;
        foreach ($display as $command) {
            $count++;
            if ($count > 10) break;
            $message[] = "§7" . $count . ". §f/f " . $command->getName() . " §7> §e" . $command->getDescription();
        }
        $sender->sendMessage(implode("\n", $message));
    }

}