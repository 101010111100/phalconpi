<?php 

namespace Phalcon\Flash {

	/**
	 * Phalcon\Flash\Session
	 *
	 * Temporarily stores the messages in session, then messages can be printed in the next request
	 */
	
	class Session extends \Phalcon\Flash implements \Phalcon\FlashInterface, \Phalcon\DI\InjectionAwareInterface {

		protected $_dependencyInjector;

		/**
		 * Sets the dependency injector
		 *
		 * @param \Phalcon\DiInterface $dependencyInjector
		 */
		public function setDI($dependencyInjector)
        {
            $this->_dependencyInjector = $dependencyInjector;
        }


		/**
		 * Returns the internal dependency injector
		 *
		 * @return \Phalcon\DiInterface
		 */
		public function getDI()
        {
            return $this->_dependencyInjector;
        }


		/**
		 * Returns the messages stored in session
		 *
		 * @param boolean $remove
         *
		 * @return array
		 */
		protected function _getSessionMessages($remove)
        {
            if (!is_object($this->_dependencyInjector)) {
                throw new \Phalcon\Flash\Exception('A dependency injection container is required to access the "session" service');
            }

            $session = $this->_dependencyInjector->getShared('session');

            $messages = $session->get('_flashMessages');

            if (true === $remove) {
                $session->remove('_flashMessages');
            }

            return $messages;
        }


		/**
		 * Stores the messages in session
		 *
		 * @param array $messages
		 */
		protected function _setSessionMessages($messages)
        {
            if (!is_object($this->_dependencyInjector)) {
                throw new \Phalcon\Flash\Exception('A dependency injection container is required to access the "session" service');
            }

            $session = $this->_dependencyInjector->getShared('session');

            return $session->set('_flashMessages', $messages);
        }


		/**
		 * Adds a message to the session flasher
		 *
		 * @param string $type
		 * @param string $message
		 */
		public function message($type, $message)
        {
            $messages = $this->_getSessionMessages(false);

            if (!is_array($messages)) {
                $messages = array();
            }

            if (!array_key_exists($type, $messages)) {
                $messages[$type] = array();
            }

            $messages[$type][] = $message;

            $this->_setSessionMessages($messages);
        }


		/**
		 * Returns the messages in the session flasher
		 *
		 * @param string $type
		 * @param boolean $remove
         *
		 * @return array
		 */
		public function getMessages($type = null, $remove = null)
        {
            if (is_null($remove)) {
                $remove = true;
            }

            $messages = $this->_getSessionMessages($remove);

            if (is_array($messages)) {
                if (is_string($type)) {
                    if (array_key_exists($type, $messages)) {
                        return $messages[$type];
                    }
                }

                return $messages;
            }

            return array();
        }


		/**
		 * Prints the messages in the session flasher
		 *
		 * @param string $type
		 * @param boolean $remove
		 */
		public function output($remove = null)
        {
            if (is_null($remove)) {
                $remove = true;
            }

            $messages = $this->_getSessionMessages($remove);

            if ($messages && is_array($messages)) {
                foreach ($messages as $type => $message) {
                    $this->outputMessage($type, $message);
                }
            }
        }

	}
}
