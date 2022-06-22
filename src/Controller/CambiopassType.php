<?php
// src/Form/Type/CambiopassType.php
namespace App\Controller;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CambiopassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {		
		// Creo los campos
        $builder->add('nueva_contrasena', PasswordType::class);
		
		// Submit
		$builder->add('Enviar', SubmitType::class, array('label' => 'Enviar'));
    }
}