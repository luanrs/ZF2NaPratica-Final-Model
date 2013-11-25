<?php
use Core\Test\ControllerTestCase;
use Admin\Controller\IndexController;
use Admin\Model\User;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;
use Zend\View\Renderer\PhpRenderer;

/**
 * @group Controller
 */
class UserControllerTest extends ControllerTestCase {
	/**
	 * @var string
	 */
	protected $controllerFQDN = 'Admin\Controller\UserController';
	
	/**
	 * @var string
	 */
	protected $controllerRoute = 'admin';
	
	public function testUserIndexAction() {
		$userA = $this->addUser();
		$userB = $this->addUser();
		
		$this->routeMatch->setParam('action','index');
		$result = $this->controller->dispatch(
			$this->request, $this->response	
		);
		
		$response = $this->controller->getResponse();
		$this->assertEquals(200, $response->getStatusCode());
		
		$this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
		
		$variables = $result->getVariables();
		
		$this->assertHasArrayKey('users',$variables);
		
		$controllerData = $variables['users'];
		$this->assertEquals($userA->name, $controllerData[0]->name);
		$this->assertEquals($userB->name, $controllerData[1]->name);
	}
	
	/**
	 * @return void
	 */
	public function testUserSaveActionNewRequest() {
		$this->routeMatch->setParam('action','save');
		$result = $this->controller->dispatch(
			$this->request, $this->response	
		);
		
		$response = $this->controller->getResponse();
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
		
		$variables = $result->getVariables();
		$this->assertInstanceOf('Zend\Form\Form', $variables['form']);
		$form = $variables['form'];
		
		$id = $form->get('id');
		$this->assertEquals('id', $id->getName());
		$this->assertEquals('hidden', $id->getAttribute('type'));
	}
	
	public function testUserSaveActionUpdateFormRequest() {
		$userA = $this->addUser();
		
		$this->routeMatch->setParam('action', 'save');
		$this->routeMatch->setParam('id', $userA->id);
		$result = $this->controller->dispatch(
			$this->request, $this->response	
		);
		
		$response = $this->controller->getResponse();
		$this->assertEquals(200, $response->getStatusCode());
		
		$this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
		
		$variables = $result->getVariables();
		
		$variables = $result->getVariables();
		$this->assertInstanceOf('Zend\Form\Form', $variables['form']);
		$form = $variables['form'];
		
		$id = $form->get('id');
		$name = $form->get('name');
		$this->assertEquals('id', $id->getName());
		$this->assertEquals($userA->id,$id->getValue());
		$this->assertEquals($userA->name, $name->getValue());
	}
	
	public function testUserSaveActionPostRequest() {
		$this->routeMatch->setParam('action','save');
		
		$this->request->setMethod('post');
		$this->request->getPost()->set('name','Bill Gates');
		$this->request->getPost()->set('password', md5('apple'));
		$this->request->getPost()->set('username', 'bill');
		$this->request->getPost()->set('valid', 1);
		$this->request->getPost()->set('role', 'admin');
		
		$result = $this->controller->dispatch(
			$this->request, $this->response	
		);
		$response = $this->controller->getResponse();
		
		$this->assertEquals(302, $response->getStatusCode());
		$headers = $response->getHeaders();
		$this->assertEquals('Location: /admin/user', $headers->get('Location'));
	}
	
	public function testUserUpdateAction() {
		$user = $this->addUser();

		$this->routeMatch->setParam('action', 'save');
		
		$this->request->setMethod('post');
		$this->request->getPost()->set('id', $user->id);
		$this->request->getPost()->set('name', 'Alan Turing');
		$this->request->getPost()->set('password', md5('apple'));
		$this->request->getPost()->set('username', 'bill');
		$this->request->getPost()->set('valid', 1);
		$this->request->getPost()->set('role', 'admin');
		
		$result = $this->controller->dispatch(
			$this->request, $this->response	
		);
		
		$response = $this->controller->getResponse();
		
		$this->assertEquals(302, $response->getStatusCode());
		$headers = $response->getHeaders();
		
		$this->assertEquals(
			'Location: /admin/user', $headers->get('Location')	
		);
	}
	
	public function testUserSaveActionInvalidPostRequest() {
		$this->routeMatch->setParam('action', 'save');

		$this->request->setMethod('post');
		$this->request->getPost()->set('username', '');
		
		$result = $this->controller->dispatch(
			$this->request, $this->response	
		);
		
		$variables = $result->getVariables();
		$this->assertInstanceOf('Zend\Form\Form', $variables['form']);
		$form = $variables['form'];
		
		$username = $form->get('username');
		$usernameErrors = $username->getMessages();
		$this->assertEquals(
			"Value is required and can't be empty", $usernameErrors['isEmpty']
		);
	}
	
	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Código obrigatório
	 */
	public function testUserInvalidDeleteAction() {
		$this->routeMatch->setParam('action', 'delete');
		
		$result = $this->controller->dispatch(
			$this->request, $this->response	
		);
		
		$response = $this->controller->getResponse();
	}
	
	public function testUserDeleteAction() {
		$user = $this->addUser();
		
		$this->routeMatch->setParam('action', 'delete');
		$this->routeMatch->setParam('id', $user->id);
		
		$result = $this->controller->dispatch(
			$this->request, $this->response	
		);
		
		$response = $this->controller->getResponse();
		
		$this->assertEquals(302, $response->getStatusCode());
		$headers = $response->getHeaders();
		$this->assertEquals('Location: /admin/user', $headers->get('Location'));
	}
	
	private function addUser() {
		$user = new User();
		$user->username = 'steve';
		$user->password = md5('apple');
		$user->name = 'Steve <b>Jobs</>';
		$user->valid = 1;
		$user->roler = 'admin';
		
		$em = $this->serviceManager->get('Doctrine\Orm\EntityManager');
		$em->persist($user);
		$em->flush();
		
		return $user;
	}
}