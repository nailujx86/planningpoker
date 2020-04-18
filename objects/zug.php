<?php
class Zug {
    private $runde;
    private $user;
    private $karte;

    public function __construct(Runde $runde, User $user, string $karte = null) {
        $this->runde = $runde;
        $this->user = $user;
        $this->karte = $karte;
    }

    /**
     * Get the value of runde
     */ 
    public function getRunde()
    {
        return $this->runde;
    }

    /**
     * Get the value of user
     */ 
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get the value of karte
     */ 
    public function getKarte()
    {
        return $this->karte;
    }
}

?>