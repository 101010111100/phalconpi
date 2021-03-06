<?php 

namespace Phalcon\Forms\Element {

	/**
	 * Phalcon\Forms\Element\File
	 *
	 * Component INPUT[type=file] for forms
	 */
	
	class File extends \Phalcon\Forms\Element {

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
            $code = \Phalcon\Tag::fileField($widgetAttributes);

            return $code;
        }

	}
}
