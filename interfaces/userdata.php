<?php
interface UserDataInterface {
    public function addUser(User $user) : ?int;
    public function getUser(int $id = null, string $name = null): ?User;
}
?>