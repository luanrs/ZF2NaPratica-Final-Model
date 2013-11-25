<?php
namespace Admin\Form;

use Zend\Form\Form;

class User extends Form {
	public function __construct() {
		parent::__construct('user');
		$this->setAttribute('method', 'post');
		$this->setAttribute('action', '/admin/user/save');
		
		$this->add(array(
			'name' => 'id',
			'attributes' => array(
				'type' => 'hidden'		
			)	
		));
		
		$this->add(array(
			'name' => 'name',
			'attributes' => array(
				'type' => 'text'		
			),
			'options' => array(
				'label' => 'Nome'		
			)
		));
		
		$this->add(array(
			'name' => 'username',
			'attributes' => array(
				'type' => 'text'		
			),
			'options' => array(
				'label' => 'Username'
			)
		));
		
		$this->add(array(
			'name' => 'password',
			'attributes' => array(
				'type' => 'password'		
			),
			'options' => array(
				'label' => 'Senha'		
			)
		));
		
		$this->add(array(
			'name' => 'role',
			'attributes' => array(
				'type' => 'text'		
			),
			'options' => array(
				'label' => 'Papel'		
			)
		));
		
		$this->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'value' => 'Enviar',
				'id' => 'submitbutton'		
			)	
		));
	}
}