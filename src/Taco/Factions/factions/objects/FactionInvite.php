<?php namespace Taco\Factions\factions\objects;

use pocketmine\player\Player;

class FactionInvite {

    /**
     * Time the invite was sent as a time()
     * @var int
     */
    private int $sent;

    /**
     * Player the invite was sent to
     * @var string
     */
    private string $who;

    /**
     * Player who sent the invite
     * @var string
     */
    private string $from;

    public function __construct(string $from, string $to, int $sent) {
        $this->from = $from;
        $this->who = $to;
        $this->sent = $sent;
    }

    /*** @return string */
    public function getWho() : string {
        return $this->who;
    }

    /*** @return string */
    public function getFrom() : string {
        return $this->from;
    }

    /*** @return int */
    public function getTimeSinceSent() : int {
        return time() - $this->sent;
    }

}