<?php 

namespace Phalcon\Validation\Validator {

	/**
	 * Phalcon\Validation\Validator\Identical
	 *
	 * Checks if a value is identical to other
	 */
	
	class Identical extends \Phalcon\Validation\Validator implements \Phalcon\Validation\ValidatorInterface {

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
            $identicalValue = $this->getOption('value');

            if ($value !== $identicalValue) {
                $messageStr = $this->getOption('message');

                if (!$messageStr) {
                    $messageStr = $attribute . ' does not have the expected value';
                }

                $message = new \Phalcon\Validation\Message($messageStr, $attribute, 'Identical');
                $validator->appendMessage($message);

                return false;
            }

            return true;
        }

	}
}
