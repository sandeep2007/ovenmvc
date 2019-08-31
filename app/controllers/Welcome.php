<?php

class Welcome extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->sessionInit();
       
        $this->sub_directory = 'admin';
    }

    public function index()
    {
        echo 'home';
        ?>
        <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> -->
        <?php
        //$this->view->render('welcome');
        // $app = getInstance();
        // $app->model->table = 'pr_merchant';
        // $merchant = $app->model->get(['limit' => 50])->data();

        // $app->model->table = 'pr_countries';
        // $app->model->primary_key = 'country_id';
        // $banner = $app->model->get(['limit' => 3])->data();
        // echo '<pre>';
        // print_r($merchant);
        // print_r($banner);

        
        load_script(NULL,NULL,NULL,['abc']);
    }
}
