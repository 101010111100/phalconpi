<?php 

namespace Phalcon\Validation {

	/**
	 * Phalcon\Validation\Message
	 *
	 * Encapsulates validation info generated in the validation process
	 *
	 */
	
	class Message {

		protected $_type;

		protected $_message;

		protected $_field;

		/**
		 * \Phalcon\Validation\Message constructor
		 *
		 * @param string $message
		 * @param string $field
		 * @param string $type
		 */
		public function __construct($message, $field = null, $type = null)
        {
            $this->_message = $message;
            $this->_field = $field;
            $this->_type = $type;
        }


		/**
		 * Sets message type
		 *
		 * @param string $type
         *
		 * @return \Phalcon\Mvc\Model\Message
		 */
		public function setType($type)
        {
            $this->_type = $type;

            return $this;
        }


		/**
		 * Returns message type
		 *
		 * @return string
		 */
		public function getType()
        {
            return $this->_type;
        }


		/**
		 * Sets verbose message
		 *
		 * @param string $message
         *
		 * @return \Phalcon\Mvc\Model\Message
		 */
		public function setMessage($message)
        {
            $this->_message = $message;

            return $this;
        }


		/**
		 * Returns verbose message
		 *
		 * @return string
		 */
		public function getMessage()
        {
            return $this->_message;
        }


		/**
		 * Sets field name related to message
		 *
		 * @param string $field
         *
		 * @return \Phalcon\Mvc\Model\Message
		 */
		public function setField($field)
        {
            $this->_field = $field;

            return $this;
        }


		/**
		 * Returns field name related to message
		 *
		 * @return string
		 */
		public function getField()
        {
            return $this->_field;
        }


		/**
		 * Magic __toString method returns verbose message
		 *
		 * @return string
		 */
		public function __toString()
        {
            return $this->_message;
        }


		/**
		 * Magic __set_state helps to recover messsages from serialization
		 *
		 * @param array $message
         *
		 * @return \Phalcon\Mvc\Model\Message
		 */
		public static function __set_state($message)
        {
            $messageObject = new \Phalcon\Validation\Message($message->_message, $message->_field, $message->_type);

            return $messageObject;
        }

	}
}
