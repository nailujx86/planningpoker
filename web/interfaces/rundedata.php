<?php
interface RundeDataInterface {
    public function addRunde(Runde $runde): ?int;
    public function getRunden(Spiel $spiel): ?array;
    public function updateRundeAbgeschlossen(Runde $runde): ?bool;
}
?>