<?php 

namespace Phalcon\Forms\Element {

	/**
	 * Phalcon\Forms\Element\Password
	 *
	 * Component INPUT[type=password] for forms
	 */
	
	class Password extends \Phalcon\Forms\Element {

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
            $code = \Phalcon\Tag::passwordField($widgetAttributes);

            return $code;
        }

	}
}
