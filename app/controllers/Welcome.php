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
        
        $db = new DB();
        //$users = $db->table('pr_merchant')->select(['company_name', 'email'])->where('id', '23')->limit(100)->get();

        $users = DB::select("select company_name from pr_merchant order by id DESC limit 1");

        //new Base_model();

        //echo '<pre>';
       // print_r(Test_model::limit(2)->get());
        
        //print_r($obj->get());
        //$app =& getInstance();


        // $app->model->table = 'pr_merchant';
        //  $merchant = $app->model->get(['limit' => 50])->data();

        // $app->model->table = 'pr_countries';
        // $app->model->primary_key = 'country_id';
        // $banner = $app->model->get(['limit' => 3])->data();
        // echo '<pre>';
        // print_r($merchant);
         print_r($users);

    }

 
}
