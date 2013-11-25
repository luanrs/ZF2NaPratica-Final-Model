<?php
namespace Admin\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\ActionController;
use Admin\Model\User;
use Admin\Form\User as UserForm;

use Doctrine\ORM\EntityManager;

/**
 * @category Admin
 * @package Controller
 */

class UserController extends ActionController {
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	public function setEntityManager(EntityManager $em) {
		$this->em = $em;
	}
	
	public function getEntityManager() {
		if (null === $this->em) {
			$this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		}
		return $this->em;
	}
	
	/**
	 * @return void
	 */
	public function indexAction() {
		$users = $this->getEntityManager()->getRepository('Admin\Model\User')->findAll();
		return new ViewModel(array(
			'users' => $users
		));
	}
	
	/**
	 * @return void
	 */
	public function saveAction() {
		$form = new UserForm();

		$request = $this->getRequest();
		if ($request->isPost()) {
			$user = new User();
			$form->setInputFilter($user->getInputFilter());
			$form->setData($request->getPost());
			if ($form->isValid()) {
				$data = $form->getData();
				unset($data['submit']);
				$data['valid'] = 1;
				$data['password'] = md5($data['password']);
				$user->setData($data);
				
				$this->getEntityManager()->persist($user);
				$this->getEntityManager()->flush();
				
				return $this->redirect()->toUrl('/admin/user');
			}
		}
		
		$id = (int) $this->params()->fromRoute('id', 0);
		if ($id > 0) {
			$user = $this->getEntityManager()->find('Admin\Model\User', $id);
			$form->bind($user);
			$form->get('submit')->setAttribute('value', 'Edit');
		}
		
		return new ViewModel(array(
			'form' => $form	
		));
	}
	
	/**
	 * @return void
	 */
	public function deleteAction() {
		$id = (int) $this->params()->fromRoute('id', 0);
		if ($id == 0) {
			throw new \Exception("Código obrigatório");	
		}
		
		$user = $this->getEntityManager->find('Admin\Model\User', $id);
		if ($user) {
			$this->getEntityManager()->remove($user);
			$this->getEntityManager()->flush();	
		}
		return $this->redirect()->toUrl('/admin/user');
	}
}