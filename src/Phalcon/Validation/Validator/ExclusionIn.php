<?php 

namespace Phalcon\Validation\Validator {

	/**
	 * Phalcon\Validation\Validator\ExclusionIn
	 *
	 * Check if a value is not included into a list of values
	 *
	 *<code>
	 *use Phalcon\Validation\Validator\ExclusionIn;
	 *
	 *$validator->add('status', new ExclusionIn(array(
	 *   'message' => 'The status must not be A or B'
	 *   'domain' => array('A', 'B')
	 *)));
	 *</code>
	 */
	
	class ExclusionIn extends \Phalcon\Validation\Validator implements \Phalcon\Validation\ValidatorInterface {

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
             * Check if the value is contained by the array
             */
            if (in_array($value, $domain)) {
                $messageStr = $this->getOption('message');

                if (!$messageStr) {
                    $joinedDomain = implode(', ', $domain);

                    // FIXME: In CPhalcon — seems to be a copy-paste from InclusionIn, there is a logic mistake in a message
                    $messageStr = 'Value of field "' . $attribute . '" must not be part of list: ' . $joinedDomain;
                }

                $message = new \Phalcon\Validation\Message($messageStr, $attribute, 'ExclusionIn');
                $validator->appendMessage($message);

                return false;
            }

            return true;
        }

	}
}
