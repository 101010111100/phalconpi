<?php 

namespace Phalcon\Forms\Element {

	/**
	 * Phalcon\Forms\Element\Text
	 *
	 * Component INPUT[type=text] for forms
	 */
	
	class Text extends \Phalcon\Forms\Element {

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
            $code = \Phalcon\Tag::textField($widgetAttributes);

            return $code;
        }

	}
}
