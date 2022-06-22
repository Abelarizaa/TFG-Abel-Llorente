<?php
// src/Form/Type/AreauserType.php
namespace App\Controller;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class AreauserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {		
		// Creo los campos
		
		
		$usuario = $options['data']['usuActual'];
	    $builder->add('email', EmailType::class, array('attr' => array(
          'value' => $usuario->getEmail()
      )));
		$builder->add('edad', NumberType::class, array("required" => false, 
													  'attr' => array(
													  'value'=> $usuario->getEdad())));	// La edad es opcional
        $builder->add('aficiones', TextType::class, array("required" => false,
														 'attr'=>array(
														 'value'=>$usuario->getAficiones())));
        $builder->add('ciudad', TextType::class, array("required" => false,
														 'attr'=>array(
														 'value'=>$usuario->getCiudad())));
        $builder->add('cambia_tu_avatar', FileType::class, array("required" => false,
													  "attr" => ['accept' => 'image/jpeg', 'image/png']
													  ));
		
		// Submit
		$builder->add('Enviar', SubmitType::class, array('label' => 'Enviar'));
    }
}