<?php 

namespace Phalcon\Events {

	/**
	 * Phalcon\Events\Manager
	 *
	 * Phalcon Events Manager, offers an easy way to intercept and manipulate, if needed,
	 * the normal flow of operation. With the EventsManager the developer can create hooks or
	 * plugins that will offer monitoring of data, manipulation, conditional execution and much more.
	 *
	 */
	
	class Manager implements \Phalcon\Events\ManagerInterface {

		protected $_events;

		protected $_collect = false;

		protected $_responses;

		/**
		 * Attach a listener to the events manager
		 *
		 * @param string $eventType
		 * @param object $handler
		 * @param int $priority
		 */
		public function attach($eventType, $handler, $priority = null)
        {
            if (is_null($priority)) {
                $priority = 100;
            }

            if (!is_string($eventType)) {
                throw new \Phalcon\Events\Exception('Event type must be a string');
            }

            if (!is_object($handler)) {
                throw new \Phalcon\Events\Exception('Event handler must be an Object');
            }

            if (!array_key_exists($eventType, $this->_events)) {
                /**
                 * Create an SplPriorityQuenue to store the events with priorities
                 */
                $priorityQueue = new \SplPriorityQueue;

                /**
                 * Set extraction flags
                 */
                $priorityQueue->setExtractFlags(1);

                /**
                 * Append the events to the quenue
                 */
                $this->_events[$eventType] = $priorityQueue;
            } else {
                $priorityQueue = $this->_events[$eventType];
            }

            /**
             * Insert the handler in the quenue
             */
            $priorityQueue->insert($handler, $priority);
        }


		/**
		 * Tells the event manager if it needs to collect all the responses returned by every
		 * registered listener in a single fire
		 *
		 * @param boolean $collect
		 */
		public function collectResponses($collect)
        {
            $this->_collect = $collect;
        }


		/**
		 * Check if the events manager is collecting all all the responses returned by every
		 * registered listener in a single fire
		 */
		public function isCollecting()
        {
            return $this->_collect;
        }


		/**
		 * Returns all the responses returned by every handler executed by the last 'fire' executed
		 *
		 * @return array
		 */
		public function getResponses()
        {
            return $this->_responses;
        }


		/**
		 * Removes all events from the EventsManager
		 *
		 * @param string $type
		 */
		public function dettachAll($type = null)
        {
            if (!is_null($type)) {
                if (array_key_exists($type, $this->_events)) {
                    $this->_events[$type] = null;
                }
            } else {
                $this->_events = null;
            }
        }


		/**
		 * Internal handler to call a queue of events
		 *
		 * @param \SplPriorityQueue $queue
		 * @param \Phalcon\Events\Event $event
         *
		 * @return mixed
		 */
		public function fireQueue($queue, $event)
        {
            if (!is_object($queue)) {
                throw new \Phalcon\Events\Exception('The SplPriorityQueue is not valid');
            }

            if (!is_object($event)) {
                throw new \Phalcon\Events\Exception('The event is not valid');
            }

            $status = null;
            $arguments = null;

            /**
             * Get the event type
             */
            $eventName = $event->getName();

            if (!is_string($eventName)) {
                throw new \Phalcon\Events\Exception('The event type not valid');
            }

            /**
             * Get the object who triggered the event
             */
            $source = $event->getSource();

            /**
             * Get extra data passed to the event
             */
            $data = $event->getData();

            /**
             * Tell if the event is cancelable
             */
            $cancelable = $event->getCancelable();

            /**
             * We need to clone the queue before iterate over it
             *
             * @var $iterator \SplPriorityQueue
             */
            $iterator = clone($queue);

            /**
             * Move the queue to the top
             */
            $iterator->top();

            while ($iterator->valid()) {
                /**
                 * Get the current data
                 */
                $handler = $iterator->current();

                /**
                 * Only handler objects are valid
                 */
                if (is_object($handler)) {
                    /**
                     * Check if the event is a closure
                     */
                    if ($handler instanceof \Closure) {
                        /**
                         * Create the closure arguments
                         */
                        if (is_null($arguments)) {
                            $arguments[] = $event;
                            $arguments[] = $source;
                            $arguments[] = $data;
                        }

                        /**
                         * Call the function in the PHP userland
                         */
                        $status = call_user_func_array($handler, $arguments);

                        /**
                         * Trace the response
                         */
                        if (true === $this->_collect) {
                            $this->_responses[] = $status;
                        }

                        if (true === $cancelable) {
                            /**
                             * Check if the event was stopped by the user
                             */
                            if (true === $event->isStopped()) {
                                break;
                            }
                        }
                    } else {
                        /**
                         * Check if the listener has implemented an event with the same name
                         */
                        if (method_exists($handler, $eventName)) {
                            /**
                             * Call the function in the PHP userland
                             */
                            $status = $handler->$eventName($event, $source, $data);

                            /**
                             * Trace the response
                             */
                            if (true === $this->_collect) {
                                $this->_responses[] = $status;
                            }

                            if (true === $cancelable) {
                                /**
                                 * Check if the event was stopped by the user
                                 */
                                if (true === $event->isStopped()) {
                                    break;
                                }
                            }
                        }
                    }
                }

                /**
                 * Move the queue to the next handler
                 */
                $iterator->next();
            }
        }


