<?php 

namespace Phalcon\Acl {

	/**
	 * Phalcon\Acl\Adapter
	 *
	 * Adapter for Phalcon\Acl adapters
	 */
	
	abstract class Adapter implements \Phalcon\Events\EventsAwareInterface {

		protected $_eventsManager;

		protected $_defaultAccess = 1;

		protected $_accessGranted = false;

		protected $_activeRole;

		protected $_activeResource;

		protected $_activeAccess;

		/**
		 * Sets the events manager
		 *
		 * @param \Phalcon\Events\ManagerInterface $eventsManager
		 */
		public function setEventsManager($eventsManager)
        {
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
		 * Sets the default access level (Phalcon\Acl::ALLOW or \Phalcon\Acl::DENY)
		 *
		 * @param int $defaultAccess
		 */
		public function setDefaultAction($defaultAccess)
        {
            $this->_defaultAccess = $defaultAccess;
        }


		/**
		 * Returns the default ACL access level
		 *
		 * @return int
		 */
		public function getDefaultAction()
        {
            return $this->_defaultAccess;
        }


		/**
		 * Returns the role which the list is checking if it's allowed to certain resource/access
		 *
		 * @return string
		 */
		public function getActiveRole()
        {
            return $this->_activeRole;
        }


		/**
		 * Returns the resource which the list is checking if some role can access it
		 *
		 * @return string
		 */
		public function getActiveResource()
        {
            return $this->_activeResource;
        }


		/**
		 * Returns the access which the list is checking if some role can access it
		 *
		 * @return string
		 */
		public function getActiveAccess()
        {
            return $this->_activeAccess;
        }

	}
}
