<?php
namespace Admin\Model;

use Core\Test\ModelTestCase;
use Admin\Model\User;
use Zend\InputFilter\InputFilterInterface;

/**
 * @group Model
 */

class UserTest extends ModelTestCase {
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	private $em;
	
	public function setup() {
		parent::setup();
		$this->em = $this->serviceManager->get('Doctrine\ORM\EntityManager');
	}
	
	public function testGetInputFilter() {
		$user = new User();
		$if = $user->getInputFilter();
		$this->assertInstanceOf("Zend\InputFilter\InputFilter", $if);
		return $if;
	}
	
	/**
	 * @depends testGetInputFilter
	 */
	public function testInputFilterValid($if) {
		$this->assertEquals(6, $if->count());
		$this->assertTrue(
			$if->has('id')	
		);
		
		$this->assertTrue(
			$if->has('username')
		);
		
		$this->assertTrue(
			$if->has('password')	
		);
		
		$this->assertTrue(
			$if->has('name')
		);
		
		$this->assertTrue(
			$if->has('valid')	
		);
		
		$this->assertTrue(
			$if->has('role')	
		);
	}
	
	/**
	 * @expectedException Core\Model\EntityException
	 */
	public function testInputFilterInvalidoUsername() {
		$user = new User();
		$user->username = 'Lorem Ipsum e simplesmente uma simulacao de texto da industria tipografica e de 
		impressos. Lorem Ipsum e simplesmente uma simulacao de texto da indústria tipografica e de impressos';
	}
	
	/**
	 * @expectedException Core\Model\EntityException
	 */
	public function testInputFilterInvalidoRole() {
		$user = new User();
		$user->role = 'Lorem Ipsum e simplesmente uma simulacao de texto da industria tipografica e de 
		impressos. Lorem Ipsum e simplesmente uma simulacao de texto da indústria tipografica e de impressos';
	}
	
	public function testInsert() {
		$user = $this->addUser();
		$this->assertEquals('Steve Jobs',$user->name);
		$this->assertEquals(1, $user->id);
	}
	
	/**
	 * @expectedException Core\Model\EntityException
	 * @expectedExceptionMessage Input inválido: username =
	 */
	public function testInsertInvalido() {
		$user = new User();
		$user->name = 'teste';
		$user->username = '';
		
		$this->em->persist($user);
		$this->em->flush();
		//$saved = $this->getTable('Admin\Model\User')->save($user);
	}
	
	public function testUpdate() {
		//$tableGateway = $this->getTable('Admin\Model\User');
		$user = $this->addUser();
		
		$id = $user->id;
		
		$this->assertEquals(1, $id);
		
		//$user = $tableGateway->get($id);
		$user = $this->em->find('Admin\Model\User', $id);
		$this->assertEquals('Steve Jobs', $user->name);
		
		$user->name = 'Bill <br>Gates';
		//$updated = $tableGateway->save($user);
		$this->em->persist($user);
		$this->em->flush();
		
		//$user = $tableGateway->get($id);
		$user = $this->em->find('Admin\Model\User', $id);
		$this->assertEquals('Bill Gates', $user->name);
	}
	
	/**
	 * @expectedException Core\Model\EntityException
	 * @expectedExceptionMessage Could not find row 1
	 */
	public function testDelete() {
		//$tableGateway = $this->getTable('Admin\Model\User');
		$user = $this->addUser();
		
		$id = $user->id;
		
		$user = $this->em->find('Admin\Model\User',$id);
		$this->em->remove($user);
		$this->em->flush();
		
		$user = $this->em->find('Admin\Model\User',$id);
		$this->assertNull($user);
		
		//$deleted = $tableGateway->delete($id);
		//$this->assertEquals(1, $deleted);
		//$user = $tableGateway->get($id);
	}
	
	private function addUser() {
		$user = new User();
		$user->username = 'steve';
		$user->password = md5('apple');
		$user->name = 'Steve <b>Jobs</b>';
		$user->valid = 1;
		$user->role = 'admin';
		
		$this->em->persist($user);
		$this->em->flush();
		//$saved = $this->getTable('Admin\Model\User')->save($user);
		return $user;
	}
}