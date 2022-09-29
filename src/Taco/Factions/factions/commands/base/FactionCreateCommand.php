<?php namespace Taco\Factions\factions\commands\base;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Taco\Factions\commands\CoreSubCommand;
use Taco\Factions\factions\FactionManager;
use Taco\Factions\utils\Format;
use Taco\Factions\utils\TextUtils;

class FactionCreateCommand extends CoreSubCommand {

    public function __construct(private FactionManager $manager) {
        parent::__construct("create", "Create a faction! [name, tag]");
    }

    public function execute(Player|CommandSender $sender, array $args = []) : void {
        if (!$sender instanceof Player) return;
        if (!is_null($this->manager->getPlayerFaction($sender))) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You need to leave or disband your current faction to make another one.");
            return;
        }
        if (count($args) < 2) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "Please provide a name, and tag for your faction.");
            return;
        }
        $name = $args[0];
        if (!is_null($reason = TextUtils::validateRegularText($name, "faction name", 3, 12 . Format::PREFIX_FACTIONS_BAD))) {
            $sender->sendMessage($reason);
            return;
        }
        $tag = strtoupper($args[1]);
        if (!is_null($reason = TextUtils::validateRegularText($tag, "faction tag", 2, 4, Format::PREFIX_FACTIONS_BAD))) {
            $sender->sendMessage($reason);
            return;
        }
        $this->manager->createFaction($name, $tag, $sender->getName());
        $sender->sendMessage(Format::PREFIX_FACTIONS_GOOD . "Successfully created the faction " . $name . " [" . $tag . "]");
    }

}