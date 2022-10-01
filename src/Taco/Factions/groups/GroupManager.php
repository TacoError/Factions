<?php namespace Taco\Factions\groups;

use pocketmine\Server;
use Taco\Factions\groups\commands\AddGroupCommand;
use Taco\Factions\groups\commands\RemoveGroupCommand;

class GroupManager {

    /** @var array<string, Group> */
    private array $groups;

    public function __construct(array $groups) {
        foreach ($groups as $name => $data) {
            $this->groups[$name] = new Group(
                $name,
                $data["fancyName"],
                $data["permissions"],
                $data["authority"]
            );
        }

        Server::getInstance()->getCommandMap()->registerAll("Factions", [
            new AddGroupCommand(),
            new RemoveGroupCommand()
        ]);
    }

    /**
     * Returns the group with the highest authority
     * in the provided array
     *
     * @param array<Group> $groups
     * @return Group|null
     */
    public function getHighestGroupAuthority(array $groups) : ?Group {
        if (count($groups) < 1) return null;
        $groups = array_combine(
            array_map(fn($group) => $group->getName(), $groups),
            array_map(fn($group) => $group->getAuthority(), $groups)
        );
        arsort($groups);
        return $this->getGroupFromName(array_keys($groups)[0]);
    }

    /**
     * Returns a group from its name
     *
     * @param string $name
     * @return Group|null
     */
    public function getGroupFromName(string $name) : ?Group {
        if (isset($this->groups[$name])) return $this->groups[$name];
        return null;
    }

    /*** @return Group */
    public function getDefaultGroup() : Group {
        return array_values($this->groups)[0];
    }

}