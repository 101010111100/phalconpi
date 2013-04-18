<?php 

namespace Phalcon {

	/**
	 * Phalcon\Text
	 *
	 * Provides utilities when working with strings
	 */
	
	abstract class Text {

		const RANDOM_ALNUM = 0;

		const RANDOM_ALPHA = 1;

		const RANDOM_HEXDEC = 2;

		const RANDOM_NUMERIC = 3;

		const RANDOM_NOZERO = 4;

		/**
		 * Converts strings to camelize style
		 *
		 *<code>
		 *	echo \Phalcon\Text::camelize('coco_bongo'); //CocoBongo
		 *</code>
		 *
		 * @param string $str
         *
		 * @return string
		 */
		public static function camelize($str)
        {
            if (!is_string($str)) {
                throw new \Exception('Invalid arguments supplied for Text::camelize()');
            }

            $str = trim($str);
            $str = preg_replace('@[_-]+@', '_', $str);
            $str = str_replace(' ', '_', $str);

            if (function_exists('mb_strlen')) {
                $length = mb_strlen($str);
            } else {
                $length = strlen($str);
            }

            $camelized = '';

            for ($i = 0; $i < $length; $i++) {
                if ('_' == $str[$i] && $i + 1 < $length) {
                    $camelized .= \Phalcon\Text::upper($str[++$i]);
                } else {
                    $camelized .= $str[$i];
                }
            }

            $camelized = ucfirst($camelized);

            return trim($camelized, ' _');
        }


		/**
		 * Uncamelize strings which are camelized
		 *
		 *<code>
		 *	echo \Phalcon\Text::camelize('CocoBongo'); //coco_bongo
		 *</code>
		 *
		 * @param string $str
		 * @return string
		 */
		public static function uncamelize($str)
        {
            $uncamelized = '';

            if (function_exists('mb_strlen')) {
                $length = mb_strlen($str);
            } else {
                $length = strlen($str);
            }

            for ($i = 0; $i < $length; $i++) {
                $upper = \Phalcon\Text::upper($str[$i]);

                if (ctype_alpha($str[$i]) && $upper === $str[$i]) {
                    $uncamelized .= '_' . strtolower($str[$i]);
                } else {
                    $uncamelized .= $str[$i];
                }
            }

            return trim($uncamelized, ' _');
        }


		/**
		 * Adds a number to a string or increment that number if it already is defined
		 *
		 *<code>
		 *	echo \Phalcon\Text::increment("a"); // "a_1"
		 *  echo \Phalcon\Text::increment("a_1"); // "a_2"
		 *</code>
		 *
		 * @param string $str
		 * @param string $separator
         *
		 * @return string
		 */
		public static function increment($str, $separator = null)
        {
            if (is_null($separator)) {
                $separator = '_';
            }

            $parts = explode($separator, $str);
            $incrementable = end($parts);

            if (is_numeric($incrementable)) {
                $number = $incrementable + 1;

                array_pop($parts);
            } else {
                $number = 1;
            }

            $parts[] = $number;

            $incremented = implode('_', $parts);

            return $incremented;
        }


		/**
		 * Generates a random string based on the given type. Type is one of the RANDOM_* constants
		 *
		 *<code>
		 *	echo \Phalcon\Text::random(Phalcon\Text::RANDOM_ALNUM); //"aloiwkqz"
		 *</code>
		 *
		 * @param int $type
		 * @param int $length
         *
		 * @return string
		 */
		public static function random($type, $length = null)
        {
            if (is_null($length)) {
                $length = 8;
            }

            $numeric = range(0, 9);
            $hexdec = array_merge($numeric, range('a', 'f'));
            $lowerAlpha = range('a', 'z');
            $upperAlpha = range('A', 'Z');
            $nozero = range(1, 9);
            $alpha = array_merge($lowerAlpha, $upperAlpha);
            $alphaNum = array_merge($numeric, $alpha);

            $randomStr = '';

            for ($i = 0; $i < $length; $i++) {
                switch ($type) {
                    case self::RANDOM_ALNUM:
                        $randomStr .= $alphaNum[array_rand($alphaNum)];
                    break;

                    case self::RANDOM_ALPHA:
                        $randomStr .= $alpha[array_rand($alpha)];
                    break;

                    case self::RANDOM_HEXDEC:
                        $randomStr .= $hexdec[array_rand($hexdec)];
                    break;

                    case self::RANDOM_NUMERIC:
                        $randomStr .= $numeric[array_rand($numeric)];
                    break;

                    case self::RANDOM_NOZERO:
                        $randomStr .= $nozero[array_rand($nozero)];
                    break;
                }
            }

            return $randomStr;
        }


		/**
		 * Check if a string starts with a given string
		 *
		 *<code>
		 *	echo \Phalcon\Text::startsWith("Hello", "He"); // true
		 *  echo \Phalcon\Text::startsWith("Hello", "he"); // false
		 *  echo \Phalcon\Text::startsWith("Hello", "he", false); // true
		 *</code>
		 *
		 * @param string $str
		 * @param string $start
		 * @param boolean $ignoreCase
		 */
		public static function startsWith($str, $start, $ignoreCase = null)
        {
            if (is_null($ignoreCase)) {
                $ignoreCase = true;
            }

            if ($ignoreCase) {
                $regExp = '@^' . $start . '@ui';
            } else {
                $regExp = '@^' . $start . '@u';
            }

            return preg_match($regExp, $str);
        }


		/**
		 * Check if a string ends with a given string
		 *
		 *<code>
		 *	echo \Phalcon\Text::endsWith("Hello", "llo"); // true
		 *  echo \Phalcon\Text::endsWith("Hello", "LLO"); // false
		 *  echo \Phalcon\Text::endsWith("Hello", "LLO", false); // true
		 *</code>
		 *
		 * @param string $str
		 * @param string $end
		 * @param boolean $ignoreCase
		 */
		public static function endsWith($str, $end, $ignoreCase = null)
        {
            if (is_null($ignoreCase)) {
                $ignoreCase = true;
            }

            if ($ignoreCase) {
                $regExp = '@' . $end . '$@ui';
            } else {
                $regExp = '@' . $end . '$@u';
            }

            return preg_match($regExp, $str);
        }


		/**
		 * Lowecases a string, this function make use of the mbstring extension if available
		 *
		 * @param string $str
         *
		 * @return string
		 */
		public static function lower($str)
        {
            /**
             * 'lower' checks for the mbstring extension to make a correct lowercase
             * transformation
             */
            if (function_exists('mb_strtolower')) {
                return mb_strtolower($str);
            } else {
                return strtolower($str);
            }
        }


		/**
		 * Uppercases a string, this function make use of the mbstring extension if available
		 *
		 * @param string $str
		 * @return string
		 */
		public static function upper($str)
        {
            /**
             * 'upper' checks for the mbstring extension to make a correct lowercase
             * transformation
             */
            if (function_exists('mb_strtoupper')) {
                return mb_strtoupper($str);
            } else {
                return strtoupper($str);
            }
        }

	}
}
