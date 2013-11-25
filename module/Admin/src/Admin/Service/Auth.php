<?php
	namespace Admin\Service;
	
	use Core\Service\Service;
	use Zend\Authentication\AuthenticationService;
	use Zend\Authentication\Adapter\DbTable as AuthAdapter;
	use Zend\Db\Sql\Select;
	
	/**
	 * @category Admin
	 * @package Service
	 */
	class Auth extends Service {
		/**
		 * @var Zend\Db\Adapter\Adapter
		 */
		private $dbAdapter;
		
		/**
		 * @return void
		 */
		public function __construct($dbAdapter = null) {
			$this->dbAdapter = $dbAdapter;
		}
		
		/**
		 * @param array $params
		 * @return array
		 */
		public function authenticate($params) {
			if (!isset($params['username']) || !isset($params['password'])){
				throw new \Exception("Parâmetros inválidos");
			}
			
			$password = md5($params['password']);
			$auth = new AuthenticationService();
			$authAdapter = new AuthAdapter($this->dbAdapter);
			$authAdapter
				->setTableName('users')
				->setIdentityColumn('username')
				->setCredentialColumn('password')
				->setIdentity($params['username'])
				->setCredential($password);
			$result = $auth->authenticate($authAdapter);
			
			if( !$result->isValid()) {
				throw new \Exception("Login ou senha inválidos");
			}
		
			$session = $this->getServiceManager()->get('Session');
			$session->offsetSet('user',$authAdapter->getResultRowObject());
			
			return true;
		}
		
		/**
		 * @return void
		 */
		public function logout() {
			$auth = new AuthenticationService();
			$session = $this->getServiceManager()->get('Session');
			$session->offsetUnset('user');
			$auth->clearIdentity();
			return true;
		}
		
		/**
		 * @param string $moduleName
		 * @param string $controllerName
		 * @param string $actionName
		 * @return boolean
		 */
		public function authorize($moduleName, $controllerName, $actionName) {
			$auth = new AuthenticationService();
			$role = 'visitante';
			if ($auth->hasIdentity()) {
				$session = $this->getServiceManager()->get('Session');
				$user = $session->offsetGet('user');
				$role = $user->role;
			}
			
			$resource = $controllerName . '.' . $actionName;
			
			$acl = $this->getServiceManager()
				->get('Core\Acl\Builder')
				->build();
			
			if ($acl->isAllowed($role, $resource)) {
				return true;
			}
			return false;
		}
	}