<?php 

namespace Phalcon {

	/**
	 * Phalcon\Filter
	 *
	 * The Phalcon\Filter component provides a set of commonly needed data filters. It provides
	 * object oriented wrappers to the php filter extension. Also allows the developer to
	 * define his/her own filters
	 *
	 *<code>
	 *	$filter = new Phalcon\Filter();
	 *	$filter->sanitize("some(one)@exa\\mple.com", "email"); // returns "someone@example.com"
	 *	$filter->sanitize("hello<<", "string"); // returns "hello"
	 *	$filter->sanitize("!100a019", "int"); // returns "100019"
	 *	$filter->sanitize("!100a019.01a", "float"); // returns "100019.01"
	 *</code>
	 */
	
	class Filter implements \Phalcon\FilterInterface {

		protected $_filters;

		/**
		 * \Phalcon\Filter constructor
		 */
		public function __construct()
        {
            $this->_filters = array();
        }


		/**
		 * Adds a user-defined filter
		 *
		 * @param string $name
		 * @param callable $handler
         *
		 * @return \Phalcon\Filter
		 */
		public function add($name, $handler)
        {
            if (!is_string($name)) {
                throw new \Phalcon\Filter\Exception('Filter name must be string');
            }

            if (!is_object($handler)) {
                throw new \Phalcon\Filter\Exception('Filter must be an object');
            }

            $this->_filters[$name] = $handler;

            return $this;
        }


		/**
		 * Sanitizes a value with a specified single or set of filters
		 *
		 * @param  mixed $value
		 * @param  mixed $filters
         *
		 * @return mixed
		 */
		public function sanitize($value, $filters)
        {
            /**
             * Apply an array of filters
             */
            if (is_array($filters)) {
                $newValue = $value;

                if (!is_null($value)) {
                    if ($filters && is_array($filters)) {
                        foreach ($filters as $filter) {
                            /**
                             * If the value to filter is an array we apply the filters recursively
                             */
                            if (is_array($newValue)) {
                                $arrayValue = array();

                                if ($newValue) {
                                    foreach ($newValue as $key => $itemValue) {
                                        $filterValue = $this->_sanitize($itemValue, $filter);

                                        $arrayValue[$key] = $filterValue;
                                    }
                                }

                                $newValue = $arrayValue;
                            } else {
                                $newValue = $this->_sanitize($newValue, $filter);
                            }
                        }
                    }
                }

                return $newValue;
            }

            /**
             * Apply a single filter value
             */
            if (is_array($value)) {
                $sanitizedValue = array();

                if ($value) {
                    foreach ($value as $key => $itemValue) {
                        $filterValue = $this->_sanitize($itemValue, $filters);

                        $sanitizedValue[$key] = $filterValue;
                    }
                }
            } else {
                $sanitizedValue = $this->_sanitize($value, $filters);
            }

            return $sanitizedValue;
        }


		/**
		 * Internal sanitize wrapper to filter_var
		 *
		 * @param  mixed $value
		 * @param  string $filter
         *
		 * @return mixed
		 */
		protected function _sanitize($value, $filter)
        {
            if (array_key_exists($filter, $this->_filters)) {
                $filterObject = $this->_filters[$filter];

                /**
                 * If the filter is a closure we call it in the PHP userland
                 */
                if ($filterObject instanceof \Closure) {
                    $arguments = array($value);

                    $filtered = call_user_func_array($filterObject, $arguments);
                } else {
                    $filtered = $filterObject->filter($value);
                }

                return $filtered;
            }

            if ('email' === $filter) {
                $escaped = str_replace("'", '', $value);

                return filter_var($escaped, FILTER_SANITIZE_EMAIL);
            }

            if ('int' === $filter) {
                return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            }

            if ('string' === $filter) {
                return filter_var($value, FILTER_SANITIZE_STRIPPED);
            }

            if ('float' === $filter) {
                return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, array('flags' => 4096));
            }

            if ('alphanum' === $filter) {
                return preg_replace('@[^a-z0-9]+@ui', '', $value);
            }

            if ('trim' === $filter) {
                return trim($value);
            }

            if ('striptags' === $filter) {
                return strip_tags($value);
            }

            if ('lower' === $filter) {
                if (function_exists('mb_strtolower')) {
                    /**
                     * 'lower' checks for the mbstring extension to make a correct lowercase
                     * transformation
                     */
                    $filtered = mb_strtolower($value);
                } else {
                    $filtered = strtolower($value);
                }

                return $filtered;
            }

            if ('upper' === $filter) {
                if (function_exists('mb_strtoupper')) {
                    /**
                     * 'upper' checks for the mbstring extension to make a correct lowercase
                     * transformation
                     */
                    $filtered = mb_strtoupper($value);
                } else {
                    $filtered = strtoupper($value);
                }

                return $filtered;
            }

            throw new \Phalcon\Filter\Exception('Sanitize filter "' . $filter . '" is not supported');
        }


		/**
		 * Return the user-defined filters in the instance
		 *
		 * @return object[]
		 */
		public function getFilters()
        {
            return $this->_filters;
        }

	}
}
