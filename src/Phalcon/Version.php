<?php 

namespace Phalcon {

	/**
	 * Phalcon\Version
	 *
	 * This class allows to get the installed version of the framework
	 */
	
	class Version {

		/**
		 * Area where the version number is set. The format is as follows:
		 * ABBCCDE
		 *
		 * A - Major version
		 * B - Med version (two digits)
		 * C - Min version (two digits)
		 * D - Special release: 1 = Alpha, 2 = Beta, 3 = RC, 4 = Stable
		 * E - Special release version i.e. RC1, Beta2 etc.
		 */
		protected static function _getVersion()
        {
            $version = array();

            $version[] = 1;
            $version[] = 0;
            $version[] = 0;
            $version[] = 4;
            $version[] = 0;

            return $version;
        }


		/**
		 * Returns the active version (string)
		 *
		 * <code>
		 * echo \Phalcon\Version::get();
		 * </code>
		 *
		 * @return string
		 */
		public static function get()
        {
            $version = self::_getVersion();

            list ($major, $medium, $minor, $special, $specialNumber) = $version;

            $result = $major . '.' . $medium . '.' . $minor . ' ';

            switch ($special)
            {
                case 1:
                    $suffix = 'ALPHA ' . $specialNumber;
                    break;

                case 2:
                    $suffix = 'BETA ' . $specialNumber;
                    break;

                case 3:
                    $suffix = 'RC ' . $specialNumber;
                    break;

                default:
                    $suffix = '';
                    break;
            }

            $result .= $suffix;

            $finalVersion = trim($result);

            return $finalVersion;
        }


		/**
		 * Returns the numeric active version
		 *
		 * <code>
		 * echo \Phalcon\Version::getId();
		 * </code>
		 *
		 * @return int
		 */
		public static function getId()
        {
            $version = self::_getVersion();

            list ($major, $medium, $minor, $special, $specialNumber) = $version;

            $realMedium = sprintf('%02s', $medium);
            $realMinor = sprintf('%02s', $minor);

            $version = $major . $realMedium . $realMinor . $special . $specialNumber;

            return $version;
        }

	}
}
