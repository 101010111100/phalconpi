<?php 

namespace Phalcon\Validation {

	/**
	 * Phalcon\Validation\Validator
	 *
	 * This is a base class for validators
	 */
	
	abstract class Validator {

		protected $_options;

		/**
		 * \Phalcon\Validation\Validator constructor
		 *
		 * @param array $options
		 */
		public function __construct($options = null)
        {
            // FIXME: In CPhalcon — "The attribute must be an string" is an exception message

            if ($options && !is_array($options)) {
                throw new \Phalcon\Validation\Exception('Options must be an array');
            }

            $this->_options = $options;
        }


		/**
		 * Checks if an option is defined
		 *
		 * @param string $key
         *
		 * @return mixed
		 */
		public function isSetOption($key)
        {
            if (is_array($this->_options)) {
                if (array_key_exists($key, $this->_options)) {
                    return true;
                }
            }

            return false;
        }


		/**
		 * Returns an option in the validator's options
		 * Returns null if the option hasn't been set
		 *
		 * @param string $key
         *
		 * @return mixed
		 */
		public function getOption($key)
        {
            if (is_array($this->_options)) {
                if (array_key_exists($key, $this->_options)) {
                    return $this->_options[$key];
                }
            }

            return null;
        }

	}
}
