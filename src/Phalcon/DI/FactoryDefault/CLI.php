<?php 

namespace Phalcon\DI\FactoryDefault {

	/**
	 * Phalcon\DI\FactoryDefault\CLI
	 *
	 * This is a variant of the standard Phalcon\DI. By default it automatically
	 * registers all the services provided by the framework.
	 * Thanks to this, the developer does not need to register each service individually.
	 * This class is specially suitable for CLI applications
	 */
	
	class CLI extends \Phalcon\DI\FactoryDefault implements \Phalcon\DiInterface {

		/**
		 * \Phalcon\DI\FactoryDefault\CLI constructor
		 */
		public function __construct()
        {
            parent::__construct();

            $router = new \Phalcon\DI\Service('router', '\Phalcon\CLI\Router');
            $dispatcher = new \Phalcon\DI\Service('dispatcher', '\Phalcon\CLI\Dispatcher');

            /**
             * Models manager for ORM
             */
            $modelsManager = new \Phalcon\DI\Service('modelsManager', '\Phalcon\Mvc\Model\Manager');

            /**
             * Models meta-data using the Memory adapter
             */
            $modelsMetadata = new \Phalcon\DI\Service('modelsMetadata', '\Phalcon\Mvc\Model\MetaData\Memory');

            /**
             * Filter/Escaper services are always shared
             */
            $filter = new \Phalcon\DI\Service('filter', '\Phalcon\Filter', true);
            $escaper = new \Phalcon\DI\Service('escaper', '\Phalcon\Escaper', true);

            /**
             * Flash services are always shared
             */
            $flash = new \Phalcon\DI\Service('flash', '\Phalcon\Flash\Direct', true);
            $flashSession = new \Phalcon\DI\Service('flashSession', '\Phalcon\Flash\Session', true);

            /**
             * Events Manager is always shared
             */
            $eventsManager = new \Phalcon\DI\Service('eventsManager', '\Phalcon\Events\Manager', true);
            $transactionManager = new \Phalcon\DI\Service('transactionManager', '\Phalcon\Mvc\Model\Transaction\Manager', true);

            /**
             * Register services
             */
            $this->_services = array();
            $this->_services['router'] = $router;
            $this->_services['dispatcher'] = $dispatcher;
            $this->_services['modelsManager'] = $modelsManager;
            $this->_services['modelsMetadata'] = $modelsMetadata;
            $this->_services['filter'] = $filter;
            $this->_services['escaper'] = $escaper;
            $this->_services['flash'] = $flash;
            $this->_services['flashSession'] = $flashSession;
            $this->_services['eventsManager'] = $eventsManager;
            $this->_services['transactionManager'] = $transactionManager;
        }

	}
}
