<?php

class Welcome extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->sessionInit();
    }

    public function index()
    {
        $this->view->render('welcome');
        $model = new Test_model();
        //$model->table = 'users';
        $data = $model->get(['limit' => 1,'select' => ['id','email']]);
        print_r($data->data());
        print_r($data->error());

        $model = new Test_model2();
       // $model->table = 'users';
        $data = $model->get(['limit' => 2,'select' => ['id']]);
        print_r($data->data());
        print_r($data->error());
    }

 
}
