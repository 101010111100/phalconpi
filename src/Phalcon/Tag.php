<?php 

namespace Phalcon {

	/**
	 * Phalcon\Tag
	 *
	 * Phalcon\Tag is designed to simplify building of HTML tags.
	 * It provides a set of helpers to generate HTML in a dynamic way.
	 * This component is an abstract class that you can extend to add more helpers.
	 */
	
	abstract class Tag {

		const HTML32 = 1;

		const HTML401_STRICT = 2;

		const HTML401_TRANSITIONAL = 3;

		const HTML401_FRAMESET = 4;

		const HTML5 = 5;

		const XHTML10_STRICT = 6;

		const XHTML10_TRANSITIONAL = 7;

		const XHTML10_FRAMESET = 8;

		const XHTML11 = 9;

		const XHTML20 = 10;

		const XHTML5 = 11;

		protected static $_displayValues;

		protected static $_documentTitle;

		protected static $_documentType = self::XHTML5;

		protected static $_dependencyInjector;

		protected static $_urlService;

		protected static $_dispatcherService;

		protected static $_escaperService;

		protected static $_autoEscape = true;

		/**
		 * Sets the dependency injector container.
		 *
		 * @param \Phalcon\DiInterface $dependencyInjector
		 */
		public static function setDI($dependencyInjector)
        {
            if (!is_object($dependencyInjector)) {
                throw new \Phalcon\Tag\Exception('Parameter dependencyInjector must be an Object');
            }

            self::$_dependencyInjector = $dependencyInjector;
        }


		/**
		 * Internally gets the request dispatcher
		 *
		 * @return \Phalcon\DiInterface
		 */
		public static function getDI()
        {
            return self::$_dependencyInjector;
        }


		/**
		 * Return a URL service from the default DI
		 *
		 * @return \Phalcon\Mvc\UrlInterface
		 */
		public static function getUrlService()
        {
            if (!is_object(self::$_urlService)) {
                $dependencyInjector = self::$_dependencyInjector;

                if (!is_object($dependencyInjector)) {
                    $dependencyInjector = \Phalcon\Di::getDefault();
                }

                if (!is_object($dependencyInjector)) {
                    throw new \Phalcon\Tag\Exception('A dependency injector container is required to obtain the "url" service');
                }

                self::$_urlService = $dependencyInjector->getShared('url');
            }

            return self::$_urlService;
        }


		/**
		 * Returns a Dispatcher service from the default DI
		 *
		 * @return \Phalcon\Mvc\DispatcherInterface
		 */
		public static function getDispatcherService()
        {
            if (!is_object(self::$_dispatcherService)) {
                $dependencyInjector = self::$_dependencyInjector;

                if (!is_object($dependencyInjector)) {
                    $dependencyInjector = \Phalcon\Di::getDefault();
                }

                if (!is_object($dependencyInjector)) {
                    throw new \Phalcon\Tag\Exception('A dependency injector container is required to obtain the "dispatcher" service');
                }

                self::$_dispatcherService = $dependencyInjector->getShared('dispatcher');
            }

            return self::$_dispatcherService;
        }


		/**
		 * Returns an Escaper service from the default DI
		 *
		 * @return \Phalcon\EscaperInterface
		 */
		public static function getEscaperService()
        {
            if (!is_object(self::$_escaperService)) {
                $dependencyInjector = self::$_dependencyInjector;

                if (!is_object($dependencyInjector)) {
                    $dependencyInjector = \Phalcon\Di::getDefault();
                }

                if (!is_object($dependencyInjector)) {
                    throw new \Phalcon\Tag\Exception('A dependency injector container is required to obtain the "escaper" service');
                }

                self::$_escaperService = $dependencyInjector->getShared('escaper');
            }

            return self::$_escaperService;
        }


		/**
		 * Set autoescape mode in generated html
		 *
		 * @param boolean $autoescape
		 */
		public static function setAutoescape($autoescape)
        {
            self::$_autoEscape = $autoescape;
        }


