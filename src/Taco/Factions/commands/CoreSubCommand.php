<?php namespace Taco\Factions\commands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;

abstract class CoreSubCommand {

    /** @var string */
    private string $name;

    /** @var string */
    private string $description;

    /** @var string */
    private string $permission;

    public function __construct(string $name, string $description, string $permission = "") {
        $this->name = $name;
        $this->description = $description;
        $this->permission = $permission;
    }

    /*** @return string */
    public function getName() : string {
        return $this->name;
    }

    /*** @return string */
    public function getDescription() : string {
        return $this->description;
    }

    /*** @return string */
    public function getPermission() : string {
        return $this->permission;
    }

    /**
     * @param CommandSender|Player $sender
     * @param array $args
     * @return void
     */
    abstract function execute(CommandSender|Player $sender, array $args = []) : void;

}