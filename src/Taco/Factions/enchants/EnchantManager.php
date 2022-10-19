<?php namespace Taco\Factions\enchants;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\Sword;
use pocketmine\item\Tool;
use pocketmine\item\VanillaItems;
use pocketmine\Server;
use Taco\Factions\enchants\commands\GiveEnchantmentBookCommand;
use Taco\Factions\enchants\enchants\armor\SpringsEnchant;
use Taco\Factions\enchants\enchants\sword\SlowEnchant;
use Taco\Factions\enchants\types\CoreEnchant;
use Taco\Factions\Main;

class EnchantManager {

    /** @var array<int, CoreEnchant> */
    private array $enchantments;

    private const DEFAULT_ENCHANTMENT_LIMITS = [
        "helmet" => 6,
        "chestPlate" => 8,
        "leggings" => 7,
        "boots" => 6,
        "sword" => 6,
        "tools" => 8
    ];

    public const IDS = [
        "helmet" => [
            ItemIds::DIAMOND_HELMET,
            ItemIds::IRON_HELMET,
            ItemIds::GOLD_HELMET,
            ItemIds::CHAIN_HELMET,
            ItemIds::LEATHER_HELMET,
        ],
        "chestPlate" => [
            ItemIds::DIAMOND_CHESTPLATE,
            ItemIds::IRON_CHESTPLATE,
            ItemIds::GOLD_CHESTPLATE,
            ItemIds::LEATHER_CHESTPLATE
        ],
        "leggings" => [
            ItemIds::DIAMOND_LEGGINGS,
            ItemIds::IRON_LEGGINGS,
            ItemIds::GOLD_LEGGINGS,
            ItemIds::LEATHER_LEGGINGS
        ],
        "boots" => [
            ItemIds::DIAMOND_BOOTS,
            ItemIds::IRON_LEGGINGS,
            ItemIds::GOLD_LEGGINGS,
            ItemIds::LEATHER_LEGGINGS
        ]
    ];

    public function __construct() {
        EnchantmentIdMap::getInstance()->register(-1, new Enchantment("", 0, 0, 0, 0));
        $this->enchantments = [
            50 => new SlowEnchant("Slow", Rarity::COMMON, ItemFlags::SWORD, 0x0, 5),
            51 => new SpringsEnchant("Springs", Rarity::COMMON, ItemFlags::FEET, 0x0, 2)
        ];
        foreach ($this->enchantments as $id => $enchant) {
            EnchantmentIdMap::getInstance()->register($id, $enchant);
        }

        $server = Server::getInstance();
        $server->getPluginManager()->registerEvents(new EnchantListener(), Main::getInstance());
        $server->getCommandMap()->registerAll("Factions", [
            new GiveEnchantmentBookCommand()
        ]);
    }

    /**
     * @param CoreEnchant $enchant
     * @param Item $item
     * @return bool
     */
    public function canBeAppliedTo(CoreEnchant $enchant, Item $item) : bool {
        if ($enchant->getPrimaryItemFlags() === ItemFlags::FEET) {
            if (in_array($item->getId(), self::IDS["boots"])) return true;
            return false;
        }
        if ($enchant->getPrimaryItemFlags() === ItemFlags::LEGS) {
            if (in_array($item->getId(), self::IDS["leggings"])) return true;
            return false;
        }
        if ($enchant->getPrimaryItemFlags() === ItemFlags::TORSO) {
            if (in_array($item->getId(), self::IDS["chestPlate"])) return true;
            return false;
        }
        if ($enchant->getPrimaryItemFlags() === ItemFlags::HEAD) {
            if (in_array($item->getId(), self::IDS["helmet"])) return true;
            return false;
        }
        if ($enchant->getPrimaryItemFlags() === ItemFlags::SWORD) {
            if ($item instanceof Sword) return true;
            return false;
        }
        if ($enchant->getPrimaryItemFlags() === ItemFlags::TOOL) {
            if ($item instanceof Tool) return true;
            return false;
        }
        return false;
    }

