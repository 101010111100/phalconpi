<?php 

namespace Phalcon {

	/**
	 * Phalcon\DI
	 *
	 * Phalcon\DI is a component that implements Dependency Injection of services and
	 * it's itself a container for them.
	 *
	 * Since Phalcon is highly decoupled, Phalcon\DI is essential to integrate the different
	 * components of the framework. The developer can also use this component to inject dependencies
	 * and manage global instances of the different classes used in the application.
	 *
	 * Basically, this component implements the `Inversion of Control` pattern. Applying this,
	 * the objects do not receive their dependencies using setters or constructors, but requesting
	 * a service dependency injector. This reduces the overall complexity, since there is only one
	 * way to get the required dependencies within a component.
	 *
	 * Additionally, this pattern increases testability in the code, thus making it less prone to errors.
	 *
	 *<code>
	 * $di = new Phalcon\DI();
	 *
	 * //Using a string definition
	 * $di->set('request', 'Phalcon\Http\Request', true);
	 *
	 * //Using an anonymous function
	 * $di->set('request', function(){
	 *	  return new Phalcon\Http\Request();
	 * }, true);
	 *
	 * $request = $di->getRequest();
	 *
	 *</code>
	 */
	
	class DI implements \Phalcon\DiInterface {

		protected $_services;

		protected $_sharedInstances;

		protected $_freshInstance;

		protected static $_default;

		/**
		 * \Phalcon\DI constructor
		 *
		 */
		public function __construct()
        {
            if (!self::$_default) {
                self::$_default = $this;
            }
        }


		/**
		 * Registers a service in the services container
		 *
		 * @param string $name
		 * @param mixed $definition
		 * @param boolean $shared
         *
		 * @return \Phalcon\DI\ServiceInterface
		 */
		public function set($name, $definition, $shared = null)
        {
            if (!$shared) {
                $shared = false;
            }

            if (!is_string($name)) {
                throw new \Phalcon\DI\Exception('The service name must be a string');
            }

            $service = new \Phalcon\DI\Service($name, $definition, $shared);

            $this->_services[$name] = $service;

            return $service;
        }


		/**
		 * Registers an "always shared" service in the services container
		 *
		 * @param string $name
		 * @param mixed $definition
         *
		 * @return \Phalcon\DI\ServiceInterface
		 */
		public function setShared($name, $definition)
        {
            return $this->set($name, $definition, true);
        }


		/**
		 * Removes a service in the services container
		 *
		 * @param string $name
		 */
		public function remove($name)
        {
            if (!is_string($name)) {
                throw new \Phalcon\DI\Exception('The service name must be a string');
            }

            unset($this->_services[$name]);
        }


		/**
		 * Attempts to register a service in the services container
		 * Only is successful if a service hasn't been registered previously
		 * with the same name
		 *
		 * @param string $name
		 * @param mixed $definition
		 * @param boolean $shared
         *
		 * @return \Phalcon\DI\ServiceInterface
		 */
		public function attempt($name, $definition, $shared = null)
        {
            if (!is_string($name)) {
                throw new \Phalcon\DI\Exception('The service name must be a string');
            }

            if (!array_key_exists($name, $this->_services)) {
                return $this->set($name, $definition, $shared);
            }
        }


		/**
		 * Sets a service using a raw \Phalcon\DI\Service definition
		 *
		 * @param string $name
		 * @param \Phalcon\DI\ServiceInterface $rawDefinition
         *
		 * @return \Phalcon\DI\ServiceInterface
		 */
		public function setRaw($name, $rawDefinition)
        {
            if (!is_string($name)) {
                throw new \Phalcon\DI\Exception('The service name must be a string');
            }

            if (!is_object($rawDefinition)) {
                throw new \Phalcon\DI\Exception('The service definition must be an object');
            }

            $this->_services[$name] = $rawDefinition;

            return $rawDefinition;
        }


		/**
		 * Returns a service definition without resolving
		 *
		 * @param string $name
         *
		 * @return mixed
		 */
		public function getRaw($name)
        {
            if (!is_string($name)) {
                throw new \Phalcon\DI\Exception('The service name must be a string');
            }

            if (array_key_exists($name, $this->_services)) {
                $service = $this->_services[$name];

                $definition = $service->getDefinition();

                return $definition;
            }

            throw new \Phalcon\DI\Exception('Service "' . $name . '" wasn\'t found in the dependency injection container');
        }


		/**
		 * Returns a \Phalcon\DI\Service instance
		 *
		 * @param string $name
         *
		 * @return \Phalcon\DI\ServiceInterface
		 */
		public function getService($name)
        {
            if (!is_string($name)) {
                throw new \Phalcon\DI\Exception('The service name must be a string');
            }

            if (array_key_exists($name, $this->_services)) {
                return $this->_services[$name];
            }

            throw new \Phalcon\DI\Exception('Service "' . $name . '" wasn\'t found in the dependency injection container');
        }


