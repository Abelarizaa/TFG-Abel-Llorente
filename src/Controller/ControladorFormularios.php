<?php
// src/Controller/ControladorFormularios.php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\UsuarioType;
use App\Entity\Usuario;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Mensaje;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class ControladorFormularios extends AbstractController
{
	
	
/**
* @Route("/registro", name = "registro")
*/
	
public function registro(Request $request, UserPasswordEncoderInterface $encoder, MailerInterface $mailer)
	{				
	if(isset($_POST['nombre'])&& !empty($_POST['nombre'])) {
		
		// El formulario es válido, asigno los datos	
		$nombre 	= $_POST['nombre'];
		$password 		= $_POST['clave'];
		$email 		= $_POST['email'];


			
// Sacamos la extensión del fichero si se ha subido

// Le ponemos un nombre al fichero aleatorio para evitar duplicidades
$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
$codigoar = substr(str_shuffle($permitted_chars), 0, 10);	

	//Asignamos codigo de activacion y demas parametros
	$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
	$codigo = substr(str_shuffle($permitted_chars), 0, 10);	
	$entityManager = $this->getDoctrine()->getManager();
	$usubusca = $this->getDoctrine()->getRepository(Usuario::class)->find($nombre);
	if($usubusca != null){
			$mensajeconf = "Has puesto un nombre de usuario que ya existe, por favor vuelve a intentar registrarte";
			return $this->render('confirmaciones.html.twig', array('mensaje' => $mensajeconf));
	}
	$usuarios = $this->getDoctrine()->getRepository(Usuario::class);
	$usuarios = $usuarios->findBy(
    ['email' => $email]

	);
	if($usuarios != null){
		$mensajeconf = "Has puesto un correo electrónico ya registrado";
		return $this->render('confirmaciones.html.twig', array('mensaje' => $mensajeconf));
	}
	$usuario = new Usuario();
	$usuario->setUsuario($nombre);
	$usuario->setClave($encoder->encodePassword($usuario, $password));
	$usuario->setRol(0);
	$usuario->setEmail($email);
	$usuario->setActivado(0);
	$usuario->setClaveact($codigo);
	$usuario->setBloqueo(0);
	$usuario->setAvisos(0);
		$entityManager->persist($usuario);
		$entityManager->flush();
			

	 	//Enviamos el mail de registro
		$message = new email();
        $message->from(new Address('no-reply@whatschat.com', "GeniusPadel"));
        $message->to(new Address($email));
		$message->subject("Bienvenido a Geniuspadel! Activa tu cuenta");
		$message->html("<h1>Bienvenido a Geniuspadel!</h1><br/>
		Por favor, para poder usar tu cuenta haz click en el siguiente <a href=http://localhost/tfg/public/confirmacion/$nombre/$codigo>enlace</a>.<br/><br/>
		Gracias por su registro!");
		$mailer->send($message);	
			$mensajeconf = "Se ha enviado un correo electrónico para activar su cuenta";
		return $this->render('confirmaciones.html.twig', array('mensaje' => $mensajeconf));
	 
		} else {
		return $this->render('formuregistro.html.twig');
	}

} 

/**
* @Route("/recuperacionclave", name = "recuperacionclave")
*/
	
	public function recuperacionclave(Request $request, UserPasswordEncoderInterface $encoder, MailerInterface $mailer)
	{				
		// Cargo el formulario
		$form = $this->createForm(RecuperaType::class);
		
		// Recojo la respuesta
		$form->handleRequest($request);
		if($form->isSubmitted()) {
			
			// Compruebo si no es válido
			if(!$form->isValid()) {
				return new Response("ERROR: algún campo falla!");
			} 
			
		// El formulario es válido, imprimo los datos
		$datos = $form->getData();		
		$user 	= $datos['nombre'];

		$entityManager = $this->getDoctrine()->getManager();
		$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
		$codigo = substr(str_shuffle($permitted_chars), 0, 10);	
		$usuario = $entityManager->find(Usuario::class, $user);
		if($usuario == null){
			$mensajeconf = "Usuario no encontrado";
			return $this->render('confirmaciones.html.twig', array('mensaje' => $mensajeconf));
		}	
		$email = $usuario->getEmail();
		$usuario->setRecupera($codigo);
		$entityManager->persist($usuario);
		$entityManager->flush();
			

	 	
		$message = new email();
        $message->from(new Address('no-reply@geniuspadel.com', "Geniuspadel"));
        $message->to(new Address($email));
		$message->subject("Recupera tu clave en Geniuspadel");
		$message->html("<h1>Recupera tu clave Geniuspadel</h1><br/>
		Por favor, para recuperar tu contraseña haz click en el siguiente <a href=http://localhost/tfg/public/recuperacion/$user/$codigo>enlace</a>.<br/><br/> 
		Gracias!");
		$mailer->send($message);	
		$mensajeconf = "Se ha enviado un correo electrónico para restablecer su clave";
		return $this->render('confirmaciones.html.twig', array('mensaje' => $mensajeconf));
		}
		
		// En otro caso, envío el formulario
		return $this->render('formuRecupera.html.twig', array('form' => $form->createView()));
	} 
	
}
