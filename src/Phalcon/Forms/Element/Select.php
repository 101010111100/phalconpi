<?php 

namespace Phalcon\Forms\Element {

	/**
	 * Phalcon\Forms\Element\Select
	 *
	 * Component SELECT (choice) for forms
	 */
	
	class Select extends \Phalcon\Forms\Element {

		protected $_options;

		/**
		 * \Phalcon\Forms\Element constructor
		 *
		 * @param string $name
		 * @param object|array $options
		 * @param array $attributes
		 */
		public function __construct($name, $options = null, $attributes = null)
        {
            $this->_options = $options;

            parent::__construct($name, $attributes);
        }


		/**
		 * Set the choice's options
		 *
		 * @param array|object $options
         *
		 * @return \Phalcon\Forms\Element
		 */
		public function setOptions($options)
        {
            $this->_options = $options;

            return $this;
        }


		/**
		 * Returns the choices' options
		 *
		 * @return array|object
		 */
		public function getOptions()
        {
            return $this->_options;
        }


		/**
		 * Adds an option to the current options
		 *
		 * @param array $option
         *
		 * @return $this;
		 */
		public function addOption($option)
        {
            $this->_options[] = $option;

            return $this;
        }


		/**
		 * Renders the element widget
		 *
		 * @param array $attributes
         *
		 * @return string
		 */
		public function render($attributes = null)
        {
            $widgetAttributes = $this->prepareAttributes($attributes);
            $code = \Phalcon\Tag\Select::selectField($widgetAttributes, $this->_options);

            return $code;
        }

	}
}
