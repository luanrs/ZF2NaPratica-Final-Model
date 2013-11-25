<?php
use Core\Test\ControllerTestCase;
use Admin\Controller\IndexController;
use Application\Model\Post;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;
use Zend\View\Renderer\PhpRenderer;

/**
 * @group Controller
 */
class IndexControllerTest extends ControllerTestCase {
	/**
	 * @var string
	 */
	protected $controllerFQDN = 'Admin\Controller\IndexController';
	
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
	
	/**
	 * @return void
	 */
	public function testSaveActionNewRequest() {
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
	
	public function testSaveActionUpdateFormRequest() {
		$postA = $this->addPost();
		
		$this->routeMatch->setParam('action','save');
		$this->routeMatch->setParam('id', $postA->id);
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
		$title = $form->get('title');
		$this->assertEquals('id',$id->getName());
		$this->assertEquals($postA->id, $id->getValue());
		$this->assertEquals($postA->title, $title->getValue());
	}
	
	public function testSaveActionPostRequest() {
		$this->routeMatch->setParam('action', 'save');
		
		$this->request->setMethod('post');
		$this->request->getPost()->set('title', 'Apple compra a Coderockr');
		
		$this->request->getPost()->set('description', 'A Apple compra a <b>Coderockr</b><br>');
		
		$result = $this->controller->dispatch(
			$this->request, $this->response	
		);
		
		$response = $this->controller->getResponse();
		$this->assertEquals(302, $response->getStatusCode());
		
		$posts = $this->getTable('Application\Model\Post')
			->fetchAll()
			->toArray();
		$this->assertEquals(1, count($posts));
		$this->assertEquals('Apple complra a Coderockr', $posts[0]['title']);
		$this->assertNotNull($posts[0]['post_date']);
	}
	
	public function testSaveActionInvalidPostRequest() {
		$this->routeMatch->setParam('action', 'save');
		
		$this->request->setMethod('post');
		$this->request->getPost()->set('title', '');
		
		$result = $this->controller->dispatch(
			$this->request, $this->response	
		);
		
		$variables = $result->getVariables();
		$this->assertInstanceOf('Zend\Form\Form', $variables['form']);
		$form = $variables['form'];
		
		$title = $form->get('title');
		$titleErrors = $title->getmessages();
		$this->assertEquals(
			"Value is required and can't be empty",
			$titleErrors['isEmpty']	
		);
		
		$description = $form->get('description');
		$descriptionErrors = $description->getMessages();
		$this->assertEquals(
			"Value is required and can't be empty",
				$descriptionErrors['isEmpty']	
		);
	}
	
	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Código obrigatório
	 */
	public function testInvalidDeleteAction() {
		$post = $this->addPost();
		
		$this->routeMatch->setParam('action', 'delete');
		$result = $this->controller->dispatch(
			$this->request, $this->response		
		);
		
		$response = $this->controller->getResponse();
	}
	
	public function testDeleteAction() {
		$post = $this->addPost();
		
		$this->routeMatch->setParam('action', 'delete');
		$this->routeMatch->setParam('id', $post->id);
		
		$result = $this->controller->dispatch(
			$this->request, $this->response	
		);
		
		$response = $this->controller->getResponse();
		
		$this->assertEquals(302, $response->getStatusCode());
		
		$posts = $this->getTable('Application\Model\Post')
			->fetchAll()
			->toArray();
		$this->assertEquals(0, count($posts));
	}
	
	private function addPost() {
		$post = new Post();
		$post->title = 'Apple compra a Coderockr';
		$post->description = 'A Apple compra <b>Coderockr</b><br>';
		$post->post_date = date('Y-m-d H:i:s');
		$saved = $this->getTable('Application\Model\Post')->save($post);
		return $saved;
	}
}