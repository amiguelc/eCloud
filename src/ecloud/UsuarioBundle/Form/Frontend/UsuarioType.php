<?php
namespace eCloud\UsuarioBundle\Form\Frontend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UsuarioType extends AbstractType{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
			$builder
			->add('email')
			->add('nombre_usuario')
			->add('nombre')
			->add('apellidos')
			->add('direccion')
			->add('ciudad')
			->add('pais')
		;
		}
		public function getName()
		{
			return 'ecloud_usuariobundle_usuariotype';
		}
}