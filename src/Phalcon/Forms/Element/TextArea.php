<?php 

namespace Phalcon\Forms\Element {

	/**
	 * Phalcon\Forms\Element\TextArea
	 *
	 * Component TEXTAREA for forms
	 */
	
	class TextArea extends \Phalcon\Forms\Element {

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
            $code = \Phalcon\Tag::textArea($widgetAttributes);

            return $code;
        }

	}
}