		/**
		 * Assigns default values to generated tags by helpers
		 *
		 * <code>
		 * //Assigning "peter" to "name" component
		 * \Phalcon\Tag::setDefault("name", "peter");
		 *
		 * //Later in the view
		 * echo \Phalcon\Tag::textField("name"); //Will have the value "peter" by default
		 * </code>
		 *
		 * @param string $id
		 * @param string $value
		 */
		public static function setDefault($id, $value)
        {
            if (!is_null($value) && !is_scalar($value)) {
                throw new \Phalcon\Tag\Exception('Only scalar values can be assigned to UI components');
            }

            if (!is_array(self::$_displayValues)) {
                self::$_displayValues = (array) self::$_displayValues;
            }

            self::$_displayValues[$id] = $value;
        }


		/**
		 * Assigns default values to generated tags by helpers
		 *
		 * <code>
		 * //Assigning "peter" to "name" component
		 * \Phalcon\Tag::setDefaults(array("name" => "peter"));
		 *
		 * //Later in the view
		 * echo \Phalcon\Tag::textField("name"); //Will have the value "peter" by default
		 * </code>
		 *
		 * @param array $values
		 */
		public static function setDefaults($values)
        {
            if (!is_array($values)) {
                throw new \Phalcon\Tag\Exception('An array is required as default values');
            }

            self::$_displayValues = $values;
        }


		/**
		 * Alias of \Phalcon\Tag::setDefault
		 *
		 * @param string $id
		 * @param string $value
		 */
		public static function displayTo($id, $value)
        {
            // FIXME: In CPhalcon — return is used, though self::setDefault returns void
            return self::setDefault($id, $value);
        }


		/**
		 * Check if a helper has a default value set using \Phalcon\Tag::setDefault or value from $_POST
		 *
		 * @param string $name
         *
		 * @return boolean
		 */
		public static function hasValue($name)
        {
            /**
             * Check if there is a predefined value for it
             */
            if (array_key_exists($name, self::$_displayValues)) {
                // FIXME: In CPhalcon — we fetch a value for the key in displayValues, but never used; looks like a copy-paste from getValue

                return true;
            } else {
                /**
                 * Check if there is a post value for the item
                 */
                if (array_key_exists($name, $_POST)) {
                    return true;
                }
            }

            return false;
        }


		/**
		 * Every helper calls this function to check whether a component has a predefined
		 * value using \Phalcon\Tag::setDefault or value from $_POST
		 *
		 * @param string $name
		 * @param array $params
         *
		 * @return mixed
		 */
		public static function getValue($name, $params = null)
        {
            /**
             * Check if there is a predefined value for it
             */
            if (array_key_exists($name, self::$_displayValues)) {
                $value = self::$_displayValues[$name];
            } else {
                /**
                 * Check if there is a post value for the item
                 */
                if (array_key_exists($name, $_POST)) {
                    $value = $_POST[$name];
                } else {
                    return null;
                }
            }

            /**
             * Escape all values in autoescape mode. Only escaping values
             */
            if (is_string($value)) {
                if (self::$_autoEscape) {
                    $escaper = self::getEscaperService();

                    return $escaper->escapeHtmlAttr($value);
                } else {
                    if (array_key_exists('escape', $params)) {
                        if (true === $params['escape']) {
                            $escaper = self::getEscaperService();

                            return $escaper->escapeHtmlAttr($value);
                        }
                    }
                }
            }

            return $value;
        }


		/**
		 * Resets the request and internal values to avoid those fields will have any default value
		 */
		public static function resetInput()
        {
            self::$_displayValues = $_POST = array();
        }


		/**
		 * Builds a HTML A tag using framework conventions
		 *
		 *<code>
		 *	echo \Phalcon\Tag::linkTo('signup/register', 'Register Here!');
		 *</code>
		 *
		 * @param array|string $parameters
		 * @param   string $text
         *
		 * @return string
		 */
		public static function linkTo($parameters, $text = null)
        {
            $params = array();

            if (!is_array($parameters)) {
                $params[] = $parameters;
                $params[] = $text;
            } else {
                $params = $parameters;
            }

            $action = '';

            if (array_key_exists(0, $params)) {
                $action = $params[0];
            } else {
                if (array_key_exists('action', $params)) {
                    $action = $params['action'];

                    unset($params['action']);
                }
            }

            $text = '';

            if (array_key_exists(1, $params)) {
                $text = $params[1];
            } else {
                if (array_key_exists('text', $params)) {
                    $text = $params['text'];

                    unset($params['text']);
                }
            }

            $urlService = self::getUrlService();

            $internalUrl = $urlService->get($action);

            $code = '<a href="' . $internalUrl . '"';

            if ($params) {
                foreach ($params as $key => $value) {
                    if (!is_long($key)) {
                        $code .= ' ' . $key . '="' . $value . '"';
                    }
                }
            }

            $code .= '>' . $text . '</a>';

            return $code;
        }


