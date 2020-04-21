<?php
class User
{
    private $username;
    private $passwhash;
    private $mail;
    private $erstellt;

    public function __construct(string $username, string $passw, string $mail, string $time = null)
    {
        if ($time == null) {
            $time = date("Y-m-d H:i:s");
        }
        $this->username = $username;
        $hashedpw = "";
        $pwinfo = password_get_info($passw);
        if ($pwinfo['algo'] == 0) {
            $hashedpw = password_hash($passw, PASSWORD_DEFAULT);
        } else {
            $hashedpw = $passw;
        }
        $this->passwhash = $hashedpw;
        $this->mail = $mail;
        $this->erstellt = $time;
    }

    /**
     * Verifiziert ein Passwort
     * 
     * @return bool verifiziert
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->passwhash);
    }

    /**
     * Get the value of mail
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set the value of mail
     *
     * @return  self
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get the value of username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get the value of erstellt
     */
    public function getErstellt()
    {
        return $this->erstellt;
    }

    /**
     * Get the value of passwhash
     */
    public function getPasswhash()
    {
        return $this->passwhash;
    }
}
