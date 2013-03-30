<?php 

namespace Phalcon\Acl\Adapter {

	/**
	 * Phalcon\Acl\Adapter\Memory
	 *
	 * Manages ACL lists in memory
	 *
	 *<code>
	 *
	 *	$acl = new Phalcon\Acl\Adapter\Memory();
	 *
	 *	$acl->setDefaultAction(Phalcon\Acl::DENY);
	 *
	 *	//Register roles
	 *	$roles = array(
	 *		'users' => new Phalcon\Acl\Role('Users'),
	 *		'guests' => new Phalcon\Acl\Role('Guests')
	 *	);
	 *	foreach ($roles as $role) {
	 *		$acl->addRole($role);
	 *	}
	 *
	 *	//Private area resources
	 *  $privateResources = array(
	 *		'companies' => array('index', 'search', 'new', 'edit', 'save', 'create', 'delete'),
	 *		'products' => array('index', 'search', 'new', 'edit', 'save', 'create', 'delete'),
	 *		'invoices' => array('index', 'profile')
	 *	);
	 *	foreach ($privateResources as $resource => $actions) {
	 *		$acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
	 *	}
	 *
	 *	//Public area resources
	 *	$publicResources = array(
	 *		'index' => array('index'),
	 *		'about' => array('index'),
	 *		'session' => array('index', 'register', 'start', 'end'),
	 *		'contact' => array('index', 'send')
	 *	);
	 *  foreach ($publicResources as $resource => $actions) {
	 *		$acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
	 *	}
	 *
	 *  //Grant access to public areas to both users and guests
	 *	foreach ($roles as $role){
	 *		foreach ($publicResources as $resource => $actions) {
	 *			$acl->allow($role->getName(), $resource, '*');
	 *		}
	 *	}
	 *
	 *	//Grant acess to private area to role Users
	 *  foreach ($privateResources as $resource => $actions) {
	 * 		foreach ($actions as $action) {
	 *			$acl->allow('Users', $resource, $action);
	 *		}
	 *	}
	 *
	 *</code>
	 */
	
	class Memory extends \Phalcon\Acl\Adapter implements \Phalcon\Events\EventsAwareInterface, \Phalcon\Acl\AdapterInterface {

		protected $_rolesNames;

		protected $_roles;

		protected $_resources;

		protected $_access;

		protected $_roleInherits;

		protected $_resourcesNames;

		protected $_accessList;

		/**
		 * \Phalcon\Acl\Adapter\Memory constructor
		 */
		public function __construct()
        {
            $this->_rolesNames = array();
            $this->_roles = array();
            $this->_resources = array();
            $this->_access = array();
            $this->_roleInherits = array();

            $this->_resourcesNames = array();
            $this->_resourcesNames['*'] = true;

            $this->_accessList = array();
            $this->_accessList['*']['*'] = true;
        }


		/**
		 * Adds a role to the ACL list. Second parameter allows inheriting access data from other existing role
		 *
		 * Example:
		 * <code>
		 * 	$acl->addRole(new \Phalcon\Acl\Role('administrator'), 'consultant');
		 * 	$acl->addRole('administrator', 'consultant');
		 * </code>
		 *
		 * @param  \Phalcon\Acl\RoleInterface $role
		 * @param  array|string $accessInherits
         *
		 * @return boolean
		 */
		public function addRole($role, $accessInherits = null)
        {
            if (is_object($role)) {
                $roleName = $role->getName();
                $object = $role;
            } else {
                $roleName = $role;
                $object = new \Phalcon\Acl\Role($role);
            }

            if (!array_key_exists($roleName, $this->_rolesNames)) {
                return false;
            }

            $this->_roles[] = $object;
            $this->_rolesNames[$roleName] = true;

            $this->_access[$roleName]['*']['*'] = $this->_defaultAccess;

            if (!is_null($accessInherits)) {
                $this->addInherit($roleName, $accessInherits);
            }

            return true;
        }


