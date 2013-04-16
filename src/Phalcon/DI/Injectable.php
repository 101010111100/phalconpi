<?php 

namespace Phalcon\DI {

	/**
	 * Phalcon\DI\Injectable
	 *
	 * This class allows to access services in the services container by just only accessing a public property
	 * with the same name of a registered service
	 */
	
	abstract class Injectable implements \Phalcon\DI\InjectionAwareInterface, \Phalcon\Events\EventsAwareInterface {

		protected $_dependencyInjector;

		protected $_eventsManager;

		/**
 		 * @var \Phalcon\Mvc\ViewInterface
 		 */
		public $view;

		/**
		 * @var \Phalcon\Mvc\RouterInterface
	 	 */
		public $router;

		/**
		 * @var \Phalcon\Mvc\DispatcherInterface
	 	 */
		public $dispatcher;

		/**
		 * @var \Phalcon\Mvc\UrlInterface
	 	 */
		public $url;

		/**
		 * @var \Phalcon\DiInterface
	 	 */
		public $di;

		/**
		 * @var \Phalcon\HTTP\RequestInterface
	 	 */
		public $request;

		/**
		 * @var \Phalcon\HTTP\ResponseInterface
	 	 */
		public $response;

		/**
		 * @var \Phalcon\Flash\Direct
	 	 */
		public $flash;

		/**
		 * @var \Phalcon\Flash\Session
	 	 */
		public $flashSession;

		/**
		 * @var \Phalcon\Session\AdapterInterface
	 	 */
		public $session;

		/**
		 * @var \Phalcon\Session\Bag
	 	 */
		public $persistent;

		/**
		 * @var \Phalcon\Mvc\Model\ManagerInterface
	 	 */
		public $modelsManager;

		/**
		 * @var \Phalcon\Mvc\Model\MetadataInterface
	 	 */
		public $modelsMetadata;

		/**
		 * @var \Phalcon\Mvc\Model\Transaction\Manager
	 	 */
		public $transactionManager;

		/**
		 * @var \Phalcon\FilterInterface
	 	 */
		public $filter;

		/**
		 * @var \Phalcon\Security
	 	 */
		public $security;

        /**
         * @var \Phalcon\Annotations\Adapter\Memory
         */
        public $annotations;
		
		/**
		 * Sets the dependency injector
		 *
		 * @param \Phalcon\DiInterface $dependencyInjector
		 */
		public function setDI($dependencyInjector)
        {
            if (!is_object($dependencyInjector)) {
                throw new \Phalcon\DI\Exception('Dependency Injector is invalid');
            }

            $this->__dependencyInjector = $dependencyInjector;
        }


		/**
		 * Returns the internal dependency injector
		 *
		 * @return \Phalcon\DiInterface
		 */
		public function getDI()
        {
            return $this->_dependencyInjector;
        }


		/**
		 * Sets the event manager
		 *
		 * @param \Phalcon\Events\ManagerInterface $eventsManager
		 */
		public function setEventsManager($eventsManager)
        {
            if (!is_object($eventsManager)) {
                throw new \Phalcon\DI\Exception('Events manager is invalid');
            }

            $this->_eventsManager = $eventsManager;
        }


		/**
		 * Returns the internal event manager
		 *
		 * @return \Phalcon\Events\ManagerInterface
		 */
		public function getEventsManager()
        {
            return $this->_eventsManager;
        }


		/**
		 * Magic method __get
		 *
		 * @param string $propertyName
		 */
		public function __get($propertyName)
        {
            $dependencyInjector = $this->_dependencyInjector;

            if (!is_object($dependencyInjector)) {
                $dependencyInjector = \Phalcon\DI::getDefault();

                if (!is_object($dependencyInjector)) {
                    throw new \Phalcon\DI\Exception('A dependency injection object is required to access the application services');
                }
            }

            /**
             * This class injects a public property with a resolved service
             */
            if ($dependencyInjector->has($propertyName)) {
                $service = $dependencyInjector->getShared($propertyName);

                $this->$propertyName = $service;

                return $service;
            }

            if ('di' == $propertyName) {
                $this->di = $dependencyInjector;

                return $dependencyInjector;
            }

            /**
             * Accessing the persistent property will create a session bag in any class
             */
            if ('persistent' == $propertyName) {
                $className = get_class($this);
                $arguments = array($className);

                $persistent = $dependencyInjector->get('sessionBag', $arguments);

                $this->persistent = $persistent;

                return $persistent;
            }

            /**
             * A notice is shown if the property is not defined and isn't a valid service
             */
            trigger_error('Access to undefined property "' . $propertyName . '"');
        }

	}
}
