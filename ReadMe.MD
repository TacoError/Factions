# Factions Core

Factions core made for CenturyMC by TacoError.

# Permissions

| Permission  | Usage                                 |
|-------------|---------------------------------------|
| core.groups | Add or remove groups from players.    |
|core.permissions| Edit a players individual permissions |
|core.kits| Create, delete, and edit kits.        |
|core.money| Give and take money from a player     |
|core.crates|Permission to give crate keys|

# Groups

Groups are created in config.yml, every group has a regular name, 
a fancy name, its authority, and its corresponding permissions.
The groups fancy name is what will be shown above the players
name and in chat. The permissions are what permissions the player
will receive when they are set into the group. 

Players can be in more than one group, they will inherit permissions
from every group they are in. The tag used above their name and chat
will come from the group that has highest authority.

# Kits

Kits are simple, you create one by using the command
/createkit, this command has three arguments,
/createkit (name) (coolDown IN SECONDS) (permission). After running the command
you will be prompted with a double chest, any item you put in the chest
will be given to whoever claims the kit, once you are done
adding items, simply close the inventory. If no permission is provided 
when doing the command, everyone will be able to claim the kit.
When the player claims the kit, every item will have a check seeing
if the inventory can hold the item, if they can it will be added to their
inventory, otherwise it will be dropped on the ground at their position.

When you edit a kit, it is very simple (/editkit (name)). The inventory you saw the first
time you made the kit will open, containing all the items you put inside the first time.
Once you are finished editing the inventory, just close it and it will
save.

Deleting a kit is pretty self-explanitory, simple use /deletekit (name). This can be used whenever.
Also, when creating a kit, if you don't want the kit to have a permission just leave that argument empty.

# Storage

All factions, playerData, kits, and etc are stored in YAML files. 
These files will be accessed via async, except for factions. Factions
are loaded on enable, and saves on disable. They also save
every 10 minutes async to prevent any need for rollbacks. Players
are loaded when they login, and saved when they leave. I did not see
a need to use MySQL or SQLite as Minecraft Bedrock Edition servers
do not scale anywhere near enough for it to be worth the time spent
doing said thing.

# Crates

Crates a simple, there are commands such as /givekey, and /keyall.
Crates are to be configured in the config.yml file and rewards
will be configured with a chance to get the reward. Rewards will be given via
commands only. The "name" is the name that will be types to keyall
and give keys to the players. the "fancyName" is the name that
will be above the crate in game, and on the keys.

The animation for crates should be kept simple so that players can
open their crates quickly, and it looks smooth. There will be SOME particles
and the crate will open for 2 seconds. Keys will be able to get opened
immediately.

# Configuration

In the event the server shuts down saying "UPDATE CONFIG" do not panic. Simply goto the
"resources" folder in this repository, and see what is added in there opposed to the one you have, 
and add it to your config.yml (after doing so, update cfg-version to the next version). This is simply so that the server does not break in the future due to a
older config version.

# Requirements

- InvMenu ([Link](https://github.com/Muqsit/InvMenu))
- FormAPI ([Link](https://github.com/jojoe77777/FormAPI))
- WFT ([Link](https://github.com/WolfDen133/WFT))

