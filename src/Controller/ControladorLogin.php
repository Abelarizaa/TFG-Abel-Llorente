<?php
// src/Controller/ControladorLogin.php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Usuario;
use App\Entity\Pista;
use App\Entity\Reserva;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class ControladorLogin extends AbstractController
{
	/**
     * @Route("/login", name="controlador_login")
     */
    public function login(){    
        return $this->render('acceso.html.twig');
    }
	
	/**
	 * @Route("/logout", name="controlador_logout")
	 */
	public function logout()
	{
		// Este método nunca se llamará, pero es necesario para crear la ruta "/logout".
		return;
	}	
	
	
	
/**
* @Route("/reservar_pista", name="reservar_pista")
*/
public function reservar_pista()
{
// Comprobamos si el usuario al menos se ha logueado
	$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');	
	$reservas = $this->getDoctrine()->getRepository(Reserva::class)->findAll();
	$fechaActual = date('Y-m-d');
    return $this->render('reservaPista.html.twig', array('reservas'=>$reservas, 'fechactual'=>$fechaActual));
}


	
/**
 * @Route("/reservarconfecha", name="reservarconfecha")
 */
public function reservarconfecha(){	
$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');	
$fecha = $_POST['fechareserva'];
	$reservas = $this->getDoctrine()->getRepository(Reserva::class)->findAll();

	for ($i = 0; $i <count($reservas) ; $i++) {
			if($reservas[$i]->getFecha() != $fecha) {
				unset($reservas[$i]);
			} else{
				if($reservas[$i]->getNombre_usuario() != null) {
				unset($reservas[$i]);
			}
		}

	}

	$pistas = $this->getDoctrine()->getRepository(Pista::class)->findAll();
	$fechaActual = date('Y-m-d');
	return $this->render('reservarconfecha.html.twig', array('reservas'=>$reservas, "fecha" =>$fecha, "pistas" => $pistas, 'fechactual'=>$fechaActual));

}

/**
 * @Route("/confirmareserva", name="confirmareserva")
 */
public function confirmareserva(MailerInterface $mailer){	
	$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');	
	$id_reserva = $_GET['id_reserva'];
	$entityManager = $this->getDoctrine()->getManager();
	$usuario = $this->getUser()->getUsuario();
	$reserva = $this->getDoctrine()->getRepository(Reserva::class)->find($id_reserva);
	$reservafecha=$reserva->getFecha();
	$reservahora=$reserva->getHora();
	$reserva->setNombre_usuario($usuario);

	$entityManager->persist($reserva);
	$entityManager->flush();
	
	$entityManager = $this->getDoctrine()->getManager();
	$usuario = $this->getDoctrine()->getRepository(Usuario::class)->find($usuario);

	$email = $usuario->getEmail();
		//Enviamos el mail de confirmación de reserva de pista
		$message = new email();
        $message->from(new Address('no-reply@geniuspadel.com', "GeniusPadel"));
        $message->to(new Address($email));
		$message->subject("Has reservado tu pista");
		$message->html("<h1>Tu reserva con GeniusPadel</h1><br/>
		Gracias por reservar con genius padel.<br/> Tu reserva $id_reserva para el dia $reservafecha a las $reservahora ha sido confirmada!<br/><br/>
		Gracias por su reserva!<br/>
		<img src='https://i.ibb.co/NTLvTNy/logo.png'>");
		$mailer->send($message);
	return $this->redirectToRoute('misreservas');

}
/**
 * @Route("/misreservas", name="misreservas")
 */
public function misreservas(){	
	
	$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');	
	$reservas = $this->getDoctrine()->getRepository(Reserva::class);
	$usuario = $this->getUser()->getUsuario();
	$reservas = $reservas->findBy(
    ['nombre_usuario' => $usuario]

);
	
	
	return $this->render('misreservas.html.twig', array('reservas'=>$reservas));


}

/**
 * @Route("/cancelareserva", name="cancelareserva")
 */
public function cancelareserva(MailerInterface $mailer){
	$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');	
	$id = $_GET['id_reserva'];
	$entityManager = $this->getDoctrine()->getManager();
	$reserva = $this->getDoctrine()->getRepository(Reserva::class)->find($id );
	$reserva->setNombre_usuario(null);
	$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
	$codigo = substr(str_shuffle($permitted_chars), 0, 10);	
	$reserva->setId_reserva($codigo);
	$reservafecha=$reserva->getFecha();
	$reservahora=$reserva->getHora();
	$entityManager->persist($reserva);
	$entityManager->flush();
	
	$reservas = $this->getDoctrine()->getRepository(Reserva::class)->findAll();
	$usuario = $this->getUser()->getUsuario();
	$usuario = $this->getDoctrine()->getRepository(Usuario::class)->find($usuario);
		$email = $usuario->getEmail();
		//Enviamos el mail de confirmación de reserva de pista
		$message = new email();
        $message->from(new Address('no-reply@geniuspadel.com', "GeniusPadel"));
        $message->to(new Address($email));
		$message->subject("Has cancelado tu pista");
		$message->html("<h1>Tu reserva con GeniusPadel</h1><br/>
		Tu reserva $id para el dia $reservafecha a las $reservahora ha sido cancelada!<br/><br/>
		Esperamos que vuelvas a reservar con nosotros!");
		$mailer->send($message);
	
	return $this->redirectToRoute('misreservas');
	}
	
/**
 * @Route("/estadoreservas", name="estadoreservas")
 */
public function estadoreservas(){	
	$this->denyAccessUnlessGranted('ROLE_ADMIN');
	$reservas = $this->getDoctrine()->getRepository(Reserva::class)->findAll();
	
	if(isset($_POST['fecha'])){
		$reservas = $this->getDoctrine()->getRepository(Reserva::class)->findBy(
		['fecha' => $_POST['fecha']]

		);
	}
	return $this->render('estadoreservas.html.twig', array('reservas'=>$reservas));


	}

/**
 * @Route("/verpistas", name="verpistas")
 */
public function verpistas(){
	$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');	
	$pistas = $this->getDoctrine()->getRepository(Pista::class)->findAll();
	return $this->render('verpistas.html.twig', array('pistas'=>$pistas));
	}
/**
 * @Route("/anularreserva", name="anularreserva")
 */
public function anularreserva(MailerInterface $mailer){	
	$this->denyAccessUnlessGranted('ROLE_ADMIN');
	$id = $_GET['id'];
	$entityManager = $this->getDoctrine()->getManager();
	$reserva = $this->getDoctrine()->getRepository(Reserva::class)->find($id);
	$nombreUsu = $reserva->getNombre_usuario();
	$reservafecha=$reserva->getFecha();
	$reservahora=$reserva->getHora();
	$reserva->setNombre_usuario(null);
	$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
	$codigo = substr(str_shuffle($permitted_chars), 0, 10);	
	$reserva->setId_reserva($codigo);
	
	$entityManager->persist($reserva);
	$entityManager->flush();
	
	$reservas = $this->getDoctrine()->getRepository(Reserva::class)->findAll();
	
		$usuario = $this->getDoctrine()->getRepository(Usuario::class)->find($nombreUsu);
		$email = $usuario->getEmail();
		//Enviamos el mail de confirmación de reserva de pista
		$message = new email();
        $message->from(new Address('no-reply@geniuspadel.com', "GeniusPadel"));
        $message->to(new Address($email));
		$message->subject("Tu pista ha sido cancelada");
		$message->html("<h1>Tu reserva con GeniusPadel</h1><br/>
		Tu reserva $id para el dia $reservafecha a las $reservahora ha sido cancelada por el centro deportivo, disculpa las molestias<br/><br/>
		El equipo de GeniusPadel");
		$mailer->send($message);
	
	return $this->redirectToRoute('estadoreservas');
	}

/**
 * @Route("/anulardisponibilidad", name="anulardisponibilidad")
 */
public function anulardisponibilidad(){	
	$this->denyAccessUnlessGranted('ROLE_ADMIN');
	$id = $_GET['id'];
	$entityManager = $this->getDoctrine()->getManager();
	$reserva = $this->getDoctrine()->getRepository(Reserva::class)->find($id );
	$usureserva = $reserva->getNombre_usuario();
	if($usureserva != null ){
		return $this->redirectToRoute('estadoreservas');
	}
	$entityManager->remove($reserva);
	$entityManager->flush();
	
	$reservas = $this->getDoctrine()->getRepository(Reserva::class)->findAll();
	return $this->redirectToRoute('estadoreservas');
}
	
/**
 * @Route("/gestionpolideportivo", name="gestionpolideportivo")
 */
public function gestionpolideportivo(){	
	$this->denyAccessUnlessGranted('ROLE_ADMIN');
	$fechaActual = date('Y-m-d');
	return $this->render('gestionpolideportivo.html.twig', array("fecha" =>$fechaActual));

	}
/**
 * @Route("/abrirpolideportivo", name="abrirpolideportivo")
 */
public function abrirpolideportivo(MailerInterface $mailer){	
	$this->denyAccessUnlessGranted('ROLE_ADMIN');
	$fecha = $_POST['fecha'];
	$horainicio = $_POST['horainicio'];
	$horafin = $_POST['horafin'];
	$primero =$horainicio[0];
	$segundo =$horainicio[1];
	$horainicio = $primero.$segundo;
	$primero =$horafin[0];
	$segundo =$horafin[1];
	$horafin = $primero.$segundo;
	$noanadas = false;
	if(isset($_POST['abrir'])) {
		for($i=$horainicio; $i<$horafin; $i++) {
		$pistas = $this->getDoctrine()->getRepository(Pista::class)->findAll();
		foreach($pistas as $pista) {

			$hora = $i.":00:00";
			
			$reservas = $this->getDoctrine()->getRepository(Reserva::class)->findBy(
			array(
				'id_pista' => $pista->getId(),
				'fecha' => $fecha,
				'hora' => $hora
				)
			);
			
			if(count($reservas)==0){
			$idPista = $pista->getId();
			$date = strtotime($i);
			$reserva= new Reserva();
			$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
			$codigo = substr(str_shuffle($permitted_chars), 0, 10);	
			$reserva->setId_reserva($codigo);
			$reserva->setId_pista($idPista);
			$reserva->setFecha($fecha);
			$reserva->setHora($hora);
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($reserva);
			$entityManager->flush();	
				}
			}

		}
	}
	if(isset($_POST['cerrar'])) {
	$entityManager = $this->getDoctrine()->getManager();
	$reservas = $this->getDoctrine()->getRepository(Reserva::class)->findBy(
	array(
		'fecha' => $fecha
		)
	);
		foreach($reservas as $reserva) { 
		$horaReserva = $reserva->getHora();
		if($horaReserva>=$horainicio && $horaReserva<$horafin) {
			
		if($reserva->getNombre_usuario()!=null){
		$idreserva=$reserva->getId_reserva();
		$usuario = $this->getDoctrine()->getRepository(Usuario::class)->find($reserva->getNombre_usuario());
		$email = $usuario->getEmail();
		//Enviamos el mail de confirmación de reserva de pista
		$message = new email();
        $message->from(new Address('no-reply@geniuspadel.com', "GeniusPadel"));
        $message->to(new Address($email));
		$message->subject("Tu reserva ha sido anulada");
		$message->html("<h1>Tu reserva con GeniusPadel</h1><br/>
		Tu reserva $idreserva para el dia $fecha a las $horaReserva ha sido cancelada debido a el cierre del centro deportivo, puedes reservar otro día u hora chequeando en nuestra web. <br/>Disculpa las molestias.<br/><br/>
		El equipo de GeniusPadel");
		$mailer->send($message);
			}
			$entityManager->remove($reserva);
			$entityManager->flush();
			}
		}
	}


	
	return $this->redirectToRoute('gestionpolideportivo');

	}
/**
 * @Route("/gestionpistas", name="gestionpistas")
 */
public function gestionpistas(){	
	
	$this->denyAccessUnlessGranted('ROLE_ADMIN');
	$pistas = $this->getDoctrine()->getRepository(Pista::class)->findAll();
	
	return $this->render('gestionPistas.html.twig', array("pistas" =>$pistas));

	}

/**
 * @Route("/cambiopista", name="cambiopista")
 */
public function cambiopista(MailerInterface $mailer){	
	$this->denyAccessUnlessGranted('ROLE_ADMIN');
	$idpistanuevo = $_POST["idpista"];
	$idpista = $_POST["idpista"];
	$tipopista = $_POST["tipopista"];
	$fotopista = $_FILES['fotopista']['name'];
	if(isset($_POST["borrar"])) {
	$reservas = $this->getDoctrine()->getRepository(Reserva::class)->findBy(
	array(
		'id_pista' => $idpista
		)
	);
		foreach($reservas as $reserva) {
			$nombreUsu = $reserva->getNombre_usuario();
			if($nombreUsu != null) {
		$id = $reserva->getId_reserva();
		$reservafecha = $reserva->getFecha();
		$reservahora = $reserva->getHora();		
		$usuario = $this->getDoctrine()->getRepository(Usuario::class)->find($nombreUsu);
		$email = $usuario->getEmail();
		//Enviamos el mail de eliminación de reserva de pista
		$message = new email();
        $message->from(new Address('no-reply@geniuspadel.com', "GeniusPadel"));
        $message->to(new Address($email));
		$message->subject("Tu reserva ha sido cancelada");
		$message->html("<h1>Tu reserva con GeniusPadel</h1><br/>
		Tu reserva $id para el dia $reservafecha a las $reservahora ha sido cancelada por el centro deportivo, debido a que la pista ya no existe, disculpa las molestias<br/>
		Puedes comprobar si quedan pistas disponibles para ese día en nuestra web.
		<br/>
		Un saludo.<br/>
		El equipo de GeniusPadel");
		$mailer->send($message);
			}
			
			$id_pistareserva = $reserva->getId_pista();
			if($id_pistareserva == $idpista) {
				$entityManager = $this->getDoctrine()->getManager();
				$entityManager->remove($reserva);
				$entityManager->flush();
			}
		}
		
		$pista = $this->getDoctrine()->getRepository(Pista::class)->find($idpista);
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($pista);
		$entityManager->flush();
		return $this->redirectToRoute('gestionpistas');
	}
	
	
	if(isset($_POST["cambio"])) {
		$pista = $this->getDoctrine()->getRepository(Pista::class)->find($idpista);
		$pista->setTipo_pista($tipopista);
	} else if(isset($_POST["nueva"])) {
		$pista = new Pista();
		$pista->setId($idpista);
		$pista->setFotopista("default.png");
	}
	if($fotopista!="") {
		$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
		$codigoar = substr(str_shuffle($permitted_chars), 0, 10);
		// Sacamos la extensión del fichero si se ha subido
		$ext = pathinfo($_FILES['fotopista']['name'], PATHINFO_EXTENSION);
		//Establecemos ruta para guardar los ficheros
		$path = "uploads/". $codigoar.".".$ext;
		$fotopista = $codigoar.".".$ext;

		if($ext=="jpg"||$ext=="png"||$ext=="jpeg") {
		move_uploaded_file($_FILES['fotopista']['tmp_name'], $path);
		$pista->setFotopista($fotopista);

		}
	} 

		$pista->setTipo_pista($tipopista);
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($pista);
		$entityManager->flush();
		
	
	

	return $this->redirectToRoute('gestionpistas');

	}
	
/**
 * @Route("/area_usuario", name="area_usuario")
 */
public function area_usuario(){	
	 $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');	

	$usuario = $this->getDoctrine()->getRepository(Usuario::class)->find($this->getUser()->getUsuario());
	
	if(isset($_POST['cambio'])){
		$usuario = $this->getDoctrine()->getRepository(Usuario::class)->find($this->getUser()->getUsuario());
		$usuario->setNombre($_POST['nombre']);
		$usuario->setApellidos($_POST['apellidos']);
		$usuario->setEmail($_POST['email']);
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($usuario);
		$entityManager->flush();
	}
	
	return $this->render('area_usuario.html.twig', array("usuario" =>$usuario));
	
	}
	
/**
 * @Route("/gestionusuarios", name="gestionusuarios")
 */
public function gestionsuarios(MailerInterface $mailer){
	$this->denyAccessUnlessGranted('ROLE_ADMIN');
	$usuarios = $this->getDoctrine()->getRepository(Usuario::class)->findAll();
	$usuario = $this->getUser()->getUsuario();
	for ($i = 0; $i <=count($usuarios) ; $i++) {
   	if(!strcmp($usuarios[$i]->getUsuario(), $this->getUser()->getUsuario())) {
	unset($usuarios[$i]);
		}
	}
	
	if(isset($_POST['daraviso'])){
		$usuaviso = $this->getDoctrine()->getRepository(Usuario::class)->find($_POST['usuario']);
		$avisos = $usuaviso->getAvisos();
		$usuaviso->setAvisos($avisos+1);
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($usuaviso);
		$entityManager->flush();
		
		$email = $usuaviso->getEmail();
		$nombre = $usuaviso->getNombre();
		$avisos = $usuaviso->getAvisos();
		//Enviamos el mail de eliminación de reserva de pista
		$message = new email();
        $message->from(new Address('no-reply@geniuspadel.com', "GeniusPadel"));
        $message->to(new Address($email));
		$message->subject("Has recibido un aviso");
		$message->html("<h1>Has recibido un aviso por mal uso del centro</h1><br/>
		Hola $nombre ,
		<br/>
		Has recibido un aviso por mal uso del centro deportivo, esto puede ser debido a que no has acudido a una reserva o a que se han detectado desperfectos tras el uso de la pista. Tienes $avisos avisos. Si recibes más avisos puedes ser bloqueado.
		<br/>
		Un saludo.<br/>
		El equipo de GeniusPadel :)");
		$mailer->send($message);
	}
	if(isset($_POST['quitaraviso'])){
		$usuaviso = $this->getDoctrine()->getRepository(Usuario::class)->find($_POST['usuario']);
		$avisos = $usuaviso->getAvisos();
		$usuaviso->setAvisos($avisos-1);
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($usuaviso);
		$entityManager->flush();
		
		$email = $usuaviso->getEmail();
		$nombre = $usuaviso->getNombre();
		$avisos = $usuaviso->getAvisos();
		//Enviamos el mail de eliminación de reserva de pista
		$message = new email();
        $message->from(new Address('no-reply@geniuspadel.com', "GeniusPadel"));
        $message->to(new Address($email));
		$message->subject("Se te ha retirado un aviso");
		$message->html("<h1>Tienes un aviso menos!</h1><br/>
		Hola $nombre ,
		<br/>
		Se te ha quitado un aviso. Tienes $avisos avisos. Gracias por el buen uso de nuestras pistas!
		<br/>
		Un saludo.<br/>
		El equipo de GeniusPadel :)");
		$mailer->send($message);
	}
	
	if(isset($_POST['bloquear'])){
		$nombreusu = $_POST['usuario'];
		$usuaviso = $this->getDoctrine()->getRepository(Usuario::class)->find($_POST['usuario']);
		$usuaviso->setBloqueo(true);
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($usuaviso);
		$entityManager->flush();
		
		$email = $usuaviso->getEmail();
		$nombre = $usuaviso->getNombre();
		$avisos = $usuaviso->getAvisos();
		
		//Enviamos el mail de eliminación de reserva de pista
		$message = new email();
        $message->from(new Address('no-reply@geniuspadel.com', "GeniusPadel"));
        $message->to(new Address($email));
		$message->subject("Se te ha bloqueado la reserva de pistas");
		$message->html("<h1>Has sido bloqueado de la aplicación</h1><br/>
		Hola $nombre ,
		<br/>
		Has sido bloqueado por un mal uso continuado de las pistas deportivas, a partir de este momento no podrás realizar nuevas reservas, y tus reservas actuales han sido canceladas. Serás informado cuando se te retire el bloqueo. Si tienes cualquier duda adicional puedes contactar con nuestro equipo en info@geniuspadel.com
		<br/>
		Un saludo.<br/>
		El equipo de GeniusPadel :)");
		$mailer->send($message);
		
			$reservas = $this->getDoctrine()->getRepository(Reserva::class)->findBy(
	array(
		'nombre_usuario' => $nombreusu
		)
	);
		foreach($reservas as $reserva) {
			$reserva->setNombre_usuario(null);
			$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
			$codigo = substr(str_shuffle($permitted_chars), 0, 10);	
			$reserva->setId_reserva($codigo);	
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($reserva);
			$entityManager->flush();
		}
		
	}
	
	if(isset($_POST['desbloquear'])){
		$usuaviso = $this->getDoctrine()->getRepository(Usuario::class)->find($_POST['usuario']);
		$usuaviso->setBloqueo(false);
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($usuaviso);
		$entityManager->flush();
		
		$email = $usuaviso->getEmail();
		
		$nombre = $usuaviso->getNombre();
		
		$message = new email();
        $message->from(new Address('no-reply@geniuspadel.com', "GeniusPadel"));
        $message->to(new Address($email));
		$message->subject("Has sido desbloqueado");
		$message->html("<h1>Has sido desbloqueado de la aplicación</h1><br/>
		Hola $nombre ,
		<br/>
		Has sido desbloqueado de Geniuspadel, a partir de ahora puedes volver a realizar nuevas reservas, por favor te recordamos por el bien de todos los usuarios hacer un buen uso de la aplicación, gracias por confiar en nosotros!
		<br/>
		Un saludo.<br/>
		El equipo de GeniusPadel :)");
		$mailer->send($message);
	}
	
 	return $this->render('gestionusuarios.html.twig', array("usuarios" =>$usuarios));
	
	}
}



	
