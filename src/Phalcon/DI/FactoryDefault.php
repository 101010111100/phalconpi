<?php 

namespace Phalcon\DI {

	/**
	 * Phalcon\DI\FactoryDefault
	 *
	 * This is a variant of the standard Phalcon\DI. By default it automatically
	 * registers all the services provided by the framework. Thanks to this, the developer does not need
	 * to register each service individually providing a full stack framework
	 */
	
	class FactoryDefault extends \Phalcon\DI implements \Phalcon\DiInterface {

		/**
		 * \Phalcon\DI\FactoryDefault constructor
		 */
		public function __construct()
        {
            parent::__construct();

            $router = new \Phalcon\DI\Service('router', '\Phalcon\Mvc\Router', true);
            $dispatcher = new \Phalcon\DI\Service('dispatcher', '\Phalcon\Mvc\Dispatcher', true);
            $url = new \Phalcon\DI\Service('url', '\Phalcon\Mvc\Url', true);

            /**
             * Models manager for ORM
             */
            $modelsManager = new \Phalcon\DI\Service('modelsManager', '\Phalcon\Mvc\Model\Manager', true);

            /**
             * Models meta-data using the Memory adapter
             */
            $modelsMetadata = new \Phalcon\DI\Service('modelsMetadata', '\Phalcon\Mvc\Model\MetaData\Memory', true);

            /**
             * Request/Response are always shared
             */
            $response = new \Phalcon\DI\Service('response', '\Phalcon\Http\Response', true);
            $request = new \Phalcon\DI\Service('request', '\Phalcon\Http\Request', true);

            /**
             * Filter/Escaper services are always shared
             */
            $filter = new \Phalcon\DI\Service('filter', '\Phalcon\Filter', true);
            $escaper = new \Phalcon\DI\Service('escaper', '\Phalcon\Escaper', true);

            /**
             * Default annotations service
             */
            $annotations = new \Phalcon\DI\Service('annotations', '\Phalcon\Annotations\Adapter\Memory', true);

            /**
             * Security doesn't need to be shared, but anyways we register it as shared
             */
            $security = new \Phalcon\DI\Service('security', '\Phalcon\Security', true);

            /**
             * Flash services are always shared
             */
            $flash = new \Phalcon\DI\Service('flash', '\Phalcon\Flash\Direct', true);
            $flashSession = new \Phalcon\DI\Service('flashSession', '\Phalcon\Flash\Session', true);

            /**
             * Session is always shared
             */
            $session = new \Phalcon\DI\Service('session', '\Phalcon\Session\Adapter\Files', true);
            $sessionBag = new \Phalcon\DI\Service('sessionBag', '\Phalcon\Session\Bag', true);

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
            $this->_services['url'] = $url;
            $this->_services['modelsManager'] = $modelsManager;
            $this->_services['modelsMetadata'] = $modelsMetadata;
            $this->_services['response'] = $response;
            $this->_services['request'] = $request;
            $this->_services['filter'] = $filter;
            $this->_services['escaper'] = $escaper;
            $this->_services['security'] = $security;
            $this->_services['annotations'] = $annotations;
            $this->_services['flash'] = $flash;
            $this->_services['flashSession'] = $flashSession;
            $this->_services['session'] = $session;
            $this->_services['sessionBag'] = $sessionBag;
            $this->_services['eventsManager'] = $eventsManager;
            $this->_services['transactionManager'] = $transactionManager;
        }

	}
}
