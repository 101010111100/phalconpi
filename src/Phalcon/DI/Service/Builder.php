<?php 

namespace Phalcon\DI\Service {

	/**
	 * Phalcon\DI\Service\Builder
	 *
	 * This class builds instances based on complex definitions
	 */
	
	class Builder {

		/**
		 * Resolves a constructor/call parameter
		 *
		 * @param \Phalcon\DiInterface $dependencyInjector
		 * @param int $position
		 * @param array $argument
         *
		 * @return mixed
		 */
		protected function _buildParameter($dependencyInjector, $position, $argument)
        {
            /**
             * All the arguments must be an array
             */
            if (!is_array($argument)) {
                throw new \Phalcon\DI\Exception('Argument at position "' . $position . '" must be an array');
            }

            /**
             * All the arguments must have a type
             */
            if (array_key_exists('type', $argument)) {
                throw new \Phalcon\DI\Exception('Argument at position "' . $position . '" must have a type');
            }

            /**
             * If the argument type is 'service', we obtain the service from the DI
             */
            if ('service' == $argument['type']) {
                if (!array_key_exists('name', $argument)) {
                    throw new \Phalcon\DI\Exception('Service "name" is required in parameter on position ' . $position);
                }

                if (!is_object($dependencyInjector)) {
                    throw new \Phalcon\DI\Exception('The dependency injector container is not valid');
                }

                return $dependencyInjector->get($argument['name']);
            }

            /**
             * If the argument type is 'parameter', we assign the value as it is
             */
            if ('parameter' == $argument['type']) {
                if (!array_key_exists('value', $argument)) {
                    throw new \Phalcon\DI\Exception('Service "value" is required in parameter on position ' . $position);
                }

                return $argument['value'];
            }

            /**
             * If the argument type is 'instance', we assign the value as it is
             */
            if ('instance' == $argument['type']) {
                if (!array_key_exists('className', $argument)) {
                    throw new \Phalcon\DI\Exception('Service "className" is required in parameter on position ' . $position);
                }

                if (!is_object($dependencyInjector)) {
                    throw new \Phalcon\DI\Exception('The dependency injector container is not valid');
                }

                if (array_key_exists('arguments', $argument)) {
                    /**
                     * Build the instance with arguments
                     */
                    $value = $dependencyInjector->get($argument['className'], $argument['arguments']);
                } else {
                    /**
                     * The instance parameter does not have arguments for its constructor
                     */
                    $value = $dependencyInjector->get($argument['className']);
                }

                return $value;
            }

            /**
             * Unknown parameter type
             */
            throw new \Phalcon\DI\Exception('Unknown service type in parameter on position ' . $position);
        }


		/**
		 * Resolves an array of parameters
		 *
		 * @param \Phalcon\DiInterface $dependencyInjector
		 * @param array $arguments
         *
		 * @return arguments
		 */
		protected function _buildParameters($dependencyInjector, $arguments)
        {
            /**
             * The arguments group must be an array of arrays
             */
            if (!is_array($arguments)) {
                throw new \Phalcon\DI\Exception('Definition arguments must be an array');
            }

            $buildArguments = array();

            if ($arguments) {
                foreach ($arguments as $position => $argument) {
                    $buildArguments[] = $this->_buildParameter($dependencyInjector, $position, $argument);
                }
            }

            return $buildArguments;
        }


