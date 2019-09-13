<?php
class Welcome extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->data['app_name'] = 'OvenMVC';
        $this->view->render('welcome', $this->data);
    }
}
