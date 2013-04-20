<?php 

namespace Phalcon\Validation\Validator {

	/**
	 * Phalcon\Validation\Validator\InclusionIn
	 *
	 * Check if a value is included into a list of values
	 *
	 *<code>
	 *use Phalcon\Validation\Validator\InclusionIn;
	 *
	 *$validator->add('status', new InclusionIn(array(
	 *   'message' => 'The status must be A or B'
	 *   'domain' => array('A', 'B')
	 *)));
	 *</code>
	 */
	
	class InclusionIn extends \Phalcon\Validation\Validator implements \Phalcon\Validation\ValidatorInterface {

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

            $domain = $this->getOption('domain');

            /**
             * A domain is an array with a list of valid values
             */
            if (!is_array($domain)) {
                throw new \Phalcon\Validation\Exception('Option "domain" must be an array');
            }

            /**
             * Check if the value is not contained by the array
             */
            if (!in_array($value, $domain)) {
                $messageStr = $this->getOption('message');

                if (!$messageStr) {
                    $joinedDomain = implode(', ', $domain);
                    $messageStr = 'Value of field "' . $attribute . '" must be part of list: ' . $joinedDomain;
                }

                $message = new \Phalcon\Validation\Message($messageStr, $attribute, 'InclusionIn');
                $validator->appendMessage($message);

                return false;
            }

            return true;
        }

	}
}
