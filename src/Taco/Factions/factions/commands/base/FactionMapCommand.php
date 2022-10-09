<?php namespace Taco\Factions\factions\commands\base;

use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;
use Taco\Factions\commands\CoreSubCommand;
use Taco\Factions\factions\FactionManager;
use Taco\Factions\Manager;
use Taco\Factions\utils\Format;

class FactionMapCommand extends CoreSubCommand {

    public function __construct(private FactionManager $manager) {
        parent::__construct("map", "Show all the claims around you.");
    }

    public function execute(Player|CommandSender $sender, array $args = []) : void {
        if (!$sender instanceof Player) return;
        $fac = $this->manager->getPlayerFaction($sender);
        $faction = $fac;
        if (is_null($fac)) $fac = "";
        else $fac = $fac->getName();
        $baseX = $sender->getPosition()->getX() >> 4;
        $baseZ = $sender->getPosition()->getZ() >> 4;
        $lines = [];
        for ($x = -5; $x <= 5; $x++) {
            for ($z = -5; $z <= 5; $z++) {
                if ($x == 0 && $z == 0) {
                    $lines[] = TextFormat::AQUA;
                    continue;
                }
                $claim = $this->manager->getFactionFromChunkHash(World::chunkHash($baseX + $x, $baseZ + $z), $sender->getWorld()->getDisplayName());
                if (is_null($claim)) {
                    $lines[] = TextFormat::GRAY;
                    continue;
                }
                if ($claim->getName() == $fac) {
                    $lines[] = TextFormat::GREEN;
                    continue;
                }
                if ($fac !== "" && $faction->isAlliedTo($claim->getName())) {
                    $lines[] = TextFormat::GOLD;
                }
            }
        }
        $lines = array_chunk($lines, 11);
        $message = [];
        foreach ($lines as $arr) {
            foreach ($arr as $color) {
                $message[] = $color . " + ";
            }
            $message[] = "\n";
        }
        $sender->sendMessage(Format::PREFIX_FACTIONS_GOOD . "\n§7[§b+§7] §fYou §7[§a+§7] §fYour Faction §7[§g+§7] §fAllies §7[+] §fUnclaimed");
        $sender->sendMessage(join("", $message));
    }


}