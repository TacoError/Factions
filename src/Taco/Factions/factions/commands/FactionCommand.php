<?php namespace Taco\Factions\factions\commands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use Taco\Factions\commands\CoreCommand;
use Taco\Factions\factions\commands\bank\FactionAddMoneyCommand;
use Taco\Factions\factions\commands\bank\FactionBalanceCommand;
use Taco\Factions\factions\commands\bank\FactionWithdrawCommand;
use Taco\Factions\factions\commands\base\FactionCreateCommand;
use Taco\Factions\factions\commands\base\FactionDisbandCommand;
use Taco\Factions\factions\commands\base\FactionHelpCommand;
use Taco\Factions\factions\commands\base\FactionOpenVaultCommand;
use Taco\Factions\factions\commands\claim\FactionClaimCommand;
use Taco\Factions\factions\commands\claim\FactionDelClaimCommand;
use Taco\Factions\factions\commands\invites\FactionAcceptInviteCommand;
use Taco\Factions\factions\commands\invites\FactionInviteCommand;
use Taco\Factions\factions\commands\invites\FactionInvitesCommand;
use Taco\Factions\factions\commands\leadership\FactionDemoteCommand;
use Taco\Factions\factions\commands\leadership\FactionLeaderCommand;
use Taco\Factions\factions\commands\leadership\FactionPromoteCommand;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;

class FactionCommand extends CoreCommand {

    public function __construct() {
        parent::__construct("factions", "Factions base command.", null, ["f", "fac", "faction"]);

        $manager = Manager::getFactionManager();
        $this->addSubCommands([
            new FactionCreateCommand($manager),
            new FactionDisbandCommand($manager),
            new FactionInviteCommand($manager),
            new FactionAcceptInviteCommand($manager),
            new FactionInvitesCommand($manager),
            new FactionHelpCommand($this),
            new FactionWithdrawCommand($manager),
            new FactionAddMoneyCommand($manager),
            new FactionBalanceCommand($manager),
            new FactionClaimCommand($manager),
            new FactionDelClaimCommand($manager),
            new FactionPromoteCommand($manager),
            new FactionDemoteCommand($manager),
            new FactionLeaderCommand($manager),
            new FactionOpenVaultCommand($manager)
        ]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        $validSubCommand = Format::PREFIX_FACTIONS_BAD . "Invalid subcommand, use /f help for a list of subcommands.";
        if (count($args) < 1) {
            $sender->sendMessage($validSubCommand);
            return;
        }
        $command = array_shift($args);
        if ($this->subCommandCheck($sender, $command, $args)) return;
        $sender->sendMessage($validSubCommand);
    }

}