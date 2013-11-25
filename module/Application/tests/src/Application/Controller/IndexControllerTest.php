<?php
	use Core\Test\ControllerTestCase;
	use Application\Controller\IndexController;
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
		protected $controllerFQDN = 'Application\Controller\IndexController';
		
		/**
		 * @var string
		 */
		protected $controllerRoute = 'application';
		
		public function test404() {
			$this->routeMatch->setParam('action','action_nao_existente');
			$result = $this->controller->dispatch($this->request);
			$response = $this->controller->getResponse();
			$this->assertEquals(404, $response->getStatusCode());
		}
		
		public function testIndexAction() {
			$postA = $this->addPost();
			$postB = $this->addPost();

			$this->routeMatch->setParam('action','index');
			$result = $this->controller->dispatch($this->request, $this->response);
			
			$response = $this->controller->getResponse();
			$this->assertEquals(200, $response->getStatusCode());
			
			$this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
			
			$variables = $result->getVariables();
			$this->assertArrayHasKey('posts', $variables);
			
			$controllerData = $variables["posts"]->getCurrentItems()->toArray();
			$this->assertEquals($postA->title, $controllerData[0]['title']);
			$this->assertEquals($postB->title, $controllerData[1]['title']);
		}
		
		public function testIndexActionPaginator() {
			$post = array();
			for ($i = 0; $i < 25; $i++) {
				$post[] = $this->addPost();
			}
			
			$this->routeMatch->setParam('action','index');
			$result = $this->controller->dispatch($this->request, $this->response);
			
			$response = $this->controller->getResponse();
			$this->assertEquals(200, $response->getStatusCode());
			
			$this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
			
			$variables = $result->getVariables();
			
			$this->assertArrayHasKey('posts',$variables);
			
			$paginator = $variables["posts"];
			$this->assertEquals('Zend\Paginator\Paginator',get_class($paginator));
			
			$posts = $paginator->getCurrentItems()->toArray();
			$this->assertEquals(10, count($posts));
			$this->assertEquals($post[0]->id,$posts[0]['id']);
			$this->assertEquals($post[1]->id,$posts[1]["id"]);
			
			$this->routeMatch->setParam('action','index');
			$this->routeMatch->setParam("page",3);
			$result = $this->controller->dispatch($this->request, $this->response);
			
			$variables = $result->getVariables();
			$controllerData = $variables["posts"]->getCurrentItems()->toArray();
			$this->assertEquals(5, count($controllerData));
		}
		
		private function addPost() {
			$post = new Post();
			$post->title = 'Apple compra a Coderockr';
			$post->description = 'A apple compra <b>Coderockr</b><br> ';
			$post->post_date = date('Y-m-d H:i:s');
			$saved = $this->getTable('Application\Model\Post')->save($post);
			return $saved;
		}
	}