<?php 

namespace Phalcon\Forms {

	/**
	 * Phalcon\Forms\Form
	 *
	 * This component allows to build forms
	 */
	
	class Form implements \Countable, \Iterator, \Traversable {

        // FIXME: In CPhalcon — default position is not set

		protected $_position = 0;

		protected $_entity;

		protected $_data;

		protected $_elements;

		protected $_elementsIndexed;

		protected $_messages;

		/**
		 * \Phalcon\Forms\Form constructor
		 *
		 * @param object $entity
		 */
		public function __construct($entity = null)
        {
            if (!is_object($entity)) {
                throw new \Phalcon\Forms\Exception('The base entity is not valid');
            }

            $this->_entity = $entity;

            /**
             * Check for an 'initialize' method and call it
             */
            if (method_exists($this, 'initialize')) {
                $this->initialize();
            }
        }


		/**
		 * Sets the entity related to the model
		 *
		 * @param object $entity
         *
		 * @return \Phalcon\Forms\Form
		 */
		public function setEntity($entity)
        {
            $this->_entity = $entity;

            return $this;
        }


		/**
		 * Returns the entity related to the model
		 *
		 * @return object
		 */
		public function getEntity()
        {
            return $this->_entity;
        }


		/**
		 * Returns the form elements added to the form
		 *
		 * @return \Phalcon\Forms\ElementInterface[]
		 */
		public function getElements()
        {
            return $this->_elements;
        }


		/**
		 * Binds data to the entity
		 *
		 * @param array $data
		 * @param object $entity
		 * @param object $entity
         *
		 * @return \Phalcon\Forms\Form
		 */
		public function bind($data, $entity, $whitelist = null)
        {
            // FIXME: In CPhalcon — no usage of whitelist variable

            /**
             * The data must be an array
             */
            if (!is_array($data)) {
                throw new \Phalcon\Forms\Exception('The data must be an array');
            }

            if (!is_array($this->_elements)) {
                throw new \Phalcon\Forms\Exception('There are no elements in the form');
            }

            if ($data) {
                foreach ($data as $key => $value) {
                    if (array_key_exists($this->_elements, $key)) {
                        $method = 'set' . ucfirst($key);

                        if (method_exists($entity, $method)) {
                            /**
                             * Use the setter if any available
                             */
                            $entity->$method($value);
                        } else {
                            /**
                             * Use the public property if it doesn't have a setter
                             */
                            $entity->$key = $value;
                        }
                    }
                }
            }

            $this->_data = $data;
        }


		/**
		 * Validates the form
		 *
		 * @param array $data
		 * @param object $entity
         *
		 * @return boolean
		 */
		public function isValid($data = null, $entity = null)
        {
            if (is_array($this->_elements)) {
                /**
                 * If the user doesn't pass an entity we use the one in this_ptr->_entity
                 */
                if (is_object($entity)) {
                    $this->bind($data, $entity);
                }

                /**
                 * If the data is not an array use the one passed previously
                 */
                if (!is_array($data)) {
                    $data = $this->_data;
                }

                $notFailed = true;

                $messages = array();

                if ($this->_elements) {
                    foreach ($this->_elements as $element) {
                        $validators = $element->getValidators();

                        if ($validators && is_array($validators)) {
                            /**
                             * Element's name
                             */
                            $name = $element->getName();

                            /**
                             * Prepare the validators
                             */
                            $preparedValidators = array();

                            foreach ($validators as $validator) {
                                $scope = array();
                                $scope[] = $name;
                                $scope[] = $validator;

                                $preparedValidators[] = $scope;
                            }

                            /**
                             * Create an implicit validation
                             */
                            $validation = new \Phalcon\Validation($preparedValidators);

                            $elementMessages = $validation->validate($data, $entity);

                            if ($elementMessages) {
                                $name = $element->getName();

                                $messages[$name] = $elementMessages;

                                $notFailed = false;
                            }
                        }
                    }
                }

                /**
                 * If the validation fails
                 */
                if (false === $notFailed) {
                    $this->_messages = $messages;
                }

                /**
                 * Return the validation status
                 */
                return $notFailed;
            }
        }


		/**
		 * Returns the messages generated in the validation
		 *
		 * @param boolean $byItemName
         *
		 * @return array
		 */
		public function getMessages($byItemName = null)
        {
            if (is_null($byItemName)) {
                $byItemName = false;
            }

            if ($byItemName) {
                if (!is_array($this->_messages)) {
                    $group = new \Phalcon\Validation\Message\Group;

                    return $group;
                }

                return $this->_messages;
            }

            $group = new \Phalcon\Validation\Message\Group;

            if ($this->_messages && is_array($this->_messages)) {
                foreach ($this->_messages as $elementMessages) {
                    $group->appendMessages($elementMessages);
                }
            }

            return $group;
        }