		/**
		 * Do a role inherit from another existing role
		 *
		 * @param string $roleName
		 * @param string $roleToInherit
		 */
		public function addInherit($roleName, $roleToInherit)
        {
            if (!array_key_exists($roleName, $this->_rolesNames)) {
                throw new \Phalcon\Acl\Exception('Role "' . $roleName . '" does not exist in the role list');
            }

            if (is_object($roleToInherit)) {
                $roleInheritName = $roleToInherit->getName();
            } else {
                $roleInheritName = $roleToInherit;
            }

            /**
             * Check if the role to inherit is valid
             */
            if (!array_key_exists($roleInheritName, $this->_rolesNames)) {
                throw new \Phalcon\Acl\Exception('Role "' . $roleInheritName . '" (to inherit) does not exist in the role list');
            }

            if ($roleName == $roleInheritName) {
                return false;
            }

            if (!array_key_exists($roleName, $this->_roleInherits)) {
                $this->_roleInherits[$roleName] = array();
            }

            $this->_roleInherits[$roleName][] = $roleInheritName;

            /**
             * Re-build the access list with its inherited roles
             */
            $this->_rebuildAccessList();

            return true;
        }


		/**
		 * Check whether role exist in the roles list
		 *
		 * @param  string $roleName
         *
		 * @return boolean
		 */
		public function isRole($roleName)
        {
            return array_key_exists($roleName, $this->_rolesNames);
        }


		/**
		 * Check whether resource exist in the resources list
		 *
		 * @param  string $resourceName
         *
		 * @return boolean
		 */
		public function isResource($resourceName)
        {
            return array_key_exists($resourceName, $this->_resourcesNames);
        }


		/**
		 * Adds a resource to the ACL list
		 *
		 * Access names can be a particular action, by example
		 * search, update, delete, etc or a list of them
		 *
		 * Example:
		 * <code>
		 * //Add a resource to the the list allowing access to an action
		 * $acl->addResource(new \Phalcon\Acl\Resource('customers'), 'search');
		 * $acl->addResource('customers', 'search');
		 *
		 * //Add a resource  with an access list
		 * $acl->addResource(new \Phalcon\Acl\Resource('customers'), array('create', 'search'));
		 * $acl->addResource('customers', array('create', 'search'));
		 * </code>
		 *
		 * @param   \Phalcon\Acl\Resource $resource
		 * @param   array $accessList
         *
		 * @return  boolean
		 */
		public function addResource($resource, $accessList = null)
        {
            if (is_object($resource)) {
                $resourceName = $resource->getName();
                $object = $resource;
            } else {
                $resourceName = $resource;
                $object = new \Phalcon\Acl\Resource($resourceName);
            }

            if (!array_key_exists($resourceName, $this->_resourcesNames)) {
                $this->_resources[] = $object;
                $this->_accessList[$resourceName] = array();
                $this->_resourcesNames[$resourceName] = true;
            }

            return $this->addResourceAccess($resourceName, $accessList);
        }


		/**
		 * Adds access to resources
		 *
		 * @param string $resourceName
		 * @param mixed $accessList
		 */
		public function addResourceAccess($resourceName, $accessList)
        {
            if (!array_key_exists($resourceName, $this->_resourcesNames)) {
                throw new \Phalcon\Acl\Exception('Resource "' . $resourceName . '" does not exist in ACL');
            }

            if (is_array($accessList)) {
                if ($accessList) {
                    foreach ($accessList as $accessName) {
                        if (!array_key_exists($accessName, $this->_accessList[$resourceName])) {
                            $this->_accessList[$resourceName][$accessName] = true;
                        }
                    }
                }
            } else {
                if (is_string($accessList)) {
                    $this->_accessList[$resourceName][$accessList] = true;
                }
            }

            return true;
        }


		/**
		 * Removes an access from a resource
		 *
		 * @param string $resourceName
		 * @param mixed $accessList
		 */
		public function dropResourceAccess($resourceName, $accessList)
        {
            if (is_array($accessList)) {
                if ($accessList) {
                    foreach ($accessList as $accessName) {
                        unset($this->_accessList[$resourceName][$accessName]);
                    }
                }
            } else {
                if ($accessList) {
                    unset($this->_accessList[$resourceName][$accessList]);
                }
            }

            $this->_rebuildAccessList();
        }


