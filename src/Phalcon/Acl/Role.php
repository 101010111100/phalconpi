<?php 

namespace Phalcon\Acl {

	/**
	 * Phalcon\Acl\Role
	 *
	 * This class defines role entity and its description
	 *
	 */
	
	class Role implements \Phalcon\Acl\RoleInterface {

		protected $_name;

		protected $_description;

		/**
		 * \Phalcon\Acl\Role description
		 *
		 * @param string $name
		 * @param string $description
		 */
		public function __construct($name, $description = null)
        {
            if ('*' == $name) {
                throw new \Phalcon\Acl\Exception('Role name cannot be "*"');
            }

            $this->_name = $name;

            if (!is_null($description)) {
                $this->_description = $description;
            }
        }


		/**
		 * Returns the role name
		 *
		 * @return string
		 */
		public function getName()
        {
            return $this->_name;
        }


		/**
		 * Returns role description
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
