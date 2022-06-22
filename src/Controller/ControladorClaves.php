<?php
// src/Controller/ControladorClaves.php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Mensaje;
use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class ControladorClaves extends AbstractController
{
/**
 * @Route("/confirmacion/{usuario}/{clave}", name="confirmacion")
 */
public function confirmacion($usuario,$clave)
{
//Recuperamos el usuario para activar la cuenta
		$usuario = $this->getDoctrine()->getRepository(Usuario::class)->find($usuario);
		$claveact = $usuario->getClaveact();		
	if (strcmp($claveact, $clave) !== 0) {
    	$mensajeconf = "Ha habido algún error al activar tu usuario";
			return $this->render('confirmaciones.html.twig', array('mensaje' => $mensajeconf));
	}
     
	    $entityManager = $this->getDoctrine()->getManager();
		$usuario->setClaveact(null);
		$usuario->setActivado(true);
		$entityManager->persist($usuario);
		$entityManager->flush();
		$mensaje = "Usuario activado, ¡Ya puedes loguearte!";
		return $this->render('acceso.html.twig', array('mensaje'=>$mensaje));
	}
	

	
/**
 * @Route("/recuperacion/{usuario}/{clave}", name="recuperacion")
 */
public function recuperacion($usuario,$clave, Request $request,  UserPasswordEncoderInterface $encoder)
{
	$usuario = $this->getDoctrine()->getRepository(Usuario::class)->find($usuario);
	if($usuario == null){
			$mensajeconf = "Estas intentando recuperar la clave de un usuario que no existe";
			return $this->render('confirmaciones.html.twig', array('mensaje' => $mensajeconf));
		}	
	$claveact = $usuario->getRecupera();		
	if (strcmp($claveact, $clave) !== 0) {
			$mensajeconf = "Ha habido algún error al recuperar tu clave";
			return $this->render('confirmaciones.html.twig', array('mensaje' => $mensajeconf));
	}

		// Cargo el formulario
		$form = $this->createForm(CambiopassType::class);
		
		// Recojo la respuesta
		$form->handleRequest($request);
		if($form->isSubmitted()) {
			
			// Compruebo si no es válido
			if(!$form->isValid()) {
				$mensajeconf = "Error! Algun campo falla";
				return $this->render('confirmaciones.html.twig', array('mensaje' => $mensajeconf));
			}
			$datos = $form->getData();	
			$password = $datos['nueva_contrasena'];
			$usuario->setClave($encoder->encodePassword($usuario, $password));
			$usuario->setRecupera(null);
	       $entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($usuario);
			$entityManager->flush();
    	$mensaje = "Contraseña cambiada, ya puede loguearse";
		return $this->render('acceso.html.twig', array('mensaje'=>$mensaje));
		}
		
		return $this->render('formuCambio.html.twig', array('form' => $form->createView()));
	}
}

	