		/**
		 * Builds a service using a complex service definition
		 *
		 * @param \Phalcon\DiInterface $dependencyInjector
		 * @param array $definition
		 * @param array $parameters
         *
		 * @return mixed
		 */
		public function build($dependencyInjector, $definition, $parameters = null)
        {
            if (!is_array($definition)) {
                throw new \Phalcon\DI\Exception('The service definition must be an array');
            }

            /**
             * The class name is required
             */
            if (!array_key_exists('className', $definition)) {
                throw new \Phalcon\DI\Exception('Invalid service definition. Missing "className" parameter');
            }

            $className = $definition['className'];

            if (is_array($parameters)) {
                /**
                 * Build the instance overriding the definition constructor parameters
                 */
                if ($parameters) {
                    $instance = new $className($parameters);
                } else {
                    $instance = new $className;
                }
            } else {
                /**
                 * Check if the argument has constructor arguments
                 */
                if (array_key_exists('arguments', $definition)) {
                    /**
                     * Resolve the constructor parameters
                     */
                    $buildArguments = $this->_buildParameters($dependencyInjector, $definition['arguments']);

                    /**
                     * Create the instance based on the parameters
                     */
                    $instance = new $className($buildArguments);
                } else {
                    $instance = new $className;
                }
            }

            /**
             * The definition has calls?
             */
            if (array_key_exists('calls', $definition)) {
                if (!is_object($instance)) {
                    throw new \Phalcon\DI\Exception('The definition has setter injection parameters, but the constructor didn\'t return an instance');
                }

                if (!is_array($definition['calls'])) {
                    throw new \Phalcon\DI\Exception('Setter injection parameters must be an array');
                }

                /**
                 * The method call has parameters
                 */
                if ($definition['calls']) {
                    foreach ($definition['calls'] as $position => $method) {
                        /**
                         * The call parameter must be an array of arrays
                         */
                        if (!is_array($method)) {
                            throw new \Phalcon\DI\Exception('Method call must be an array on position ' . $position);
                        }

                        /**
                         * A param 'method' is required
                         */
                        if (!array_key_exists('method', $method)) {
                            throw new \Phalcon\DI\Exception('The method name is required on position ' . $position);
                        }

                        /**
                         * Create the method call
                         */
                        $methodCall = array($instance, $method['method']);

                        if (array_key_exists('arguments', $method)) {
                            if (!is_array($method['arguments'])) {
                                throw new \Phalcon\DI\Exception('Call arguments must be an array ' . $position);
                            }

                            if ($method['arguments']) {
                                /**
                                 * Resolve the constructor parameters
                                 */
                                $buildArguments = $this->_buildParameters($dependencyInjector, $method['arguments']);

                                /**
                                 * Call the method on the instance
                                 */
                                call_user_func_array($methodCall, $buildArguments);

                                /**
                                 * Go to next method call
                                 */
                                continue;
                            }
                        }

                        /**
                         * Call the method on the instance without arguments
                         */
                        call_user_func_array($methodCall, array());
                    }
                }
            }

            /**
             * The definition has properties?
             */
            if (array_key_exists('properties', $definition)) {
                if (!is_object($instance)) {
                    throw new \Phalcon\DI\Exception('The definition has properties injection parameters but the constructor didn\'t return an instance');
                }

                if (!is_array($definition['properties'])) {
                    throw new \Phalcon\DI\Exception('Setter injection parameters must be an array');
                }

                /**
                 * The method call has parameters
                 */
                if ($definition['properties']) {
                    foreach ($definition['properties'] as $position => $property) {
                        /**
                         * The call parameter must be an array of arrays
                         */
                        if (!is_array($property)) {
                            throw new \Phalcon\DI\Exception('Property must be an array on position ' . $position);
                        }

                        /**
                         * A param 'name' is required
                         */
                        if (!array_key_exists('name', $property)) {
                            throw new \Phalcon\DI\Exception('The property name is required on position ' . $position);
                        }

                        /**
                         * A param 'value' is required
                         */
                        if (!array_key_exists('value', $property)) {
                            throw new \Phalcon\DI\Exception('The property value is required on position ' . $position);
                        }

                        $name = $property['name'];

                        /**
                         * Resolve the parameter
                         */
                        $value = $this->_buildParameter($dependencyInjector, $position, $property['value']);

                        /**
                         * Update the public property
                         */
                        $instance->$name = $value;
                    }
                }
            }

            return $instance;
        }

	}
}
