<?php 

namespace Phalcon\Tag {

	/**
	 * Phalcon\Tag\Select
	 *
	 * Generates a SELECT html tag using a static array of values or a Phalcon\Mvc\Model resultset
	 */
	
	abstract class Select {

		/**
		 * Generates a SELECT tag
		 *
		 * @param array $parameters
		 * @param array $data
		 */
		public static function selectField($parameters, $data = null)
        {
            $params = array();

            if (!is_array($parameters)) {
                $params[] = $parameters;
                $params[] = $data;
            } else {
                $params = $parameters;
            }

            if (!array_key_exists(0, $params)) {
                $params[0] = $params['id'];
            }

            $id = $params[0];

            if (!array_key_exists('name', $params)) {
                $params['name'] = $id;
            } else {
                if (!$params['name']) {
                    $params['name'] = $id;
                }
            }

            if (!array_key_exists('id', $params)) {
                $params['id'] = $id;
            }

            if (!array_key_exists('value', $params)) {
                $value = self::getValue($id, $params);
            } else {
                $value = $params['value'];

                unset($params['value']);
            }

            $useEmpty = false;

            if (array_key_exists('useEmpty', $params)) {
                $emptyValue = '';

                if (array_key_exists('emptyValue', $params)) {
                    $emptyValue = $params['emptyValue'];
                }

                $emptyText = 'Choose…';

                if (array_key_exists('emptyText', $params)) {
                    $emptyText = $params['emptyText'];
                }

                $useEmpty = $params['useEmpty'];

                unset($params['useEmpty']);
            }

            $code = '<select';

            if ($params) {
                foreach ($params as $key => $value) {
                    if (!is_long($key)) {
                        $code .= ' ' . $key . '="' . $value . '"';
                    }
                }
            }

            $code .= '>';

            if ($useEmpty) {
                /**
                 * Create an empty value
                 */
                $code .= '<option value="' . $emptyValue . '">' . $emptyText . '</option>';
            }

            if (array_key_exists(1, $params)) {
                $options = $params[1];
            } else {
                $options = $data;
            }

            if (is_object($options)) {
                /**
                 * The options is a resultset
                 */
                if (!array_key_exists('using', $params)) {
                    throw new \Phalcon\Tag\Exception('The "using" parameter is required');
                } else {
                    if (!is_array($params['using'])) {
                        throw new \Phalcon\Tag\Exception('The "using" parameter should be an Array');
                    }
                }

                /**
                 * Create the SELECT's option from a resultset
                 */
                $code .= self::_optionsFromResultset($options, $params['using'], $value, '</option>');
            } else {
                if (is_array($options)) {
                    /**
                     * Create the SELECT's option from an array
                     */
                    $code .= self::_optionsFromArray($options, $value, '</option>');
                } else {
                    throw new \Phalcon\Tag\Exception('Invalid data provided to SELECT helper');
                }
            }

            $code .= '</select>';

            return $code;
        }


		/**
		 * Generate the OPTION tags based on a resulset
		 *
		 * @param \Phalcon\Mvc\Model $resultset
		 * @param array $using
		 * @param mixed value
		 * @param string $closeOption
		 */
		protected static function _optionsFromResultset($resultset, $using, $value, $closeOption)
        {
            $code = '';

            $resultset->rewind();

            while ($option = $resultset->next()) {
                /**
                 * Read the value attribute from the model
                 */
                $optionValue = $option->readAttribute($using[0]);

                /**
                 * Read the text attribute from the model
                 */
                $optionText = $option->readAttribute($using[1]);

                /**
                 * If the value is equal to the option's value we mark it as selected
                 */
                if (is_array($value)) {
                    if (in_array($optionValue, $value)) {
                        $code .= '<option selected="selected" value="' . $optionValue . '">' . $optionText . $closeOption;
                    } else {
                        $code .= '<option value="' . $optionValue . '">' . $optionText . $closeOption;
                    }
                } else {
                    if ($optionValue === $value) {
                        $code .= '<option selected="selected" value="' . $optionValue . '">' . $optionText . $closeOption;
                    } else {
                        $code .= '<option value="' . $optionValue . '">' . $optionText . $closeOption;
                    }
                }
            }

            return $code;
        }


		/**
		 * Generate the OPTION tags based on an array
		 *
		 * @param \Phalcon\Mvc\ModelInterface $resultset
		 * @param array $using
		 * @param mixed value
		 * @param string $closeOption
		 */
		protected static function _optionsFromArray($resultset, $value, $closeOption)
        {
            $code = '';

            foreach ($resultset as $optionValue => $optionText) {
                if (is_array($value)) {
                    if (in_array($optionValue, $value)) {
                        $code .= '<option selected="selected" value="' . $optionValue . '">' . $optionText . $closeOption;
                    } else {
                        $code .= '<option value="' . $optionValue . '">' . $optionText . $closeOption;
                    }
                } else {
                    if ($optionValue === $value) {
                        $code .= '<option selected="selected" value="' . $optionValue . '">' . $optionText . $closeOption;
                    } else {
                        $code .= '<option value="' . $optionValue . '">' . $optionText . $closeOption;
                    }
                }
            }

            return $code;
        }

	}
}
