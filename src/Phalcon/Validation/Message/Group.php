<?php 

namespace Phalcon\Validation\Message {

	/**
	 * Phalcon\Validation\Message\Group
	 *
	 * Represents a group of validation messages
	 */
	
	class Group implements \Countable, \ArrayAccess, \Iterator, \Traversable {

        // FIXME: In CPhalcon — default value for _position is not set
		protected $_position = 0;

		protected $_messages;

		/**
		 * \Phalcon\Validation\Message\Group constructor
		 *
		 * @param array $messages
		 */
		public function __construct($messages = null)
        {
            if (is_array($messages)) {
                $this->_messages = $messages;
            }
        }


		/**
		 * Gets an attribute a message using the array syntax
		 *
		 *<code>
		 * print_r($messages[0]);
		 *</code>
		 *
		 * @param string $index
         *
		 * @return \Phalcon\Validation\Message
		 */
		public function offsetGet($index)
        {
            if (array_key_exists($index, $this->_messages)) {
                return $this->_messages[$index];
            }

            return null;
        }


		/**
		 * Sets an attribute using the array-syntax
		 *
		 *<code>
		 * $messages[0] = new \Phalcon\Validation\Message('This is a message');
		 *</code>
		 *
		 * @param string $index
		 * @param \Phalcon\Validation\Message $message
		 */
		public function offsetSet($index, $message)
        {
            if (!is_object($message)) {
                throw new \Phalcon\Validation\Exception('The message must be an object');
            }

            $this->_messages[$index] = $message;
        }


		/**
		 * Checks if an index exists
		 *
		 *<code>
		 * var_dump(isset($message['database']));
		 *</code>
		 *
		 * @param string $index
         *
		 * @return boolean
		 */
		public function offsetExists($index)
        {
            return array_key_exists($index, $this->_messages);
        }


		/**
		 * Removes a message from the list
		 *
		 *<code>
		 * unset($message['database']);
		 *</code>
		 *
		 * @param string $index
		 */
		public function offsetUnset($index)
        {
            return true;
        }


		/**
		 * Appends a message to the group
		 *
		 *<code>
		 * $messages->appendMessage(new \Phalcon\Validation\Message('This is a message'));
		 *</code>
		 *
		 * @param \Phalcon\Validation\MessageInterface $message
		 */
		public function appendMessage($message)
        {
            if (!is_object($message)) {
                throw new \Phalcon\Validation\Exception('The message must be an object');
            }

            $this->_messages[] = $message;
        }


		/**
		 * Appends an array of messages to the group
		 *
		 *<code>
		 * $messages->appendMessages($messagesArray);
		 *</code>
		 *
		 * @param \Phalcon\Validation\MessageInterface[] $messages
		 */
		public function appendMessages($messages)
        {
            if (!is_array($messages) && !is_object($messages)) {
                throw new \Phalcon\Validation\Exception('The messages must be array or object');
            }

            $currentMessages = $this->_messages;

            if (is_array($messages)) {
                /**
                 * An array of messages is simply merged into the current one
                 */
                if (is_array($currentMessages)) {
                    $finalMessages = array_merge($currentMessages, $messages);
                } else {
                    $finalMessages = $messages;
                }

                $this->_messages = $finalMessages;
            } else {
                /**
                 * A group of messages is iterated and appended one-by-one to the current list
                 */
                $messages->rewind();

                foreach ($messages as $message) {
                    $this->appendMessage($message);
                }
            }
        }


		/**
		 * Returns the number of messages in the list
		 *
		 * @return int
		 */
		public function count()
        {
            return count($this->_messages);
        }


		/**
		 * Rewinds the internal iterator
		 */
		public function rewind()
        {
            $this->_position = 0;
        }


		/**
		 * Returns the current message in the iterator
		 *
		 * @return \Phalcon\Validation\Message
		 */
		public function current()
        {
            if (array_key_exists($this->_position, $this->_messages)) {
                return $this->_messages[$this->_position];
            }

            return null;
        }


		/**
		 * Returns the current position/key in the iterator
		 *
		 * @return int
		 */
		public function key()
        {
            return $this->_position;
        }


		/**
		 * Moves the internal iteration pointer to the next position
		 *
		 */
		public function next()
        {
            $this->_position++;
        }


		/**
		 * Check if the current message the iterator is valid
		 *
		 * @return boolean
		 */
		public function valid()
        {
            return array_key_exists($this->_position, $this->_messages);
        }


		/**
		 * Magic __set_state helps to re-build messages variable exporting
		 *
		 * @param array $group
         *
		 * @return \Phalcon\Mvc\Model\Message\Group
		 */
		public static function __set_state($group)
        {
            $groupObject = new \Phalcon\Validation\Message\Group($group->_messages);

            return $groupObject;
        }

	}
}
