<?php namespace Taco\Factions\factions\commands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use Taco\Factions\commands\CoreCommand;
use Taco\Factions\factions\commands\base\FactionCreateCommand;
use Taco\Factions\factions\commands\base\FactionDisbandCommand;
use Taco\Factions\factions\commands\invites\FactionAcceptInviteCommand;
use Taco\Factions\factions\commands\invites\FactionInviteCommand;
use Taco\Factions\factions\commands\invites\FactionInvitesCommand;
use Taco\Factions\Manager;

class FactionCommand extends CoreCommand {

    public function __construct() {
        parent::__construct("factions", "Factions base command.", null, ["f", "fac", "faction"]);

        $manager = Manager::getFactionManager();
        $this->addSubCommands([
            new FactionCreateCommand($manager),
            new FactionDisbandCommand($manager),
            new FactionInviteCommand($manager),
            new FactionAcceptInviteCommand($manager),
            new FactionInvitesCommand($manager)
        ]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        $validSubCommand = TextFormat::RED . "Please provide a valid subcommand. Use " . TextFormat::GREEN . "/f help" . TextFormat::RED . " to see a list of subcommands.";
        if (count($args) < 1) {
            $sender->sendMessage($validSubCommand);
            return;
        }
        $command = array_shift($args);
        if ($this->subCommandCheck($sender, $command, $args)) return;
        $sender->sendMessage($validSubCommand);
    }

}