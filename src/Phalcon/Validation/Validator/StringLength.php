<?php 

namespace Phalcon\Validation\Validator {

	/**
	 * Phalcon\Validation\Validator\StringLength
	 *
	 * Validates that a string has the specified maximum and minimum constraints
	 *
	 *<code>
	 *use Phalcon\Validation\Validator\StringLength as StringLength;
	 *
	 *$validation->validate('name_last', new StringLength(array(
	 *	'max' => 50,
	 *	'min' => 2,
	 *	'messageMaximum' => 'We don't like really long names',
	 *	'messageMinimum' => 'We want more than just their initials'
	 *)));
	 *</code>
	 *
	 */
	
	class StringLength extends \Phalcon\Validation\Validator implements \Phalcon\Validation\ValidatorInterface {

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

            /**
             * At least one of 'min' or 'max' must be set
             */
            if (!$this->isSetOption('min') && !$this->isSetOption('max')) {
                throw new \Phalcon\Validation\Exception('A minimum or maximum must be set');
            }

            /**
             * Check if mbstring is available to calculate the correct length
             */
            if (function_exists('mb_strlen')) {
                $length = mb_strlen($value);
            } else {
                $length = strlen($value);
            }

            /**
             * Maximum length
             */
            if ($this->isSetOption('max')) {
                $maximum = $this->getOption('max');

                if ($length > $maximum) {
                    $messageStr = $this->getOption('messageMaximum');

                    if (!$messageStr) {
                        $messageStr = 'Value of field "' . $attribute . '" exceeds the maximum ' . $maximum . ' characters';
                    }

                    $message = new \Phalcon\Validation\Message($messageStr, $attribute, 'TooLong');
                    $validator->appendMessage($message);

                    return false;
                }
            }

            /**
             * Minimum length
             */
            if ($this->isSetOption('min')) {
                $minimum = $this->getOption('min');

                if ($length < $minimum) {
                    $messageStr = $this->getOption('messageMinimum');

                    if (!$messageStr) {
                        $messageStr = 'Value of field "' . $attribute . '" is less than the minimum ' . $minimum . ' characters';
                    }

                    $message = new \Phalcon\Validation\Message($messageStr, $attribute, 'TooShort');
                    $validator->appendMessage($message);

                    return false;
                }
            }

            return true;
        }

	}
}
