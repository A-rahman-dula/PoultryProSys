<?php
interface crud
{
    public function create($user_id);
    public function read($user_id);
    public function update( $id);
    public function delete( $id);
    public function readOne($id);
}
