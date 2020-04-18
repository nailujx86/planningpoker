<?php
interface ZugDataInterface {
    public function addZug(Zug $zug);
    public function updateZug(Zug $zug): bool;
}