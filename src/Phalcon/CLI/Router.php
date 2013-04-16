<?php 

namespace Phalcon\CLI {

	/**
	 * Phalcon\CLI\Router
	 *
	 * <p>Phalcon\CLI\Router is the standard framework router. Routing is the
	 * process of taking a command-line arguments and
	 * decomposing it into parameters to determine which module, task, and
	 * action of that task should receive the request</p>
	 *
	 *<code>
	 *	$router = new Phalcon\CLI\Router();
	 *	$router->handle(array());
	 *	echo $router->getTaskName();
	 *</code>
	 *
	 */
	
	class Router implements \Phalcon\DI\InjectionAwareInterface {

		protected $_dependencyInjector;

		protected $_module;

		protected $_task;

		protected $_action;

		protected $_params;

		protected $_defaultModule;

		protected $_defaultTask;

		protected $_defaultAction;

		protected $_defaultParams;

		/**
		 * \Phalcon\CLI\Router constructor
		 */
		public function __construct()
        {
            $this->_params = array();
            $this->_defaultParams = array();
        }


		/**
		 * Sets the dependency injector
		 *
		 * @param \Phalcon\DiInterface $dependencyInjector
		 */
		public function setDI($dependencyInjector)
        {
            $this->_dependencyInjector = $dependencyInjector;
        }


		/**
		 * Returns the internal dependency injector
		 *
		 * @return \Phalcon\DiInterface
		 */
		public function getDI()
        {
            return $this->_dependencyInjector;
        }


		/**
		 * Sets the name of the default module
		 *
		 * @param string $moduleName
		 */
		public function setDefaultModule($moduleName)
        {
            $this->_defaultModule = $moduleName;
        }


		/**
		 * Sets the default controller name
		 *
		 * @param string $taskName
		 */
		public function setDefaultTask($taskName)
        {
            $this->_defaultTask = $taskName;
        }


		/**
		 * Sets the default action name
		 *
		 * @param string $actionName
		 */
		public function setDefaultAction($actionName)
        {
            $this->_defaultAction = $actionName;
        }


		/**
		 * Handles routing information received from command-line arguments
		 *
		 * @param array $arguments
		 */
		public function handle($arguments = null)
        {
            if (is_null($arguments)) {
                $arguments = array();
            }

            if (!is_array($arguments)) {
                throw new \Phalcon\CLI\Router\Exception('Arguments must be an Array');
            }

            $argumentsCount = count($arguments);

            $params = array();

            if ($argumentsCount > 3) {
                // script, task, action, params.....
                $taskNameTemp = $arguments[1];
                $actionName = $arguments[2];

                // process params
                for ($i = 3; $i < $argumentsCount; $i++) {
                    $params[] = $arguments[$i];
                }
            } else {
                if ($argumentsCount > 2) {
                    // script, task, action
                    $taskNameTemp = $arguments[1];
                    $actionName = $arguments[2];
                } else if ($argumentsCount > 1) {
                    // script, task
                    $taskNameTemp = $arguments[1];
                }
            }

            // if task_name settings, parse task_name for module_name
            if (isset($taskNameTemp)) {
                $taskNameParts = explode(':', $taskNameTemp);

                $status = count($taskNameParts);

                if (2 === $status) {
                    $moduleName = $taskNameParts[0];
                    $taskName = $taskNameParts[1];
                } else {
                    $taskName = $taskNameParts[0];
                }
            }

            // update properties
            if (isset($moduleName)) {
                $this->_module = $moduleName;
            } else {
                if (!is_null($this->_defaultModule)) {
                    $this->_module = $this->_defaultModule;
                }
            }

            if (isset($taskName)) {
                $this->_task = $taskName;
            } else {
                if (!is_null($this->_defaultTask)) {
                    $this->_task = $this->_defaultTask;
                }
            }

            if (isset($actionName)) {
                $this->_action = $actionName;
            } else {
                if (!is_null($this->_defaultAction)) {
                    $this->_action = $this->_defaultAction;
                }
            }

            $this->_params = $params;
        }


		/**
		 * Returns proccesed module name
		 *
		 * @return string
		 */
		public function getModuleName()
        {
            return $this->_module;
        }


		/**
		 * Returns proccesed task name
		 *
		 * @return string
		 */
		public function getTaskName()
        {
            return $this->_task;
        }


		/**
		 * Returns proccesed action name
		 *
		 * @return string
		 */
		public function getActionName()
        {
            return $this->_action;
        }


		/**
		 * Returns proccesed extra params
		 *
		 * @return array
		 */
		public function getParams()
        {
            return $this->_params;
        }

	}
}
