<?php 

namespace Phalcon\CLI {

	/**
	 * Phalcon\CLI\Console
	 *
	 * This component allows to create CLI applications using Phalcon
	 */
	
	class Console implements \Phalcon\DI\InjectionAwareInterface, \Phalcon\Events\EventsAwareInterface {

		protected $_dependencyInjector;

		protected $_eventsManager;

		protected $_modules;

		protected $_moduleObject;

		/**
		 * Sets the DependencyInjector container
		 *
		 * @param \Phalcon\DiInterface $dependencyInjector
		 */
		public function setDI($dependencyInjector)
        {
            if (!is_object($dependencyInjector)) {
                throw new \Phalcon\CLI\Console\Exception('Dependency Injector is invalid');
            }

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
		 * Sets the events manager
		 *
		 * @param \Phalcon\Events\ManagerInterface $eventsManager
		 */
		public function setEventsManager($eventsManager)
        {
            $this->_eventsManager = $eventsManager;
        }


		/**
		 * Returns the internal event manager
		 *
		 * @return \Phalcon\Events\ManagerInterface
		 */
		public function getEventsManager()
        {
            return $this->_eventsManager;
        }


		/**
		 * Register an array of modules present in the console
		 *
		 *<code>
		 *	$application->registerModules(array(
		 *		'frontend' => array(
		 *			'className' => 'Multiple\Frontend\Module',
		 *			'path' => '../apps/frontend/Module.php'
		 *		),
		 *		'backend' => array(
		 *			'className' => 'Multiple\Backend\Module',
		 *			'path' => '../apps/backend/Module.php'
		 *		)
		 *	));
		 *</code>
		 *
		 * @param array $modules
		 */
		public function registerModules($modules)
        {
            if (!is_array($modules)) {
                throw new \Phalcon\CLI\Console\Exception('Modules must be an Array');
            }

            $this->_modules = $modules;
        }


		/**
		 * Merge modules with the existing ones
		 *
		 *<code>
		 *	$application->addModules(array(
		 *		'admin' => array(
		 *			'className' => 'Multiple\Admin\Module',
		 *			'path' => '../apps/admin/Module.php'
		 *		)
		 *	));
		 *</code>
		 *
		 * @param array $modules
		 */
		public function addModules($modules)
        {
            if (!is_array($modules)) {
                throw new \Phalcon\CLI\Console\Exception('Modules must be an Array');
            }

            $this->_modules = array_merge($modules, $this->_modules);
        }


		/**
		 * Return the modules registered in the console
		 *
		 * @return array
		 */
		public function getModules()
        {
            return $this->_modules;
        }


		/**
		 * Handle the whole command-line tasks
		 *
		 * @param array $arguments
         *
		 * @return mixed
		 */
		public function handle($arguments = null)
        {
            if (is_null($arguments)) {
                $arguments = array();
            }

            if (!is_object($this->_dependencyInjector)) {
                throw new \Phalcon\CLI\Console\Exception('A dependency injection object is required to access internal services');
            }

            $router = $this->_dependencyInjector->getShared('router');
            $router->handle($arguments);

            $moduleName = $router->getModuleName();

            if ($moduleName) {
                if (is_object($this->_eventsManager)) {
                    $status = $this->_eventsManager->fire('console:beforeStartModule', $this, $moduleName);

                    if (false === $status) {
                        return false;
                    }
                }

                if (!array_key_exists($moduleName, $this->_modules)) {
                    throw new \Phalcon\CLI\Console\Exception('Module "' . $moduleName . '" isn\'t registered in the console container');
                }

                $module = $this->_modules[$moduleName];

                if (!is_array($module)) {
                    throw new \Phalcon\CLI\Console\Exception('Invalid module definition path');
                }

                if (array_key_exists('path', $module)) {
                    if (is_file($module['path']) && is_readable($module['path'])) {
                        require $module['path'];
                    } else {
                        throw new \Phalcon\CLI\Console\Exception('Module definition path "' . $module['path'] . '" doesn\'t exist');
                    }
                }

                if (array_key_exists('className', $module)) {
                    $className = $module['className'];
                } else {
                    $className = 'Module';
                }

                $moduleObject = $this->_dependencyInjector->get($className);
                $moduleObject->registerAutoloaders();
                $moduleObject->registerServices($this->_dependencyInjector);

                if (is_object($this->_eventsManager)) {
                    $status = $this->_eventsManager->fire('console:afterStartModule', $this, $moduleName);

                    if (false === $status) {
                        return false;
                    }
                }
            }

            $taskName = $router->getTaskName();
            $actionName = $router->getActionName();
            $params = $router->getParams();

            $dispatcher = $this->_dependencyInjector->getShared('dispatcher');
            $dispatcher->setTaskName($taskName);
            $dispatcher->setActionName($actionName);
            $dispatcher->setParams($params);

            if (is_object($this->_eventsManager)) {
                $status = $this->_eventsManager->fire('console:beforeHandleTask', $this, $dispatcher);

                if (false === $status) {
                    return false;
                }
            }

            $task = $dispatcher->dispatch();

            if (is_object($this->_eventsManager)) {
                $status = $this->_eventsManager->fire('console:afterHandleTask', $this, $task);

                if (false === $status) {
                    return false;
                }
            }

            return $task;
        }

	}
}
