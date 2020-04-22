<?php
require_once("rundeobj.php");
class Spiel
{
    private $id;
    private $task;
    private $beschreibung;
    private $karten;
    private $erstellt;
    private $admin;

    public function __construct(string $task, string $beschreibung, User $admin, array $karten = ["0,", "1", "2", "3", "4", "5", "6", "7", "8", "9"], string $time = null, int $id = -1)
    {
        if ($time == null) {
            $time = date("Y-m-d H:i:s");
        }
        $this->erstellt = $time;
        $this->task = $task;
        $this->beschreibung = $beschreibung;
        $this->karten = $karten;
        $this->id = $id;
        $this->admin = $admin;
    }

    /**
     * Get the value of task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Get the value of beschreibung
     */
    public function getBeschreibung()
    {
        return $this->beschreibung;
    }

    /**
     * Get the value of karten
     */
    public function getKarten()
    {
        return $this->karten;
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
     * Get the value of admin
     */ 
    public function getAdmin()
    {
        return $this->admin;
    }
}
?>