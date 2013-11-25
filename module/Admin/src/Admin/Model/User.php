<?php
namespace Admin\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFIlter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Core\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @category Admin
 * @package Model
 * 
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends Entity {
	/**
	 * @ORM\id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $username;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $password;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $name;
	
	/**
	 * @ORM\Column(type="integer")
	 */
	protected $valid
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $role;
	
	/**
	 * @return Zend\InputFilter\InputFilter
	 */
	public function getInputFilter() {
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
			
			$inputFilter->add($factory->createInput(array(
				'name' => 'id',
				'required' => true,
				'filters' => array (
					array('name' => 'Int')	
				)
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name' => 'username',
				'required' => true,
				'filters' => array (
						array('name' => 'StripTags'),
						array('name' => 'StringTrim')
				),
				'validators' => array (
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'min' => 1,
							'max' => 50
						)		
					)		
				)
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name' => 'password',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim')
				)	
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name' => 'name',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim')
				)	
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name' => 'valid',
				'required' => true,
				'filters' => array(
					array('name' => 'Int')
				)	
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name' => 'role',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim')	
				),	
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'min' => 1,
							'max' => 20		
						)		
					)		
				)
			)));
			
			$this->inputFilter = $inputFilter;
		}
		
		return $this->inputFilter;
	}
}