		/**
		 * Fires an event in the events manager causing that active listeners be notified about it
		 *
		 *<code>
		 *	$eventsManager->fire('db', $connection);
		 *</code>
		 *
		 * @param string $eventType
		 * @param object $source
		 * @param mixed  $data
		 * @param int $cancelable
         *
		 * @return mixed
		 */
		public function fire($eventType, $source, $data = null, $cancelable = null)
        {
            if (is_null($cancelable)) {
                $cancelable = true;
            }

            if (!is_string($eventType)) {
                throw new \Phalcon\Events\Exception('Event type must be a string');
            }

            if (!is_array($this->_events)) {
                return null;
            }

            /**
             * All valid events must have a colon separator
             */
            if (false === strpos($eventType, ':')) {
                throw new \Phalcon\Events\Exception('Invalid event type ' . $eventType);
            }

            $eventParts = explode(':', $eventType);

            $type = $eventParts[0];
            $eventName = $eventParts[1];

            /**
             * Responses must be traced?
             */
            if (true === $this->_collect) {
                $this->_responses = null;
            }

            $status = null;
            $event = null;

            /**
             * Check if events are grouped by type
             */
            if (array_key_exists($type, $this->_events)) {
                $fireEvents = $this->_events[$type];

                if (is_object($fireEvents)) {
                    /**
                     * Create the event context
                     */
                    $event = new \Phalcon\Events\Event($eventName, $source, $data, $cancelable);

                    /**
                     * Call the events queue
                     */
                    $status = $this->fireQueue($fireEvents, $event);
                }
            }

            /**
             * Check if there are listeners for the event type itself
             */
            if (array_key_exists($eventType, $this->_events)) {
                $fireEvents = $this->_events[$type];

                if (is_object($fireEvents)) {
                    /**
                     * Create the event if it wasn't created before
                     */
                    if (is_null($event)) {
                        $event = new \Phalcon\Events\Event($eventName, $source, $data, $cancelable);
                    }

                    /**
                     * Call the events queue
                     */
                    $status = $this->fireQueue($fireEvents, $event);
                }
            }

            return $status;
        }


		/**
		 * Check whether certain type of event has listeners
		 *
		 * @param string $type
         *
		 * @return boolean
		 */
		public function hasListeners($type)
        {
            if (is_array($this->_events)) {
                if (array_key_exists($type, $this->_events)) {
                    return true;
                }
            }

            return false;
        }


		/**
		 * Returns all the attached listeners of a certain type
		 *
		 * @param string $type
         *
		 * @return array
		 */
		public function getListeners($type)
        {
            if (is_array($this->_events)) {
                if (array_key_exists($type, $this->_events)) {
                    return $this->_events[$type];
                }
            }

            return array();
        }

	}
}
