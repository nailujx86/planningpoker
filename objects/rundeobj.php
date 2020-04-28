<?php
require_once("spielobj.php");
require_once("userobj.php");
class Runde
{
    private $id;
    private $spiel;
    private $abgeschlossen;
    private $erstellt;
    private $zuege;

    /**
     * Konstruktor für das Rundenobjekt. Legt standardmäßig den Zeit-Parameter auf das jetzige Datum im SQL DateTime Format.
     */
    public function __construct(Spiel $spiel, bool $abgeschlossen = false, string $time = null, int $id = -1)
    {
        if ($time == null) {
            $time = date("Y-m-d H:i:s");
        }
        $this->erstellt = $time;
        $this->spiel = $spiel;
        $this->abgeschlossen = $abgeschlossen;
        $this->id = $id;
        $this->zuege = [];
    }

    /**
     * Fügt einen Zug der Zugliste hinzu, wenn der Nutzer der dem Zug zugeordnet ist noch keinen Zug in diesem Rundenobjekt "getätigt" hat.
     * @return bool hinzugefügt
     */
    public function addZug(Zug $zug): bool
    {
        $isTeilnehmer = false;
        foreach ($this->zuege as $zugObj) {
            if ($zugObj->getUser()->getUsername() == $zug->getUser()->getUsername()) {
                $isTeilnehmer = true;
                break;
            }
        }
        if ($isTeilnehmer) {
            return false;
        }
        $this->zuege[] = $zug;
        return true;
    }

    /**
     * Ersetzt einen Zug
     */
    public function setZug(Zug $zug) {
        for($i = 0; $i < count($this->zuege); $i++) {
            if($zug->getUser()->getUsername() == $this->zuege[$i]->getUser()->getUsername()) {
                $this->zuege[$i] = $zug;
            }
        }
    }

    /**
     * Setzt ein Rundenobjekt auf abgeschlossen
     */
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
        $teilnehmer = [];
        foreach($this->zuege as $zug) {
            $teilnehmer[] = $zug->getUser();
        }
        return $teilnehmer;
    }
}
?>