		/**
		 * Checks if a role has access to a resource
		 *
		 * @param string $roleName
		 * @param string $resourceName
		 * @param string $access
		 * @param string $action
		 */
		protected function _allowOrDeny($roleName, $resourceName, $access, $action)
        {
            if (!array_key_exists($roleName, $this->_rolesNames)) {
                throw new \Phalcon\Acl\Exception('Role "' . $roleName . '" does not exist in the role list');
            }

            if (!array_key_exists($resourceName, $this->_resourcesNames)) {
                throw new \Phalcon\Acl\Exception('Resource "' . $resourceName . '" does not exist in ACL');
            }

            if (is_array($access)) {
                if ($access) {
                    foreach ($access as $accessName) {
                        if (!array_key_exists($accessName, $this->_accessList)) {
                            throw new \Phalcon\Acl\Exception('Access "' . $accessName . '" does not exist in resource "' . $resourceName . '" in ACL');
                        }
                    }

                    reset($access);

                    foreach ($access as $accessName) {
                        if (!array_key_exists($accessName, $this->_access[$roleName])) {
                            $this->_access[$roleName][$resourceName] = array();
                        }

                        $this->_access[$roleName][$resourceName][$accessName] = $action;

                        if (!array_key_exists('*', $this->_access[$roleName][$resourceName])) {
                            $this->_access[$roleName][$resourceName]['*'] = $this->_defaultAccess;
                        }
                    }
                }
            } else {
                if ('*' != $access) {
                    if (!array_key_exists($access, $this->_accessList[$resourceName])) {
                        throw new \Phalcon\Acl\Exception('Access "' . $access . '" does not exist in resource "' . $resourceName . '" in ACL');
                    }

                    if (!array_key_exists($resourceName, $this->_access[$roleName])) {
                        $this->_access[$roleName][$resourceName] = array();
                    }

                    if (!array_key_exists('*', $this->_access[$roleName][$resourceName])) {
                        $this->_access[$roleName][$resourceName]['*'] = $this->_defaultAccess;
                    }

                    $this->_access[$roleName][$resourceName][$access] = $action;
                }
            }

            $this->_rebuildAccessList();
        }


		/**
		 * Allow access to a role on a resource
		 *
		 * You can use '*' as wildcard
		 *
		 * Example:
		 * <code>
		 * //Allow access to guests to search on customers
		 * $acl->allow('guests', 'customers', 'search');
		 *
		 * //Allow access to guests to search or create on customers
		 * $acl->allow('guests', 'customers', array('search', 'create'));
		 *
		 * //Allow access to any role to browse on products
		 * $acl->allow('*', 'products', 'browse');
		 *
		 * //Allow access to any role to browse on any resource
		 * $acl->allow('*', '*', 'browse');
		 * </code>
		 *
		 * @param string $roleName
		 * @param string $resourceName
		 * @param mixed $access
		 */
		public function allow($roleName, $resourceName, $access)
        {
            return $this->_allowOrDeny($roleName, $resourceName, $access, true);
        }


		/**
		 * Deny access to a role on a resource
		 *
		 * You can use '*' as wildcard
		 *
		 * Example:
		 * <code>
		 * //Deny access to guests to search on customers
		 * $acl->deny('guests', 'customers', 'search');
		 *
		 * //Deny access to guests to search or create on customers
		 * $acl->deny('guests', 'customers', array('search', 'create'));
		 *
		 * //Deny access to any role to browse on products
		 * $acl->deny('*', 'products', 'browse');
		 *
		 * //Deny access to any role to browse on any resource
		 * $acl->deny('*', '*', 'browse');
		 * </code>
		 *
		 * @param string $roleName
		 * @param string $resourceName
		 * @param mixed $access
         *
		 * @return boolean
		 */
		public function deny($roleName, $resourceName, $access)
        {
            return $this->_allowOrDeny($roleName, $resourceName, $access, false);
        }


