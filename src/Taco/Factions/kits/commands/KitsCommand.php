<?php namespace Taco\Factions\kits\commands;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;
use Taco\Factions\utils\TimeUtils;

class KitsCommand extends Command {

    public function __construct() {
        parent::__construct("kit", "Open the kit selection menu.");
        $this->setAliases(["kits"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) return;

        $allowed = Manager::getKitManager()->getAllowedKits($sender);
        $session = Manager::getSessionManager()->getSession($sender);

        $form = new SimpleForm(function(Player $player, ?int $data) use ($allowed, $session) {
            if (is_null($data)) return;
            if ($data > (count($allowed) - 1)) return;

            $kit = $allowed[$data];
            $coolDown = $kit->getCoolDown() - (time() - $session->getKitCoolDown($kit->getName()));;
            if ($coolDown > 1) {
                $player->sendMessage(Format::PREFIX_KITS . "cYou are still on coolDown for " . TimeUtils::intToDDdHhMm($coolDown) . "!");
                return;
            }
            $kit->giveKit($player);
        });

        $form->setTitle("Kits Menu");
        $form->setContent("Select a kit to equip.");
        foreach ($allowed as $kit) {
            $pCD = $session->getKitCoolDown($kit->getName());
            $coolDown = $kit->getCoolDown() - (time() - $session->getKitCoolDown($kit->getName()));
            if ($pCD < 1 || $coolDown < 1) {
                $form->addButton($kit->getName(). "\nCoolDown: 00:00:00");
                continue;
            }
            $form->addButton($kit->getName() . "\nCoolDown: " . TimeUtils::intToDDdHhMm($coolDown));
        }
        $form->addButton("Close Menu.");
        $sender->sendForm($form);
    }

}