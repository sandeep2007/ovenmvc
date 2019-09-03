<?php

class Model extends Illuminate\Database\Eloquent\Model
{ 
    public function __construct()
    {
        parent::__construct();
        new Base_model;
    }
}
