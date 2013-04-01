<?php 

namespace Phalcon {

	/**
	 * Phalcon\Config
	 *
	 * Phalcon\Config is designed to simplify the access to, and the use of, configuration data within applications.
	 * It provides a nested object property based user interface for accessing this configuration data within
	 * application code.
	 *
	 *<code>
	 *	$config = new Phalcon\Config(array(
	 *		"database" => array(
	 *			"adapter" => "Mysql",
	 *			"host" => "localhost",
	 *			"username" => "scott",
	 *			"password" => "cheetah",
	 *			"name" => "test_db"
	 *		),
	 *		"phalcon" => array(
	 *			"controllersDir" => "../app/controllers/",
	 *			"modelsDir" => "../app/models/",
	 *			"viewsDir" => "../app/views/"
	 *		)
	 * ));
	 *</code>
	 *
	 */
	
	class Config implements \ArrayAccess {

		/**
		 * \Phalcon\Config constructor
		 *
		 * @param array $arrayConfig
		 */
		public function __construct($arrayConfig = null)
        {
            if ($arrayConfig && is_array($arrayConfig)) {
                foreach ($arrayConfig as $key => $value) {
                    if (is_array($value)) {
                        if (!array_key_exists(0, $value)) {
                            $this->$key = new \Phalcon\Config($value);
                        } else {
                            $this->$key = $value;
                        }
                    } else {
                        $this->$key = $value;
                    }
                }
            } else {
                if (!is_null($arrayConfig)) {
                    throw new \Phalcon\Config\Exception('The configuration must be an Array');
                }
            }
        }


		/**
		 * Allows to check whether an attribute is defined using the array-syntax
		 *
		 *<code>
		 * var_dump(isset($config['database']));
		 *</code>
		 *
		 * @param string $index
         *
		 * @return boolean
		 */
		public function offsetExists($index)
        {
            return array_key_exists($index, get_object_vars($this));
        }


		/**
		 * Gets an attribute from the configuration, if the attribute isn't defined returns null
		 * If the value is exactly null or is not defined the default value will be used instead
		 *
		 *<code>
		 * echo $config->get('controllersDir', '../app/controllers/');
		 *</code>
		 *
		 * @param string $index
		 * @param mixed $defaultValue
         *
		 * @return mixed
		 */
		public function get($index, $defaultValue = null)
        {
            if ($this->offsetExists($index)) {
                $value = $this->$index;

                if ($value) {
                    return $value;
                }
            }

            return $defaultValue;
        }


		/**
		 * Gets an attribute using the array-syntax
		 *
		 *<code>
		 * print_r($config['database']);
		 *</code>
		 *
		 * @param string $index
         *
		 * @return string
		 */
		public function offsetGet($index)
        {
            return $this->$index;
        }


		/**
		 * Sets an attribute using the array-syntax
		 *
		 *<code>
		 * $config['database'] = array('type' => 'Sqlite');
		 *</code>
		 *
		 * @param string $index
		 * @param mixed $value
		 */
		public function offsetSet($index, $value)
        {
            if (!is_string($index)) {
                throw new \Phalcon\Config\Exception('Index key must be string');
            }

            if (is_array($value)) {
                $arrayValue = new \Phalcon\Config($value);
            } else {
                $arrayValue = $value;
            }

            $this->$index = $arrayValue;
        }


		/**
		 * Unsets an attribute using the array-syntax
		 *
		 *<code>
		 * unset($config['database']);
		 *</code>
		 *
		 * @param string $index
		 */
		public function offsetUnset($index)
        {
            return true;
        }


		/**
		 * Merges a configuration into the current one
		 *
		 *<code>
		 *	$appConfig = new \Phalcon\Config(array('database' => array('host' => 'localhost')));
		 *	$globalConfig->merge($config2);
		 *</code>
		 *
		 * @param \Phalcon\Config $config
		 */
		public function merge($config)
        {
            if (!is_object($config)) {
                throw new \Phalcon\Config\Exception('Configuration must be an Object');
            }

            $arrayConfig = get_object_vars($config);

            if ($arrayConfig && is_array($arrayConfig)) {
                foreach ($arrayConfig as $key => $value) {
                    if (is_object($value)) {
                        if (array_key_exists($key, $this)) {
                            $activeValue = $this->$key;

                            if (is_object($activeValue)) {
                                if (method_exists($activeValue, 'merge')) {
                                    $activeValue->merge($value);

                                    continue;
                                }
                            }
                        }
                    }

                    $this->$key = $value;
                }
            }
        }


		/**
		 * Converts recursively the object to an array
		 *
		 *<code>
		 *	print_r($config->toArray());
		 *</code>
		 *
		 * @return array
		 */
		public function toArray()
        {
            $arrayConfig = get_object_vars($this);

            if ($arrayConfig && is_array($arrayConfig)) {
                foreach ($arrayConfig as $key => $value) {
                    if (is_object($value)) {
                        if (method_exists($value, 'toArray')) {
                            $arrayConfig[$key] = $value->toArray();
                        }
                    }
                }
            }

            return $arrayConfig;
        }


		/**
		 * Restores the state of a \Phalcon\Config object
		 *
		 * @param array $data
         *
		 * @return \Phalcon\Config
		 */
		public static function __set_state($data)
        {
            return new \Phalcon\Config($data);
        }

	}
}