		/**
		 * Resolves the service based on its configuration
		 *
		 * @param string $name
		 * @param array $parameters
         *
		 * @return mixed
		 */
		public function get($name, $parameters = null)
        {
            if (!is_string($name)) {
                throw new \Phalcon\DI\Exception('The service name must be a string');
            }

            if (array_key_exists($name, $this->_services)) {
                /**
                 * The service is registered in the DI
                 */
                $service = $this->_services[$name];
                $instance = $service->resolve($parameters);
            } else {
                /**
                 * The DI also acts as builder for any class even if it isn't defined in the DI
                 */
                if (class_exists($name)) {
                    $instance = new $name($parameters);
                } else {
                    throw new \Phalcon\DI\Exception('Service "' . $name . '" wasn\'t found in the dependency injection container');
                }
            }

            if (is_object($instance)) {
                $instance->setDI($instance);
            }

            return $instance;
        }


		/**
		 * Resolves a service, the resolved service is stored in the DI, subsequent requests for this service will return the same instance
		 *
		 * @param string $name
		 * @param array $parameters
         *
		 * @return mixed
		 */
		public function getShared($name, $parameters = null)
        {
            if (!is_string($name)) {
                throw new \Phalcon\DI\Exception('The service name must be a string');
            }

            /**
             * This method provides a first level to shared instances allowing to use
             * non-shared services as shared
             */
            if (array_key_exists($name, $this->_sharedInstances)) {
                $instance = $this->_sharedInstances[$name];

                $this->_freshInstance = false;
            } else {
                /**
                 * Resolve the instance normally
                 */
                $instance = $this->get($name, $parameters);

                /**
                 * Save the instance in the first level shared
                 */
                $this->_sharedInstances[$name] = $instance;
                $this->_freshInstance = true;
            }

            return $instance;
        }


		/**
		 * Check whether the DI contains a service by a name
		 *
		 * @param string $name
         *
		 * @return boolean
		 */
		public function has($name)
        {
            if (!is_string($name)) {
                throw new \Phalcon\DI\Exception('The service name must be a string');
            }

            return array_key_exists($name, $this->_services);
        }


		/**
		 * Check whether the last service obtained via getShared produced a fresh instance or an existing one
		 *
		 * @return boolean
		 */
		public function wasFreshInstance()
        {
            return $this->_freshInstance;
        }


		/**
		 * Return the services registered in the DI
		 *
		 * @return \Phalcon\DI\Service[]
		 */
		public function getServices()
        {
            return $this->_services;
        }


		/**
		 * Check if a service is registered using the array syntax
		 *
		 * @param string $alias
         *
		 * @return boolean
		 */
		public function offsetExists($alias)
        {
            return $this->has($alias);
        }


		/**
		 * Allows to register a shared service using the array syntax
		 *
		 *<code>
		 *	$di['request'] = new \Phalcon\Http\Request();
		 *</code>
		 *
		 * @param string $alias
		 * @param mixed $definition
		 */
		public function offsetSet($alias, $definition)
        {
            $this->setShared($alias, $definition);
        }


		/**
		 * Allows to obtain a shared service using the array syntax
		 *
		 *<code>
		 *	var_dump($di['request']);
		 *</code>
		 *
		 * @param string $alias
         *
		 * @return mixed
		 */
		public function offsetGet($alias)
        {
            return $this->getShared($alias);
        }


		/**
		 * Removes a service from the services container using the array syntax
		 *
		 * @param string $alias
		 */
		public function offsetUnset($alias)
        {
            $this->remove($alias);
        }


		/**
		 * Magic method to get or set services using setters/getters
		 *
		 * @param string $method
		 * @param array $arguments
         *
		 * @return mixed
		 */
		public function __call($method, $arguments = null)
        {
            /**
             * If the magic method starts with 'get' we try to get a service with that name
             */
            if ('get' === substr($method, 0, 3)) {
                $name = substr($method, 3);
                $name = lcfirst($name);

                if (array_key_exists($name, $this->_services)) {
                    return $this->get($name, $arguments);
                }
            }

            /**
             * If the magic method starts with 'set' we try to set a service using that name
             */
            if ('set' === substr($method, 0, 3)) {
                $name = substr($method, 3);
                $name = lcfirst($name);

                $handler = $arguments[0];

                $this->set($name, $handler);

                return null;
            }

            throw new \Phalcon\DI\Exception('Call to undefined method or service "' . $method . '"');
        }


		/**
		 * Set a default dependency injection container to be obtained into static methods
		 *
		 * @param \Phalcon\DiInterface $dependencyInjector
		 */
		public static function setDefault($dependencyInjector)
        {
            self::$_default = $dependencyInjector;
        }


		/**
		 * Return the lastest DI created
		 *
		 * @return \Phalcon\DiInterface
		 */
		public static function getDefault()
        {
            return self::$_default;
        }


		/**
		 * Resets the internal default DI
		 */
		public static function reset()
        {
            self::$_default = null;
        }

	}
}
