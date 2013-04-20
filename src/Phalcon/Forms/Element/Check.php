<?php 

namespace Phalcon\Forms\Element {

	/**
	 * Phalcon\Forms\Element\Check
	 *
	 * Component INPUT[type=check] for forms
	 */
	
	class Check extends \Phalcon\Forms\Element {

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
            $code = \Phalcon\Tag::checkField($widgetAttributes);

            return $code;
        }

	}
}
