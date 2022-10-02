<?php namespace Taco\Factions\factions\commands\invites;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Taco\Factions\commands\CoreSubCommand;
use Taco\Factions\factions\FactionManager;
use Taco\Factions\Main;
use Taco\Factions\utils\Format;

class FactionInvitesCommand extends CoreSubCommand {

    public function __construct(private FactionManager $manager) {
        parent::__construct("invites", "See all the factions you have invites from.");
    }

    public function execute(Player|CommandSender $sender, array $args = []) : void {
        if (!$sender instanceof Player) return;
        $invites = array_map(fn($faction) => $faction->getName(), array_filter(
            array_values($this->manager->getFactions()),
            fn($faction) => $faction->hasInvite($sender->getName() && $faction->getInvite($sender->getName())->getTimeSinceSent() < Main::$config["faction-invite-length"]))
        );
        $sender->sendMessage(Format::PREFIX_FACTIONS . "6Invites: (" . implode(",", $invites) . ")");
    }

}