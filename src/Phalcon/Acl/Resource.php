<?php 

namespace Phalcon\Acl {

	/**
	 * Phalcon\Acl\Resource
	 *
	 * This class defines resource entity and its description
	 *
	 */
	
	class Resource implements \Phalcon\Acl\ResourceInterface {

		protected $_name;

		protected $_description;

		/**
		 * \Phalcon\Acl\Resource constructor
		 *
		 * @param string $name
		 * @param string $description
		 */
		public function __construct($name, $description = null)
        {
            if ('*' == $name) {
                throw new \Phalcon\Acl\Exception('Resource name cannot be "*"');
            }

            $this->_name = $name;

            if (!is_null($description)) {
                $this->_description = $description;
            }
        }


		/**
		 * Returns the resource name
		 *
		 * @return string
		 */
		public function getName()
        {
            return $this->_name;
        }


		/**
		 * Returns resource description
		 *
		 * @return string
		 */
		public function getDescription()
        {
            return $this->_description;
        }


		/**
		 * Magic method __toString
		 *
		 * @return string
		 */
		public function __toString()
        {
            return $this->_name;
        }

	}
}
