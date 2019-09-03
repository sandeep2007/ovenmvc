<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class Base_model extends Capsule
{

    public function __construct()
    {
        parent::__construct();

        $this->addConnection([
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'posmenu',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $this->setEventDispatcher(new Dispatcher(new Container));
        $this->setAsGlobal();
		$this->bootEloquent();
    }
}