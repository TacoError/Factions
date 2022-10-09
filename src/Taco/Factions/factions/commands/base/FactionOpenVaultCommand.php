<?php namespace Taco\Factions\factions\commands\base;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\command\CommandSender;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use Taco\Factions\commands\CoreSubCommand;
use Taco\Factions\factions\FactionManager;
use Taco\Factions\factions\objects\FactionPermissionTypes;
use Taco\Factions\utils\Format;

class FactionOpenVaultCommand extends CoreSubCommand {

    public function __construct(private FactionManager $manager) {
        parent::__construct("vault", "Open your faction vault.");
    }

    public function execute(Player|CommandSender $sender, array $args = []) : void {
        if (!$sender instanceof Player) return;
        if (is_null($faction = $this->manager->getPlayerFaction($sender))) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You must be in a faction to open the faction vault.");
            return;
        }
        $member = $faction->getMemberFromName($sender->getName());
        if (is_null($member) || !$member->getRole()->hasPermission(FactionPermissionTypes::PERMISSION_VAULT)) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "You don't have permission to open your faction vault.");
            return;
        }
        if ($faction->getVaultState()) {
            $sender->sendMessage(Format::PREFIX_FACTIONS_BAD . "Only one person can open the faction vault at a time.");
            return;
        }
        $faction->setVaultState(true);

        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->send($sender, "Faction Vault");
        $inv = $menu->getInventory();
        foreach ($faction->getVaultItems() as $pos => $item) {
            $inv->setItem($pos, $item);
        }
        $menu->setInventoryCloseListener(function(Player $player, Inventory $inventory) use ($faction) : void {
            $faction->setVaultItems($inventory->getContents(true));
            $faction->setVaultState(false);
        });
    }


}