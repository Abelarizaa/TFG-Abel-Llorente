<?php
// src/Entity/Reserva.php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
// =======================================================
// La clase debe implementar UserInterface y Serializable
// =======================================================

/**
 * @ORM\Entity @ORM\Table(name="reserva")
 */
class Reserva
{
	// =======================================================
	// Atributos privados
	// =======================================================

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $id_reserva;
    /**
     * @ORM\Column(type="string")
     */
    private $nombre_usuario;
     /**
     * @ORM\Column(type="integer")
     */
    private $id_pista;
     /**
     * @ORM\Column(type="string")
     */
    private $fecha; 
     /**
     * @ORM\Column(type="string")
     */
    private $hora; 
	

	// =======================================================
	// Setters y getters
	// =======================================================
public function getId_reserva(){ return $this->id_reserva; }
public function setId_reserva($id_reserva){ $this->id_reserva=$id_reserva; }
public function getNombre_usuario(){ return $this->nombre_usuario; }
public function setNombre_usuario($nombre_usuario){ $this->nombre_usuario=$nombre_usuario; }
public function getId_pista(){ return $this->id_pista; }
public function setId_pista($id_pista){ $this->id_pista=$id_pista; }
public function getFecha(){ return $this->fecha; }
public function setFecha($fecha){ $this->fecha=$fecha; }
public function getHora(){ return $this->hora; }
public function setHora($hora){ $this->hora=$hora; }

 	
	
	public function serialize(){
        return serialize(array(
            $this->id_reserva,
	        $this->nombre_usuario,
	        $this->id_pista,
			$this->fecha,
			$this->hora,
			$this->tipo_pista
        ));
    }
	
    public function unserialize($serialized){
        list (
            $this->id_reserva,
	        $this->nombre_usuario,
	        $this->id_pista,
			$this->fecha,
			$this->hora,
			$this->tipo_pista
            ) = unserialize($serialized);
    }


}