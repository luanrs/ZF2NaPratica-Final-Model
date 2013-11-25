<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\ActionController;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\DbSelect as PaginatorDbSelectAdapter;

class IndexController extends ActionController
{	
	/**
	 * @return void
	 */
    public function indexAction()
    {
        $post = $this->getTable('Application\Model\Post');
        $sql = $post->getSql();
        $select = $sql->select();
        
        $paginatorAdapter = new PaginatorDbSelectAdapter($select, $sql);
        $paginator = new Paginator($paginatorAdapter);
        $cache = $this->getServiceLocator()->get('Cache');
        $paginator->setCache($cache);
        $paginator->setCurrentPageNumber($this->params()->fromRoute('page'));
        
        
        $view = new ViewModel(array(
        	'posts' => $paginator
		));
        return $view;
    }
	
	/**
	 * @return Zend\Http\Response
	 */
	public function commentsAction() {
		$id = (int) $this->params()->fromRoute('id',0);
		$where = array('post_id' => $id);
		$comments = $this->getTable('Application\Model\Comment')
			->fetchAll(null,$where)
			->toArray();
		
		$response = $this->getResponse();
		$response->setStatusCode(200);
		$response->setContent(json_encode($comments));
		$response->getHeaders()->addHeaderLine('Content-Type','application/json');
		return $response;
	}
}