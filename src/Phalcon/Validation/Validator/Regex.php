<?php 

namespace Phalcon\Validation\Validator {

	/**
	 * Phalcon\Validation\Validator\Regex
	 *
	 * Allows validate if the value of a field matches a regular expression
	 *
	 *<code>
	 *use Phalcon\Validation\Validator\Regex as RegexValidator;
	 *
	 *$validator->add('created_at', new RegexValidator(array(
	 *   'pattern' => '/^[0-9]{4}[-\/](0[1-9]|1[12])[-\/](0[1-9]|[12][0-9]|3[01])$/',
	 *   'message' => 'The creation date is invalid'
	 *)));
	 *</code>
	 */
	
	class Regex extends \Phalcon\Validation\Validator implements \Phalcon\Validation\ValidatorInterface {

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
             * The regular expression is set in the option 'pattern'
             */
            $pattern = $this->getOption('pattern');

            // FIXME: In CPhalcon we need to throw an Exception if a pattern option is not set
            if (!$pattern) {
                throw new \Phalcon\Validation\Exception('Option "pattern" must be set');
            }

            preg_match($pattern, $value, $matches);

            if ($matches) {
                $failed = $value !== $matches[0];
            } else {
                $failed = true;
            }

            if ($failed) {
                $messageStr = $this->getOption('message');

                if (!$messageStr) {
                    $messageStr = 'Value of field "' . $attribute . '" doesn\'t match regular expression';
                }

                $message = new \Phalcon\Validation\Message($messageStr, $attribute, 'Regex');
                $validator->appendMessage($message);

                return false;
            }

            return true;
        }

	}
}
