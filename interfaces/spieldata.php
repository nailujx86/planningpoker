<?php
interface SpielDataInterface {
    public function addSpiel(Spiel $spiel): ?int;
    public function getSpiel(int $id): ?Spiel;
    public function getSpiele(User $user): ?array;
}
?>