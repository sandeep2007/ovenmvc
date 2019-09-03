<?php
class Test_model2 extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'users';
    }
}
