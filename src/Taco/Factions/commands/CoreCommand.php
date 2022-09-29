<?php namespace Taco\Factions\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

abstract class CoreCommand extends Command {

    /** @var array<CoreSubCommand> */
    private array $subCommands = [];

    public function __construct(string $name, string $description, ?string $permission = null, array $aliases = []) {
        parent::__construct($name, $description);
        if (!is_null($permission)) $this->setPermission($permission);
        $this->setAliases($aliases);
    }

    /**
     * Adds an array of subcommands
     *
     * @param array<CoreSubCommand> $subCommands
     * @return void
     */
    public function addSubCommands(array $subCommands) : void {
        foreach ($subCommands as $command) {
            $this->subCommands[] = $command;
        }
    }

    /**
     * @param CoreSubCommand $command
     * @return void
     */
    public function addSubCommand(CoreSubCommand $command) : void {
        $this->subCommands[] = $command;
    }

    /*** @return CoreSubCommand[] */
    public function getSubCommands() : array {
        return $this->subCommands;
    }

    /**
     * Checks for a subcommand, and executes if found
     *
     * @param CommandSender|Player $sender
     * @param string $command
     * @param array $args
     * @return bool
     */
    public function subCommandCheck(CommandSender|Player $sender, string $command, array $args = []) : bool {
        foreach ($this->subCommands as $sub) {
            if (!$sub->getName() == $command) continue;
            if ($sender instanceof Player && $sub->getPermission() !== "" && !$sender->hasPermission($sub->getPermission())) continue;
            $sub->execute($sender, $args);
            return true;
        }
        return false;
    }

}