		/**
		 * Check whether a role is allowed to access an action from a resource
		 *
		 * <code>
		 * //Does andres have access to the customers resource to create?
		 * $acl->isAllowed('andres', 'Products', 'create');
		 *
		 * //Do guests have access to any resource to edit?
		 * $acl->isAllowed('guests', '*', 'edit');
		 * </code>
		 *
		 * @param  string $role
		 * @param  string $resource
		 * @param  string $access
		 * @return boolean
		 */
		public function isAllowed($role, $resource, $access)
        {
            $this->_activeRole = $role;
            $this->_activeResource = $resource;
            $this->_activeAccess = $access;

            if (is_object($this->_eventsManager)) {
                if (!$this->_eventsManager->fire('acl:beforeCheckAccess')) {
                    return false;
                }
            }

            if (!array_key_exists($resource, $this->_resourcesNames)) {
                return $this->_defaultAccess;
            }

            if (!array_key_exists($role, $this->_rolesNames)) {
                return $this->_defaultAccess;
            }

            $hasAccess = false;

            if ($this->_access[$role]) {
                if (array_key_exists($resource, $this->_access[$role])) {
                    $resourceAccess = $this->_access[$role][$resource];

                    if (array_key_exists($resourceAccess, $this->_access)) {
                        $hasAccess = $resourceAccess[$access];
                    } else {
                        $hasAccess = $resourceAccess['*'];
                    }
                }
            }

            if (!$hasAccess) {
                if ($this->_access[$role]) {
                    foreach ($this->_access[$role] as $resourceName => $resourceAccess) {
                        if (array_key_exists('*', $resourceAccess)) {
                            if (array_key_exists($access, $resourceAccess)) {
                                $hasAccess = $resourceAccess[$access];
                            } else {
                                $hasAccess = $resourceAccess['*'];
                            }
                        }
                    }
                }
            }

            $this->_accessGranted = $hasAccess;

            if (is_object($this->_eventsManager)) {
                $this->_eventsManager->fire('acl:afterCheckAccess');
            }

            return $hasAccess;
        }


		/**
		 * Return an array with every role registered in the list
		 *
		 * @return \Phalcon\Acl\Role[]
		 */
		public function getRoles()
        {
            return $this->_roles;
        }


		/**
		 * Return an array with every resource registered in the list
		 *
		 * @return \Phalcon\Acl\Resource[]
		 */
		public function getResources()
        {
            return $this->_resources;
        }


		/**
		 * Rebuild the list of access from the inherit lists
		 *
		 */
		protected function _rebuildAccessList()
        {
            $rolesCount = count($this->_roles);
            $middle = ceil(pow($rolesCount, $rolesCount) / 2);

            $changed = true;

            for ($i = 0; $i <= $middle; $i++) {
                $internalAccess = $this->_access;

                if ($this->_rolesNames && is_array($this->_rolesNames)) {
                    foreach ($this->_rolesNames as $roleName => $one) {
                        if (array_key_exists($roleName, $this->_roleInherits)) {
                            if ($this->_roleInherits[$roleName]) {
                                foreach ($this->_roleInherits[$roleName] as $roleInherit) {
                                    if (array_key_exists($roleInherit, $internalAccess)) {
                                        foreach ($internalAccess[$roleInherit] as $resourceName => $access) {
                                            if ($access && is_array($access)) {
                                                foreach ($access as $name => $value) {
                                                    if (array_key_exists($roleName, $internalAccess)) {
                                                        if (array_key_exists($resourceName, $internalAccess[$roleName])) {
                                                            if (array_key_exists($name, $internalAccess[$roleName][$resourceName])) {
                                                                continue;
                                                            }
                                                        }
                                                    }

                                                    $internalAccess[$roleName][$resourceName][$name] = $value;

                                                    $changed = true;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if ($changed) {
                    $this->_access = $internalAccess;
                }
            }
        }

	}
}
