<?php 

namespace Phalcon\Validation\Validator {

	/**
	 * Phalcon\Validation\Validator\PresenceOf
	 *
	 * Validates that a value is not null or empty string
	 *
	 *<code>
	 *use Phalcon\Validation\Validator\PresenceOf;
	 *
	 *$validator->add('name', new PresenceOf(array(
	 *   'message' => 'The name is required'
	 *)));
	 *</code>
	 */
	
	class PresenceOf extends \Phalcon\Validation\Validator implements \Phalcon\Validation\ValidatorInterface {

		/**
		 * Executes the validation
		 *
		 * @param \Phalcon\Validation $validator
		 * @param string $attribute
         *
		 * @return boolean
		 */
		public function validate($validator, $attribute)
        {
            $value = $validator->getValue($attribute);

            if (function_exists('mb_strlen')) {
                $length = mb_strlen($value);
            } else {
                $length = strlen($value);
            }

            if (is_null($value) || (is_string($value) && 0 === $length)) {
                $messageStr = $this->getOption('message');

                if (!$messageStr) {
                    $messageStr = $attribute . ' is required';
                }

                $message = new \Phalcon\Validation\Message($messageStr, $attribute, 'PresenceOf');
                $validator->appendMessage($message);

                return false;
            }

            return true;
        }

	}
}
