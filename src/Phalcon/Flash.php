<?php 

namespace Phalcon {

	/**
	 * Phalcon\Flash
	 *
	 * Shows HTML notifications related to different circumstances. Classes can be stylized using CSS
	 *
	 *<code>
	 * $flash->success("The record was successfully deleted");
	 * $flash->error("Cannot open the file");
	 *</code>
	 */
	
	abstract class Flash {

		protected $_cssClasses;

		protected $_implicitFlush = true;

		protected $_automaticHtml = true;

		/**
		 * \Phalcon\Flash constructor
		 *
		 * @param array $cssClasses
		 */
		public function __construct($cssClasses = null)
        {
            if (!is_array($cssClasses)) {
                $cssClasses['error'] = 'errorMessage';
                $cssClasses['notice'] = 'noticeMessage';
                $cssClasses['success'] = 'successMessage';
                $cssClasses['warning'] = 'warningMessage';
            }

            $this->_cssClasses = $cssClasses;
        }


		/**
		 * Set the if the output must be implictly flushed to the output or returned as string
		 *
		 * @param boolean $implicitFlush
         *
		 * @return \Phalcon\FlashInterface
		 */
		public function setImplicitFlush($implicitFlush)
        {
            $this->_implicitFlush = $implicitFlush;

            return $this;
        }


		/**
		 * Set the if the output must be implictly formatted with HTML
		 *
		 * @param boolean $automaticHtml
         *
		 * @return \Phalcon\FlashInterface
		 */
		public function setAutomaticHtml($automaticHtml)
        {
            $this->_automaticHtml = $automaticHtml;

            return $this;
        }


		/**
		 * Set an array with CSS classes to format the messages
		 *
		 * @param array $cssClasses
         *
		 * @return \Phalcon\FlashInterface
		 */
		public function setCssClasses($cssClasses)
        {
            if (!is_array($cssClasses)) {
                throw new \Phalcon\Flash\Exception('CSS classes must be an Array');
            }

            $this->_cssClasses = $cssClasses;

            return $this;
        }


		/**
		 * Shows a HTML error message
		 *
		 *<code>
		 * $flash->error('This is an error');
		 *</code>
		 *
		 * @param string $message
         *
		 * @return string
		 */
		public function error($message)
        {
            return $this->message('error', $message);
        }


		/**
		 * Shows a HTML notice/information message
		 *
		 *<code>
		 * $flash->notice('This is an information');
		 *</code>
		 *
		 * @param string $message
         *
		 * @return string
		 */
		public function notice($message)
        {
            return $this->message('notice', $message);
        }


		/**
		 * Shows a HTML success message
		 *
		 *<code>
		 * $flash->success('The process was finished successfully');
		 *</code>
		 *
		 * @param string $message
         *
		 * @return string
		 */
		public function success($message)
        {
            return $this->message('success', $message);
        }


		/**
		 * Shows a HTML warning message
		 *
		 *<code>
		 * $flash->warning('Hey, this is important');
		 *</code>
		 *
		 * @param string $message
         *
		 * @return string
		 */
		public function warning($message)
        {
            return $this->message('warning', $message);
        }


		/**
		 * Outputs a message formatting it with HTML
		 *
		 *<code>
		 * $flash->outputMessage('error', $message);
		 *</code>
		 *
		 * @param string $type
		 * @param string $message
		 */
		public function outputMessage($type, $message)
        {
            $cssClasses = '';

            if ($this->_automaticHtml) {
                if (array_key_exists($type, $this->_cssClasses)) {
                    $typeClasses = $this->_cssClasses[$type];

                    if (is_array($typeClasses)) {
                        $joinedClasses = implode(' ', $typeClasses);
                        $cssClasses = ' class="' . $joinedClasses . '"';
                    } else {
                        $cssClasses = ' class="' . $typeClasses . '"';
                    }
                }
            }

            if (is_array($message)) {
                /**
                 * We create the message with implicit flush or other
                 */
                if (!$this->_implicitFlush) {
                    $content = '';
                }

                if ($message) {
                    foreach ($message as $msg) {
                        if ($this->_automaticHtml) {
                            $htmlMessage = '<div' . $cssClasses . '>' . $msg . '</div>' . PHP_EOL;
                        } else {
                            $htmlMessage = $msg;
                        }

                        if ($this->_implicitFlush) {
                            print $htmlMessage;
                        } else {
                            $content .= $htmlMessage;
                        }
                    }
                }

                /**
                 * We return the message as string if the implicit_flush is turned off
                 */
                if (!$this->_implicitFlush) {
                    return $content;
                }
            } else {
                /**
                 * We create the applying formatting or not
                 */
                if ($this->_automaticHtml) {
                    $htmlMessage = '<div' . $cssClasses . '>' . $message . '</div>' . PHP_EOL;
                } else {
                    $htmlMessage = $message;
                }

                if ($this->_implicitFlush) {
                    print $htmlMessage;
                } else {
                    return $htmlMessage;
                }
            }
        }

	}
}
