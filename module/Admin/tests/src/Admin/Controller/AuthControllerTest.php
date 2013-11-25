<?php
use Core\Test\ControllerTestCase;
use Admin\Controller\AuthController;
use Admin\Model\User;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;
use Zend\View\Renderer\PhpRenderer;

/**
 * @group Controller
 */
class AuthControllerTest extends ControllerTestCase {
	/**
	 * @var string
	 */
	protected $controllerFQDN = 'Admin\Controller\AuthController';
	
	/**
	 * @var string
	 */
	protected $controllerRoute = 'admin';
	
	public function test404() {
		$this->routeMatch->setParam('action','action_nao_existente');
		$result = $this->controller->dispatch($this->request);
		$response = $this->controller->getResponse();
		$this->assertEquals(404, $response->getStatusCode());
	}
	
	public function testIndexActionLoginForm() {
		$this->routeMatch->setParam('action','index');
		$result = $this->controller->dispatch(
			$this->request, $this->response	
		);
		
		$response = $this->controller->getResponse();
		$this->assertEquals(200, $response->getStatusCode());
		
		$this->assertInstanceOf('Zend\View\Model\ViewModel',$result);
		
		$variables = $result->getVariables();
		
		$this->assertArrayHasKey('form', $variables);
		
		$this->assertInstanceOf('Zend\Form\Form', $variables['form']);
		$form = $variables['form'];
		
		$username = $form->get('username');
		$this->assertEquals('username', $username->getName());
		$this->assertEquals('text', $username->getAttribute('type'));
		
		$password = $form->get('password');
		$this->assertEquals('password', $password->getName());
		$this->assertEquals('password', $password->getAttribute('type'));	
	}
	
	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Acesso inválido
	 */
	public function testLoginInvalidMethod() {
		$user = $this->addUser();
		
		$this->routeMatch->setParam('action','login');
		$result = $this->controller->dispatch(
			$this->request, $this->response	
		);
	}
	
	public function testLogin() {
		$user = $this->addUser();

		$this->request->setMethod('post');
		$this->request->getPost()->set('username', $user->username);
		$this->request->getPost()->set('password', 'apple');
		
		$this->routeMatch->setParam('action','login');
		$resutlt = $this->controller->dispatch(
			$this->request, $this->response	
		);
		
		$response = $this->controller->getResponse();
		$this->assertEquals(302, $response->getStatusCode());
		$headers = $response->getHeaders();
		$this->assertEquals('Location: /',$headers->get('Location'));
	}
	
	public function testLogout() {
		$user = $this->addUser();
		
		$this->routeMatch->setParam('action','logout');
		$result = $this->controller->dispatch(
			$this->request, $this->response	
		);
		
		$response = $this->controller->getResponse();
		
		$this->assertEquals(302, $response->getStatusCode());
		$headers = $response->getHeaders();
		$this->assertEquals('Location: /', $headers->get('Location'));
	}
	
	private function addUser() {
		$user = new User();
		$user->username = 'steve';
		$user->password = md5('apple');
		$user->name = 'Steve <b>Jobs</b>';
		$user->valid = 1;
		$user->role = 'admin';
		
		$saved = $this->getTable('Admin\Model\User')->save($user);
		return $saved;
	}
}