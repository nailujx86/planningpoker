<?php
require_once("spielobj.php");
require_once("userobj.php");
class Runde
{
    private $id;
    private $teilnehmer;
    private $spiel;
    private $abgeschlossen;
    private $erstellt;
    private $zuege;

    public function __construct(Spiel $spiel, bool $abgeschlossen = false, string $time = null, int $id = -1)
    {
        if ($time == null) {
            $time = date("Y-m-d H:i:s");
        }
        $this->erstellt = $time;
        $this->spiel = $spiel;
        $this->abgeschlossen = $abgeschlossen;
        $this->id = $id;
        $this->teilnehmer = [];
        $this->zuege = [];
    }

    public function addUser(User $user)
    {
        $teilnehmerExistiert = false;
        foreach ($this->teilnehmer as $teilnehmerObj) {
            if ($teilnehmerObj->getUsername() == $user->getUsername()) {
                $teilnehmerExistiert = true;
            }
        }
        if (!$teilnehmerExistiert) {
            $this->teilnehmer[] = $user;
        }
    }

    public function addZug(Zug $zug): bool
    {
        $isTeilnehmer = false;
        foreach ($this->teilnehmer as $teilnehmerObj) {
            if ($teilnehmerObj->getUsername() == $zug->getUser()->getUsername()) {
                $isTeilnehmer = true;
                break;
            }
        }
        if (!$isTeilnehmer) {
            return false;
        }
        $hasAbgestimmt = false;
        foreach ($this->zuege as $zugObj) {
            if ($zugObj->getUser()->getUsername() == $zug->getUser()->getUsername()) {
                $hasAbgestimmt = true;
                break;
            }
        }
        if ($hasAbgestimmt) {
            return false;
        }
        $this->zuege[] = $zug;
        return true;
    }

    public function setAbgeschlossen(bool $abgeschlossen)
    {
        $this->abgeschlossen = $abgeschlossen;
    }

    /**
     * Get the value of spiel
     */
    public function getSpiel()
    {
        return $this->spiel;
    }

    /**
     * Get the value of abgeschlossen
     */
    public function getAbgeschlossen()
    {
        return $this->abgeschlossen;
    }

    /**
     * Get the value of erstellt
     */
    public function getErstellt()
    {
        return $this->erstellt;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of zuege
     */
    public function getZuege()
    {
        return $this->zuege;
    }

    /**
     * Get the value of teilnehmer
     */ 
    public function getTeilnehmer()
    {
        return $this->teilnehmer;
    }
}
?>
