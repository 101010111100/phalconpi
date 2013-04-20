<?php 

namespace Phalcon\Forms {

	/**
	 * Phalcon\Forms\Manager
	 *
	 * Manages forms whithin the application. Allowing the developer to access them from
	 * any part of the application
	 */
	
	class Manager {

		protected $_forms;

		public function create($name = null, $entity = null)
        {
            // FIXME: In CPhalcon — variable $name is not used

            $form = new \Phalcon\Forms\Form($entity);

            $this->_forms[$name] = $form;

            return $form;
        }


		/**
		 * Returns a form by its name
		 *
		 * @param string $name
         *
		 * @return \Phalcon\Forms\Form
		 */
		public function get($name)
        {
            // FIXME: In CPhalcon — method has no body

            if (array_key_exists($name, $this->_forms)) {
                return $this->_forms[$name];
            }

            return null;
        }

	}
}
