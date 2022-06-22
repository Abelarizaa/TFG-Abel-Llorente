<?php
// src/Entity/Pista.php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
// =======================================================
// La clase debe implementar UserInterface y Serializable
// =======================================================

/**
 * @ORM\Entity @ORM\Table(name="pista")
 */
class Pista
{
	// =======================================================
	// Atributos privados
	// =======================================================

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string")
     */
    private $tipo_pista;
    /**
     * @ORM\Column(type="string")
     */
    private $fotopista;
 

	// =======================================================
	// Setters y getters
	// =======================================================

public function getId(){ return $this->id; }
public function setId($id){ $this->id=$id; }
public function getTipo_pista(){ return $this->tipo_pista; }
public function setTipo_pista($tipo_pista){ $this->tipo_pista=$tipo_pista; }
public function getFotopista(){ return $this->fotopista; }
public function setFotopista($fotopista){ $this->fotopista=$fotopista; }
	
 	
	
	public function serialize(){
        return serialize(array(
            $this->id,
			$this->tipo_pista,
			$this->fotopista
        ));
    }
	
    public function unserialize($serialized){
        list (
            $this->id,
			$this->tipo_pista,
			$this->fotopista
            ) = unserialize($serialized);
    }

}