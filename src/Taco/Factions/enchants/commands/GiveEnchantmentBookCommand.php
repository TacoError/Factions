<?php namespace Taco\Factions\enchants\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use Taco\Factions\enchants\types\CoreEnchant;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;

class GiveEnchantmentBookCommand extends Command {

    public function __construct() {
        parent::__construct("giveecbook", "Give a enchantment book to someone.");
        $this->setPermission("core.enchantments");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->testPermission($sender)) return;
        if (count($args) < 3) {
            $sender->sendMessage(Format::PREFIX_ENCHANT . "cUsage: /giveecbook (player) (name) (level) (successChance = random)");
            return;
        }
        if (is_null($player = Server::getInstance()->getPlayerByPrefix($args[0]))) {
            $sender->sendMessage(Format::PREFIX_ENCHANT . "cThat player is not online or doesn't exist.");
            return;
        }
        $ecm = Manager::getEnchantManager();
        if (is_null($enchantment = $ecm->getEnchantmentFromName($args[1]))) {
            $sender->sendMessage(Format::PREFIX_ENCHANT . "cThat enchantment doesn't exist.");
            return;
        }
        if (!$enchantment instanceof CoreEnchant) return;
        $player->getInventory()->addItem($ecm->makeBook($enchantment, $args[2], ($args[3] ?? 0)));
        $sender->sendMessage(Format::PREFIX_ENCHANT . "aGave book to player.");
        $player->sendMessage(Format::PREFIX_ENCHANT . "aYou have received a enchantment book.");
    }

}