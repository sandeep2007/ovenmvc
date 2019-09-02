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
        // $app = getInstance();
        // $app->model->table = 'pr_merchant';
        // $merchant = $app->model->get(['limit' => 50])->data();

        // $app->model->table = 'pr_countries';
        // $app->model->primary_key = 'country_id';
        // $banner = $app->model->get(['limit' => 3])->data();
        // echo '<pre>';
        // print_r($merchant);
        // print_r($banner);
        load_script(NULL, ['abc']);
    }
}
