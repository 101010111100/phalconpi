<?php 

namespace Phalcon\Events {

	/**
	 * Phalcon\Events\Event
	 *
	 * This class offers contextual information of a fired event in the EventsManager
	 */
	
	class Event {

		protected $_type;

		protected $_source;

		protected $_data;

		protected $_stopped = false;

		protected $_cancelable = true;

		/**
		 * \Phalcon\Events\Event constructor
		 *
		 * @param string $type
		 * @param object $source
		 * @param mixed $data
		 * @param boolean $cancelable
		 */
		public function __construct($type, $source, $data = null, $cancelable = null)
        {
            if (is_null($cancelable)) {
                $cancelable = true;
            }

            $this->_type = $type;
            $this->_source = $source;

            if (!is_null($data)) {
                $this->_data = $data;
            }

            if (true !== $cancelable) {
                $this->_cancelable = $cancelable;
            }
        }


		/**
		 * Set the event's type
		 *
		 * @param string $eventType
		 */
		public function setType($eventType)
        {
            $this->_type = $eventType;
        }


		/**
		 * Returns the event's type
		 *
		 * @return string
		 */
		public function getType()
        {
            return $this->_type;
        }


		/**
		 * Returns the event's source
		 *
		 * @return object
		 */
		public function getSource()
        {
            return $this->_source;
        }


		/**
		 * Set the event's data
		 *
		 * @param string $data
		 */
		public function setData($data)
        {
            $this->_data = $data;
        }


		/**
		 * Returns the event's data
		 *
		 * @return mixed
		 */
		public function getData()
        {
            return $this->_data;
        }


		/**
		 * Sets if the event is cancelable
		 *
		 * @param boolean $cancelable
		 */
		public function setCancelable($cancelable)
        {
            $this->_cancelable = $cancelable;
        }


		/**
		 * Check whether the event is cancelable
		 *
		 * @return boolean
		 */
		public function getCancelable()
        {
            return $this->_cancelable;
        }


		/**
		 * Stops the event preventing propagation
		 */
		public function stop()
        {
            if (true === $this->_cancelable) {
                $this->_stopped = true;
            } else {
                throw new \Phalcon\Events\Exception('Trying to cancel a non-cancelable event');
            }
        }


		/**
		 * Check whether the event is currently stopped
		 */
		public function isStopped()
        {
            return $this->_stopped;
        }

	}
}