		/**
		 * Builds generic INPUT tags
		 *
		 * @param   string $type
		 * @param array $parameters
		 * @param 	boolean $asValue
         *
		 * @return string
		 */
		protected static function _inputField($type, $parameters, $asValue = null)
        {
            if (is_null($asValue)) {
                $asValue = false;
            }

            if (!is_array($parameters)) {
                $params = array($parameters);
            } else {
                $params = $parameters;
            }

            if (!$asValue) {
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
                    $params['value'] = self::getValue($id, $params);
                }
            } else {
                if (array_key_exists(0, $params)) {
                    $params['value'] = $params[0];
                }
            }

            // FIXME: In CPhalcon — we only check, if the type is checkbox, while radio also should have checked attribute

            /**
             * Automatically check inputs
             */
            if (in_array($type, array('radio', 'checkbox'))) {
                if ($params['value']) {
                    $params['checked'] = 'checked';
                }
            }

            $code = '<input type="' . $type . '"';

            if ($params) {
                foreach ($params as $key => $value) {
                    if (!is_long($key)) {
                        $code .= ' ' . $key . '="' . $value . '"';
                    }
                }
            }

            /**
             * Check if Doctype is XHTML
             */
            if (self::HTML5 < self::$_documentType) {
                $code .= ' />';
            } else {
                $code .= '>';
            }

            return $code;
        }


		/**
		 * Builds a HTML input[type="text"] tag
		 *
		 * <code>
		 *	echo \Phalcon\Tag::textField(array("name", "size" => 30))
		 * </code>
		 *
		 * @param array $parameters
         *
		 * @return string
		 */
		public static function textField($parameters)
        {
            return self::_inputField('text', $parameters);
        }


		/**
		 * Builds a HTML input[type="password"] tag
		 *
		 *<code>
		 * echo \Phalcon\Tag::passwordField(array("name", "size" => 30))
		 *</code>
		 *
		 * @param array $parameters
         *
		 * @return string
		 */
		public static function passwordField($parameters)
        {
            return self::_inputField('password', $parameters);
        }


		/**
		 * Builds a HTML input[type="hidden"] tag
		 *
		 *<code>
		 * echo \Phalcon\Tag::hiddenField(array("name", "value" => "mike"))
		 *</code>
		 *
		 * @param array $parameters
         *
		 * @return string
		 */
		public static function hiddenField($parameters)
        {
            return self::_inputField('hidden', $parameters);
        }


		/**
		 * Builds a HTML input[type="file"] tag
		 *
		 *<code>
		 * echo \Phalcon\Tag::fileField("file")
		 *</code>
		 *
		 * @param array $parameters
         *
		 * @return string
		 */
		public static function fileField($parameters)
        {
            return self::_inputField('file', $parameters);
        }


		/**
		 * Builds a HTML input[type="check"] tag
		 *
		 *<code>
		 * echo \Phalcon\Tag::checkField(array("name", "size" => 30))
		 *</code>
		 *
		 * @param array $parameters
         *
		 * @return string
		 */
		public static function checkField($parameters)
        {
            return self::_inputField('checkbox', $parameters);
        }


		/**
		 * Builds a HTML input[type="radio"] tag
		 *
		 *<code>
		 * echo \Phalcon\Tag::radioField(array("name", "size" => 30))
		 *</code>
		 *
		 * @param array $parameters
		 * @return string
		 */
		public static function radioField($parameters)
        {
            return self::_inputField('radio', $parameters);
        }


		/**
		 * Builds a HTML input[type="image"] tag
		 *
		 *<code>
		 * echo \Phalcon\Tag::imageInput(array("src" => "/img/button.png"));
		 *</code>
		 *
		 * @param array $parameters
         *
		 * @return string
		 */
		public static function imageInput($parameters)
        {
            return self::_inputField('image', $parameters, true);
        }


