<?php 

namespace Phalcon\DI {
    use Phalcon\DI\FactoryDefault\CLI;

    /**
	 * Phalcon\DI\Service
	 *
	 * Represents individually a service in the services container
	 *
	 *<code>
	 * $service = new Phalcon\DI\Service('request', 'Phalcon\Http\Request');
	 * $request = $service->resolve();
	 *<code>
	 *
	 */
	
	class Service implements \Phalcon\DI\ServiceInterface {

		protected $_name;

		protected $_definition;

		protected $_shared;

		protected $_sharedInstance;

		/**
		 * \Phalcon\DI\Service
		 *
		 * @param string $name
		 * @param mixed $definition
		 * @param boolean $shared
		 */
		public function __construct($name, $definition, $shared = null)
        {
            $this->_name = $name;
            $this->_definition = $definition;
            $this->_shared = $shared;
        }


		/**
		 * Returns the service's name
		 *
		 * @param string
		 */
		public function getName()
        {
            return $this->_name;
        }


		/**
		 * Sets if the service is shared or not
		 *
		 * @param boolean $shared
		 */
		public function setShared($shared)
        {
            $this->_shared = $shared;
        }


		/**
		 * Check whether the service is shared or not
		 *
		 * @return boolean
		 */
		public function isShared()
        {
            return $this->_shared;
        }


		/**
		 * Sets/Resets the shared instance related to the service
		 *
		 * @param mixed $sharedInstance
		 */
		public function setSharedInstance($sharedInstance)
        {
            $this->_sharedInstance = $sharedInstance;
        }


		/**
		 * Set the service definition
		 *
		 * @param mixed $definition
		 */
		public function setDefinition($definition)
        {
            $this->_definition = $definition;
        }


		/**
		 * Returns the service definition
		 *
		 * @return mixed
		 */
		public function getDefinition()
        {
            return $this->_definition;
        }


		/**
		 * Resolves the service
		 *
		 * @param array $parameters
		 * @param \Phalcon\DiInterface $dependencyInjector
         *
		 * @return mixed
		 */
		public function resolve($parameters = null, $dependencyInjector = null)
        {
            if ($this->_shared && $this->_sharedInstance) {
                return $this->_sharedInstance;
            }

            $parameters = (array) $parameters;
            $instance = '';
            $found = true;

            if (is_string($this->_definition)) {
                if (class_exists($this->_definition)) {
                    $className = $this->_definition;
                    $instance = new $className($parameters);
                } else {
                    $found = false;
                }
            } else {
                if (is_object($this->_definition)) {
                    /**
                     * Object definitions can be a Closure or an already resolved instance
                     */
                    if ($this->_definition instanceof \Closure) {
                        $instance = call_user_func_array($this->_definition, $parameters);
                    } else {
                        $instance = $this->_definition;
                    }
                } else {
                    /**
                     * Array definitions require a 'className' parameter
                     */
                    if (is_array($this->_definition)) {
                        $builder = new \Phalcon\DI\Service\Builder;
                        $builder->build($dependencyInjector, $this->_definition, $parameters);
                    } else {
                        $found = false;
                    }
                }
            }

            /**
             * If the service can't be built, we must throw an exception
             */
            if (!$found) {
                throw new \Phalcon\DI\Exception('Service "' . $this->_name . '" cannot be resolved');
            }

            if ($this->_shared) {
                $this->_sharedInstance = $instance;
            }

            return $instance;
        }


		/**
		 * Changes a parameter in the definition without resolve the service
		 *
		 * @param long $position
		 * @param array $parameter
         *
		 * @return \Phalcon\DI\Service
		 */
		public function setParameter($position, $parameter)
        {
            if (!is_array($this->_definition)) {
                throw new \Phalcon\DI\Exception('Definition must be an array to update its parameters');
            }

            if (!is_long($position)) {
                throw new \Phalcon\DI\Exception('Position must be integer');
            }

            if (!is_array($parameter)) {
                throw new \Phalcon\DI\Exception('Parameter must be an array');
            }

            /**
             * Update parameter
             */
            if (array_key_exists('arguments', $this->_definition)) {
                $arguments = $this->_definition['arguments'];
                $arguments[$position] = $parameter;
            } else {
                $arguments = array($position => $parameter);
            }

            /**
             * Re-update the definition and the arguments
             */
            $this->_definition['arguments'] = $arguments;

            return $this;
        }


		/**
		 * Returns a parameter in an specific position
		 *
		 * @param int $position
         *
		 * @return array
		 */
		public function getParameter($position)
        {
            if (!is_array($this->_definition)) {
                throw new \Phalcon\DI\Exception('Definition must be an array to update its parameters');
            }

            if (!is_long($position)) {
                throw new \Phalcon\DI\Exception('Position must be integer');
            }

            if (array_key_exists('arguments', $this->_definition)) {
                if (array_key_exists($position, $this->_definition['arguments'])) {
                    return $this->_definition['arguments'][$position];
                }
            }

            return null;
        }


		/**
		 * Restore the internal state of a service
		 *
		 * @param array $attributes
         *
		 * @return \Phalcon\DI\Service
		 */
		public static function __set_state($attributes)
        {
            if (array_key_exists('_name', $attributes)) {
                $name = $attributes['_name'];
            } else {
                throw new \Phalcon\DI\Exception('The attribute "_name" is required');
            }

            if (array_key_exists('_definition', $attributes)) {
                $definition = $attributes['_definition'];
            } else {
                throw new \Phalcon\DI\Exception('The attribute "_definition" is required');
            }

            if (array_key_exists('_shared', $attributes)) {
                $shared = $attributes['_shared'];
            } else {
                throw new \Phalcon\DI\Exception('The attribute "_shared" is required');
            }

            $service = new \Phalcon\DI\Service($name, $definition, $shared);

            return $service;
        }

	}
}
