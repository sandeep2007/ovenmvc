<?php
class Test_model extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'users';
        
    }
}