		/**
		 * Builds a HTML input[type="submit"] tag
		 *
		 *<code>
		 * echo \Phalcon\Tag::submitButton("Save")
		 *</code>
		 *
		 * @param array $parameters
         *
		 * @return string
		 */
		public static function submitButton($parameters)
        {
            return self::_inputField('submit', $parameters, true);
        }


		/**
		 * Builds a HTML SELECT tag using a PHP array for options
		 *
		 *<code>
		 *	echo \Phalcon\Tag::selectStatic("status", array("A" => "Active", "I" => "Inactive"))
		 *</code>
		 *
		 * @param array $parameters
		 * @param   array $data
         *
		 * @return string
		 */
		public static function selectStatic($parameters, $data = null)
        {
            return \Phalcon\Tag\Select::selectField($parameters, $data);
        }


		/**
		 * Builds a HTML SELECT tag using a \Phalcon\Mvc\Model resultset as options
		 *
		 *<code>
		 *	echo \Phalcon\Tag::selectStatic(array(
		 *		"robotId",
		 *		Robots::find("type = 'mechanical'"),
		 *		"using" => array("id", "name")
		 * 	));
		 *</code>
		 *
		 * @param array $parameters
		 * @param   array $data
         *
		 * @return string
		 */
		public static function select($parameters, $data = null)
        {
            return \Phalcon\Tag\Select::selectField($parameters, $data);
        }


