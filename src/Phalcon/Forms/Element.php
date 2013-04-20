<?php 

namespace Phalcon\Forms {

	/**
	 * Phalcon\Forms\Element
	 *
	 * This is a base class for form elements
	 */
	
	abstract class Element {

		protected $_form;

		protected $_name;

		protected $_label;

		protected $_attributes;

		protected $_validators;

		/**
		 * \Phalcon\Forms\Element constructor
		 *
		 * @param string $name
		 * @param array $attributes
		 */
		public function __construct($name, $attributes = null)
        {
            if (!is_string($name)) {
                throw new \Phalcon\Forms\Exception('The element\'s name must be a string');
            }

            $this->_name = $name;

            if (is_array($attributes)) {
                $this->_attributes = $attributes;
            }
        }


		/**
		 * Sets the parent form to the element
		 *
		 * @param \Phalcon\Forms\Form $form
         *
		 * @return \Phalcon\Forms\ElementInterface
		 */
		public function setForm($form)
        {
            $this->_form = $form;

            return $this;
        }


		/**
		 * Returns the parent form to the element
		 *
		 * @return \Phalcon\Forms\ElementInterface
		 */
		public function getForm()
        {
            return $this->_form;
        }


		/**
		 * Sets the element's name
		 *
		 * @param string $name
         *
		 * @return \Phalcon\Forms\ElementInterface
		 */
		public function setName($name)
        {
            $this->_name = $name;

            return $this;
        }


		/**
		 * Returns the element's name
		 *
		 * @return string
		 */
		public function getName()
        {
            return $this->_name;
        }


		/**
		 * Adds a group of validators
		 *
		 * @param \Phalcon\Validation\ValidatorInterface[]
         *
		 * @return \Phalcon\Forms\ElementInterface
		 */
		public function addValidators($validators, $merge = null)
        {
            if (is_null($merge)) {
                $merge = true;
            }

            if (!is_array($validators)) {
                throw new \Phalcon\Forms\Exception('The validators parameter must be an array');
            }

            if ($merge) {
                if (is_array($this->_validators)) {
                    $mergedValidators = array_merge($this->_validators, $validators);
                } else {
                    $mergedValidators = $validators;
                }

                $this->_validators = $mergedValidators;
            }

            return $this;
        }


		/**
		 * Adds a validator to the element
		 *
		 * @param \Phalcon\Validation\ValidatorInterface
         *
		 * @return \Phalcon\Forms\ElementInterface
		 */
		public function addValidator($validator)
        {
            if (!is_object($validator)) {
                throw new \Phalcon\Forms\Exception('The validators parameter must be an object');
            }

            $this->_validators[] = $validator;

            return $this;
        }


		/**
		 * Returns the validators registered for the element
		 *
		 * @return \Phalcon\Validation\ValidatorInterface[]
		 */
		public function getValidators()
        {
            return $this->_validators;
        }


		/**
		 * Returns an array of attributes for \Phalcon\Tag helpers prepared
		 * according to the element's parameters
		 *
		 * @param array $attributes
         *
		 * @return array
		 */
		public function prepareAttributes($attributes)
        {
            /**
             * Create an array of parameters
             */
            if (!is_array($this->_attributes)) {
                $widgetAttributes = $attributes;
            } else {
                $widgetAttributes = array();
            }

            $widgetAttributes[0] = $this->_name;

            /**
             * Merge passed parameters with default ones
             */
            if (is_array($this->_attributes)) {
                $mergedAttributes = array_merge($widgetAttributes, $this->_attributes);
            } else {
                $mergedAttributes = $widgetAttributes;
            }

            /**
             * Get the related form
             */
            if (is_object($this->_form)) {
                /**
                 * Check if the tag has a default value
                 */
                $hasDefaultValue = \Phalcon\Tag::hasValue($this->_name);

                if (false === $hasDefaultValue) {
                    /**
                     * Gets the possible value for the widget
                     */
                    $value = $this->_form->getValue($this->_name);

                    /**
                     * If the widget has a value assign it to the attributes
                     */
                    $mergedAttributes['value'] = $value;
                }
            }

            return $mergedAttributes;
        }


		/**
		 * Sets a default attribute for the element
		 *
		 * @param string $attribute
		 * @param mixed $value
         *
		 * @return \Phalcon\Forms\ElementInterface
		 */
		public function setAttribute($attribute, $value)
        {
            // FIXME: In CPhalcon — there is a typo
            $this->_attributes[$attribute] = $value;

            return $this;
        }


		/**
		 * Sets default attributes for the element
		 *
		 * @param array $attributes
         *
		 * @return \Phalcon\Forms\ElementInterface
		 */
		public function setAttributes($attributes)
        {
            $this->_attributes = $attributes;

            return $this;
        }


		/**
		 * Returns the default attributes for the element
		 *
		 * @return array
		 */
		public function getAttributes()
        {
            return $this->_attributes;
        }


		/**
		 * Sets the element label
		 *
		 * @param string $label
         *
		 * @return \Phalcon\Forms\ElementInterface
		 */
		public function setLabel($label)
        {
            $this->_label = $label;

            return $this;
        }


		/**
		 * Returns the element's label
		 *
		 * @return string
		 */
		public function getLabel()
        {
            return $this->_label;
        }


		/**
		 * Magic method __toString renders the widget without atttributes
		 *
		 * @return string
		 */
		public function __toString()
        {
            return $this->render();
        }

	}
}
