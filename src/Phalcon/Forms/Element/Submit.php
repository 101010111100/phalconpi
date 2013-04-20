<?php 

namespace Phalcon\Forms\Element {

	/**
	 * Phalcon\Forms\Element\Submit
	 *
	 * Component INPUT[type=submit] for forms
	 */
	
	class Submit extends \Phalcon\Forms\Element {

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

            // FIXME: In CPhalcon — submitField is used, which does not exist
            $code = \Phalcon\Tag::submitButton($widgetAttributes);

            return $code;
        }

	}
}
