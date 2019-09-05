<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

abstract class Base_model extends Capsule
{

    public function __construct()
    {
        parent::__construct();
        $config =& getConfig();
        $this->addConnection([
            'driver'    => 'mysql',
            'host'      => $config['db']['hostname'],
            'database'  => $config['db']['database'],
            'username'  => $config['db']['username'],
            'password'  => $config['db']['password'],
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $this->setEventDispatcher(new Dispatcher(new Container));
        $this->setAsGlobal();
		$this->bootEloquent();
    }
}