		/**
		 * Builds a HTML TEXTAREA tag
		 *
		 *<code>
		 * echo \Phalcon\Tag::textArea(array("comments", "cols" => 10, "rows" => 4))
		 *</code>
		 *
		 * @param array $parameters
         *
		 * @return string
		 */
		public static function textArea($parameters)
        {
            if (!is_array($parameters)) {
                $params = array($parameters);
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

            if (array_key_exists('value', $params)) {
                $content = $params['value'];

                unset($params['value']);
            } else {
                $content = self::getValue($id, $params);
            }

            $code = '<textarea';

            if ($params) {
                foreach ($params as $key => $value) {
                    if (!is_long($key)) {
                        $code .= ' ' . $key . '="' . $value . '"';
                    }
                }
            }

            $code .= '>' . $content . '</textarea>';

            return $code;
        }


		/**
		 * Builds a HTML FORM tag
		 *
		 * <code>
		 * echo \Phalcon\Tag::form("posts/save");
		 * echo \Phalcon\Tag::form(array("posts/save", "method" => "post"));
		 * </code>
		 *
		 * Volt syntax:
		 * <code>
		 * {{ form("posts/save") }}
		 * {{ form("posts/save", "method": "post") }}
		 * </code>
		 *
		 * @param array $parameters
         *
		 * @return string
		 */
		public static function form($parameters = null)
        {
            if (!is_array($parameters)) {
                $params = array($parameters);
            } else {
                $params = $parameters;
            }

            $paramsAction = '';

            if (array_key_exists(0, $params)) {
                $paramsAction = $params[0];
            } else {
                if (array_key_exists('action', $params)) {
                    $paramsAction = $params['action'];
                }
            }

            /**
             * By default the method is POST
             */
            if (!array_key_exists('method', $params)) {
                $params['method'] = 'post';
            }

            if ($paramsAction) {
                $urlService = self::getUrlService();

                $params['action'] = $urlService->get($paramsAction);
            }

            /**
             * Check for extra parameters
             */
            if (array_key_exists('parameters', $params)) {
                // FIXME: In CPhalcon — here parameters are concatenated with action variable, and not params['action']

                $params['action'] .= '?' . $params['parameters'];
            }

            $code = '<form';

            if ($params) {
                foreach ($params as $key => $value) {
                    if (!is_long($key)) {
                        $code .= ' ' . $key . '="' . $value . '"';
                    }
                }
            }

            $code .= '>';

            return $code;
        }


		/**
		 * Builds a HTML close FORM tag
		 *
		 * @return string
		 */
		public static function endForm()
        {
            return '</form>';
        }


		/**
		 * Set the title of view content
		 *
		 *<code>
		 * \Phalcon\Tag::setTitle('Welcome to my Page');
		 *</code>
		 *
		 * @param string $title
		 */
		public static function setTitle($title)
        {
            self::$_documentTitle = $title;
        }


		/**
		 * Appends a text to current document title
		 *
		 * @param string $title
		 */
		public static function appendTitle($title)
        {
            self::$_documentTitle .= $title;
        }


		/**
		 * Prepends a text to current document title
		 *
		 * @param string $title
		 */
		public static function prependTitle($title)
        {
            self::$_documentTitle = $title . self::$_documentTitle;
        }


		/**
		 * Gets the current document title
		 *
		 * <code>
		 * 	echo \Phalcon\Tag::getTitle();
		 * </code>
		 *
		 * <code>
		 * 	{{ get_title() }}
		 * </code>
		 *
		 * @return string
		 */
		public static function getTitle($tags = null)
        {
            if (is_null($tags)) {
                $tags = true;
            }

            if ($tags) {
                return '<title>' . self::$_documentTitle . '</title>' . PHP_EOL;
            }

            return self::$_documentTitle;
        }


		/**
		 * Builds a LINK[rel="stylesheet"] tag
		 *
		 * <code>
		 * 	echo \Phalcon\Tag::stylesheetLink("http://fonts.googleapis.com/css?family=Rosario", false);
		 * 	echo \Phalcon\Tag::stylesheetLink("css/style.css");
		 * </code>
		 *
		 * @param array $parameters
		 * @param   boolean $local
         *
		 * @return string
		 */
		public static function stylesheetLink($parameters = null, $local = null)
        {
            if (is_null($local)) {
                $local = true;
            }

            $params = array();

            if (!is_array($parameters)) {
                $params[] = $parameters;
                $params[] = $local;
            } else {
                $params = $parameters;
            }

            if (!array_key_exists('href', $params)) {
                if (array_key_exists(0, $params)) {
                    $params['href'] = $params[0];
                } else {
                    $params['href'] = '';
                }
            }

            if (!array_key_exists(1, $params)) {
                $local = $params[1];
            } else {
                if (array_key_exists('local', $params)) {
                    $local = $params['local'];

                    unset($params['local']);
                }
            }

            if (!array_key_exists('type', $params)) {
                $params['type'] = 'text/css';
            }

            if ($local) {
                $urlService = self::getUrlService();

                $params['href'] = $urlService->get($params['href']);
            }

            $code = '<link rel="stylesheet"';

            if ($params) {
                foreach ($params as $key => $value) {
                    if (!is_long($key)) {
                        $code .= ' ' . $key . '="' . $value . '"';
                    }
                }
            }

            /**
             * Check if Doctype is XHTML
             */
            if (self::HTML5 < self::$_documentType) {
                $code .= ' />' . PHP_EOL;
            } else {
                $code .= '>' . PHP_EOL;
            }

            return $code;
        }


		/**
		 * Builds a SCRIPT[type="javascript"] tag
		 *
		 * <code>
		 * 	echo \Phalcon\Tag::javascriptInclude("http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js", false);
		 * 	echo \Phalcon\Tag::javascriptInclude("javascript/jquery.js");
		 * </code>
		 *
		 * Volt syntax:
		 * <code>
		 * {{ javascript_include("http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js", false) }}
		 * {{ javascript_include("javascript/jquery.js") }}
		 * </code>
		 *
		 * @param array $parameters
		 * @param   boolean $local
         *
		 * @return string
		 */
		public static function javascriptInclude($parameters = null, $local = null)
        {
            if (is_null($local)) {
                $local = true;
            }

            $params = array();

            if (!is_array($parameters)) {
                $params[] = $parameters;
                $params[] = $local;
            } else {
                $params = $parameters;
            }

            if (!array_key_exists('src', $params)) {
                if (array_key_exists(0, $params)) {
                    $params['src'] = $params[0];
                } else {
                    $params['src'] = '';
                }
            }

            if (!array_key_exists(1, $params)) {
                $local = $params[1];
            } else {
                if (array_key_exists('local', $params)) {
                    $local = $params['local'];

                    unset($params['local']);
                }
            }

            if (!array_key_exists('type', $params)) {
                $params['type'] = 'text/javascript';
            }

            if ($local) {
                $urlService = self::getUrlService();

                $params['src'] = $urlService->get($params['src']);
            }

            $code = '<style';

            if ($params) {
                foreach ($params as $key => $value) {
                    if (!is_long($key)) {
                        $code .= ' ' . $key . '="' . $value . '"';
                    }
                }
            }

            $code .= '></script>' . PHP_EOL;

            return $code;
        }


		/**
		 * Builds HTML IMG tags
		 *
		 * @param  array $parameters
         *
		 * @return string
		 */
		public static function image($parameters = null)
        {
            $params = array();

            if (!is_array($parameters)) {
                $params = array($parameters);
            } else {
                $params = $parameters;
            }

            if (!array_key_exists('src', $params)) {
                if (array_key_exists(0, $params)) {
                    $params['src'] = $params[0];
                } else {
                    $params['src'] = '';
                }
            }

            $urlService = self::getUrlService();

            $params['src'] = $urlService->get($params['src']);

            $code = '<img';

            if ($params) {
                foreach ($params as $key => $value) {
                    if (!is_long($key)) {
                        $code .= ' ' . $key . '="' . $value . '"';
                    }
                }
            }

            /**
             * Check if Doctype is XHTML
             */
            if (self::HTML5 < self::$_documentType) {
                $code .= ' />' . PHP_EOL;
            } else {
                $code .= '>' . PHP_EOL;
            }

            return $code;
        }


		/**
		 * Converts texts into URL-friendly titles
		 *
		 * @param string $text
		 * @param string $separator
		 * @param boolean $lowercase
         *
		 * @return text
		 */
		public static function friendlyTitle($text, $separator = null, $lowercase = null)
        {
            if (is_null($separator)) {
                $separator = '-';
            }

            if (is_null($lowercase)) {
                $lowercase = false;
            }

            $friendly = preg_replace('@[^a-z0-9A-Z]+@', $separator, $text);

            if ($lowercase) {
                // FIXME: In CPhalcon we use a built-in function, however I think we should use a \Phalcon\Text component

                $friendlyText = \Phalcon\Text::lower($friendly);
            } else {
                $friendlyText = $friendly;
            }

            return $friendlyText;
        }


		/**
		 * Set the document type of content
		 *
		 * @param string $doctype
		 */
		public static function setDocType($doctype)
        {
            self::$_documentType = $doctype;
        }


		/**
		 * Get the document type declaration of content
		 *
		 * @return string
		 */
		public static function getDocType()
        {
            $declaration = '';

            switch (self::$_documentType) {
                case self::HTML32:
                    $declaration = 'PUBLIC "-//W3C//DTD HTML 3.2 Final//EN"';
                break;

                case self::HTML401_STRICT:
                    $declaration = 'PUBLIC "-//W3C//DTD HTML 4.01//EN""' . PHP_EOL . '" "http://www.w3.org/TR/html4/strict.dtd"';
                break;

                case self::HTML401_TRANSITIONAL:
                    $declaration = 'PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"' . PHP_EOL . '  "http://www.w3.org/TR/html4/loose.dtd"';
                break;

                case self::HTML401_FRAMESET:
                    $declaration = 'PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"' . PHP_EOL . '   "http://www.w3.org/TR/html4/frameset.dtd"';
                break;

                case self::XHTML10_STRICT:
                    $declaration = 'PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"' . PHP_EOL . '  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"';
                break;

                case self::XHTML10_TRANSITIONAL:
                    $declaration = 'PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"' . PHP_EOL . '    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"';
                break;

                case self::XHTML10_FRAMESET:
                    $declaration = 'PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"' . PHP_EOL . '    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd"';
                break;

                case self::XHTML11:
                    $declaration = 'PUBLIC "-//W3C//DTD XHTML 1.1//EN"' . PHP_EOL . ' "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"';
                break;

                case self::XHTML20:
                    $declaration = 'PUBLIC "-//W3C//DTD XHTML 2.0//EN"' . PHP_EOL . ' "http://www.w3.org/MarkUp/DTD/xhtml2.dtd"';
                break;
            }

            $doctypeHtml = '<!DOCTYPE html ' . $declaration . '>' . PHP_EOL;

            return $doctypeHtml;
        }

	}
}