		/**
		 * Returns the messages generated by
		 *
		 * @return \Phalcon\Validation\Message\Group[]
		 */
		public function getMessagesFor($name)
        {
            if (array_key_exists($name, $this->_messages)) {
                return $this->_messages[$name];
            }

            $group = new \Phalcon\Validation\Message\Group;

            return $group;
        }


		/**
		 * Adds an element to the form
		 *
		 * @param \Phalcon\Forms\ElementInterface $element
         *
		 * @return \Phalcon\Forms\Form
		 */
		public function add($element)
        {
            if (!is_object($element)) {
                throw new \Phalcon\Forms\Exception('The element is not valid');
            }

            /**
             * Gets the element's name
             */
            $name = $element->getName();

            /**
             * Link the element to the form
             */
            $element->setForm($this);

            /**
             * Append the element by its name
             */
            $this->_elements[$name] = $element;

            return $this;
        }


		/**
		 * Renders an specific item in the form
		 *
		 * @param string $name
		 * @param array $attributes
         *
		 * @return string
		 */
		public function render($name, $attributes = null)
        {
            if (!array_key_exists($name, $this->_elements)) {
                throw new \Phalcon\Forms\Exception('Element with ID=' . $name . ' is not part of the form');
            }

            $element = $this->_elements[$name];

            $code = $element->render($attributes);

            return $code;
        }


		/**
		 * Returns an element added to the form by its name
		 *
		 * @param string $name
         *
		 * @return \Phalcon\Forms\ElementInterface
		 */
		public function get($name)
        {
            if (!array_key_exists($name, $this->_elements)) {
                throw new \Phalcon\Forms\Exception('Element with ID=' . $name . ' is not part of the form');
            }

            return $this->_elements[$name];
        }


		/**
		 * Generate the label of a element added to the form including HTML
		 *
		 * @param string $name
		 * @param array $attributes
         *
		 * @return string
		 */
		public function label($name)
        {
            if (!array_key_exists($name, $this->_elements)) {
                throw new \Phalcon\Forms\Exception('Element with ID=' . $name . ' is not part of the form');
            }

            $element = $this->_elements[$name];

            $label = $element->getLabel();

            if (is_null($label)) {
                $html = '<label for="' . $name . '">' . $name . '</label>';
            } else {
                $html = '<label for="' . $name . '">' . $label . '</label>';
            }

            return $html;
        }


		/**
		 * Returns the label
		 *
		 * @param string $name
         *
		 * @return string
		 */
		public function getLabel($name)
        {
            if (!array_key_exists($name, $this->_elements)) {
                throw new \Phalcon\Forms\Exception('Element with ID=' . $name . ' is not part of the form');
            }

            $element = $this->_elements[$name];

            $label = $element->getLabel();

            return $label;
        }


		/**
		 * Gets a value from the the internal related entity or from the default value
		 *
		 * @param string $name
         *
		 * @return mixed
		 */
		public function getValue($name)
        {
            if (is_object($this->_entity)) {
                /**
                 * Check if the entity has a getter
                 */
                $method = 'get' . ucfirst($name);

                if (method_exists($this->_entity, $method)) {
                    return $this->_entity->$method();
                }

                /**
                 * Check if the entity has a public property
                 */
                $reflection = new \ReflectionObject($this->_entity);

                if ($reflection->hasProperty($name)) {
                    $property = $reflection->getProperty($name);

                    if ($property->isPublic()) {
                        return $this->_entity->$name;
                    }
                }
            }

            if (is_array($this->_data)) {
                /**
                 * Check if the data is in the data array
                 */
                if (array_key_exists($name, $this->_data)) {
                    return $this->_data[$name];
                }
            }

            return null;
        }


        /**
         * Check if the form contains an element
         *
         * @param string $name
         *
         * @return boolean
         */
        public function has($name)
        {
            return array_key_exists($name, $this->_elements);
        }


        /**
         * Removes an element from the form
         *
         * @param string $name
         *
         * @return boolean
         */
        public function remove($name)
        {
            if (array_key_exists($name, $this->_elements)) {
                unset($this->_elements[$name]);

                return true;
            }

            return false;
        }


		/**
		 * Returns the number of elements in the form
		 *
		 * @return int
		 */
		public function count()
        {
            return count($this->_elements);
        }


		/**
		 * Rewinds the internal iterator
		 */
		public function rewind()
        {
            $this->_position = 0;
            $this->_elementsIndexed = array_values($this->_elements);
        }


		/**
		 * Returns the current element in the iterator
		 *
		 * @return \Phalcon\Validation\Message
		 */
		public function current()
        {
            if (array_key_exists($this->_position, $this->_elementsIndexed)) {
                return $this->_elementsIndexed[$this->_position];
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
		 * Check if the current element in the iterator is valid
		 *
		 * @return boolean
		 */
		public function valid()
        {
            return array_key_exists($this->_position, $this->_elementsIndexed);
        }

	}
}
