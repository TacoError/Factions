<?php namespace Taco\Factions\factions\objects;

use pocketmine\player\Player;

class FactionBank {

    /** @var int */
    private int $balance;

    public function __construct(int $balance) {
        $this->balance = $balance;
    }

    /*** @return int */
    public function getBalance() : int {
        return $this->balance;
    }

    /**
     * Moving money from faction bank to player account
     *
     * @param Player $player
     * @param int $amount
     * @return void
     */
    public function takeBalancePlayer(Player $player, int $amount) : void {
        //TODO
    }

    /**
     * Moving money from player balance to faction bank
     *
     * @param Player $from
     * @param int $amount
     * @return void
     */
    public function addBalance(Player $from, int $amount) : void {
        //TODO
    }

}