    /**
     * @param Item $item
     * @return Item
     */
    public function brandItem(Item $item) : Item {
        $this->canHoldAnotherEnchant($item);
        $lore = [];
        foreach ($item->getEnchantments() as $enchantment) {
            $lore[] = "§r§f" . $enchantment->getType()->getName() . "§7: §elv. " . $item->getEnchantmentLevel($enchantment->getType());
        }
        $coreEnchants = 0;
        foreach ($item->getEnchantments() as $enchant) {
            if ($enchant instanceof CoreEnchant) $coreEnchants++;
        }
        $lore[] = "";
        $lore[] = "§r§fSlots§7: §e" . $coreEnchants . "§7 / §e" . $item->getNamedTag()->getInt("maxSlots");
        $item->setLore($lore);
        return $item;
    }

    public function setItem(Item $item) : Item {
        if (is_null($item->getNamedTag()->getTag("maxSlots"))) {
            $cap = 0;
            if ($item instanceof Sword) $cap = self::DEFAULT_ENCHANTMENT_LIMITS["sword"];
            if ($item instanceof Tool) $cap = self::DEFAULT_ENCHANTMENT_LIMITS["tools"];
            if (in_array($item->getId(), self::IDS["helmet"])) $cap = self::DEFAULT_ENCHANTMENT_LIMITS["helmet"];
            if (in_array($item->getId(), self::IDS["chestPlate"])) $cap = self::DEFAULT_ENCHANTMENT_LIMITS["chestPlate"];
            if (in_array($item->getId(), self::IDS["leggings"])) $cap = self::DEFAULT_ENCHANTMENT_LIMITS["leggings"];
            if (in_array($item->getId(), self::IDS["boots"])) $cap = self::DEFAULT_ENCHANTMENT_LIMITS["boots"];
            $item->getNamedTag()->setInt("maxSlots", $cap);
            return $item;
        }
        return $item;
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function canHoldAnotherEnchant(Item &$item) : bool {
        $item = $this->setItem($item);
        $coreEnchants = 0;
        foreach ($item->getEnchantments() as $enchant) {
            if ($enchant instanceof CoreEnchant) $coreEnchants++;
        }
        $slots = $item->getNamedTag()->getInt("maxSlots");
        if ($coreEnchants >= $slots) return false;
        return true;
    }

    /**
     * @param int $id
     * @return Enchantment|null
     */
    public function getEnchantmentFromID(int $id) : ?Enchantment {
        if (!isset($this->enchantments[$id])) return null;
        return $this->enchantments[$id];
    }

    /**
     * @param string $name
     * @return Enchantment|null
     */
    public function getEnchantmentFromName(string $name) : ?Enchantment {
        foreach ($this->enchantments as $enchantment) {
            if ($enchantment->getName() !== $name) continue;
            return $enchantment;
        }
        return null;
    }

    /**
     * @param CoreEnchant $enchantment
     * @param int $level
     * @param int $chance
     * @return Item
     */
    public function makeBook(CoreEnchant $enchantment, int $level, int $chance = 0) : Item {
        if ($chance == 0) $chance = mt_rand(10, 80);
        $book = VanillaItems::BOOK();
        $book->setCustomName("§r§f" . $enchantment->getName() . " §eBook");
        $book->setLore([
            "§r§7Combine with any §e" . $enchantment->getFor(),
            "§r§7to apply the enchant!",
            "",
            "§r§7Enchantment: §f" . $enchantment->getName(),
            "§r§7Success odds: §f" . $chance . " §7/ §f100",
            "",
            "§r§7Level: §f" . $level
        ]);
        $book->getNamedTag()->setString("enchantmentBook", $enchantment->getName());
        $book->getNamedTag()->setInt("enchantmentBookLevel", $level);
        $book->getNamedTag()->setInt("bookChance", $chance);
        $book->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(-1)));
        return $book;
    }


}