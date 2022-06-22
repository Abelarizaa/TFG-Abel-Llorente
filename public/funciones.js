
function myFunction() {
	var fecha = document.getElementById("myDate").value;
	return  document.getElementById("myDate").value;
  }
  
		function ver_mensaje(){
		var xhttp = new XMLHttpRequest();  
		var mivar = document.getElementById("myDate").value;
		xhttp.onreadystatechange = function() {

		if (this.readyState == 4 && this.status == 200) {  
									
			var mensajes = JSON.parse(this.response);	
			var lista = document.createElement("table");
			var encabezado = document.createElement("tr");
			lista.insertAdjacentElement("afterbegin", encabezado);
			
			// Para cada elemento del array 
			for(var i = 0; i < mensajes.length; i++){
				var elem = document.createElement("tr");
				elem.innerHTML = "<td><strong>Mensaje de: </strong>"+mensajes[i]['id_remitente']+"</br><strong>Fecha de envío: </strong>"+mensajes[i]['fecha']+"<br/>"+mensajes[i]['mensaje'];
				lista.insertAdjacentElement("beforeend",elem);		
			}
			var body = document.getElementById("principal");
			
			// eliminar el contenido actual
			body.innerHTML = "";
			body.appendChild(lista);
			removestyle(id);

	}
		};
		xhttp.open("GET", "ver_mensaje_entrada/"+mivar, true);     
		xhttp.send(); 
		
		return false;
	}


		function borrar_mensaje(id){
		var xhttp = new XMLHttpRequest();  
		var mivar = id;
		xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {  
								


	}
		};
		xhttp.open("GET", "eliminar_mensaje/"+mivar, true);     
		xhttp.send(); 
		location.reload();
		return false;
			
	}
function borrar_mensaje_salida(id){
		var xhttp = new XMLHttpRequest();  
		var mivar = id;
		xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {  
								


	}
		};
		xhttp.open("GET", "eliminar_mensaje_salida/"+mivar, true);     
		xhttp.send(); 
		location.reload();
		return false;
			
	}
function removestyle(id) {
        document.getElementById(
          id).removeAttribute("style");
    };

function insertar_mensaje(id,remitente,idfila){
		console.log("1");
				var elem = document.createElement("td");
		
				elem.innerHTML =' <textarea id=id'+idfila+' placeholder="Inserta aqui tu respuesta"></textarea><button onclick=\' responder("'+id+'","'+remitente+'","id'+idfila+'");\'>Enviar respuesta</button>';
			console.log("1");
				var body = document.getElementById(idfila);
			
			// eliminar el contenido actual
		console.log("1");
				body.append(elem);
		console.log("1");

}

function bloquear(id,opcion){
		var xhttp = new XMLHttpRequest();  
		var mivar = id;
		xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {  
								

	}
		};
		xhttp.open("GET", "bloquear/"+mivar+"/"+opcion, true);     
		xhttp.send(); 
		location.reload();
		return false;
			
	}

function responder(destinatario,remitente,idfila){
	console.log("hola");
		var xhttp = new XMLHttpRequest();  
		var rmt = remitente;
		var dst = destinatario;
		var msj = document.getElementById(idfila).value;
		xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {  
				location.reload();					

		}
		};
		xhttp.open("GET", "responder_mensaje/"+rmt+"/"+dst+"/"+msj, true);     
		xhttp.send(); 
		return false;
			
	}

function ver_mensaje_salida(id){
		var xhttp = new XMLHttpRequest();  
		var mivar = id;
		xhttp.onreadystatechange = function() {

		if (this.readyState == 4 && this.status == 200) {  
									
			var mensajes = JSON.parse(this.response);	
			var lista = document.createElement("table");
			var encabezado = document.createElement("tr");
			lista.insertAdjacentElement("afterbegin", encabezado);
			
			// Para cada elemento del array 
			for(var i = 0; i < mensajes.length; i++){
				var elem = document.createElement("tr");
				elem.innerHTML = "<td><strong>Mensaje para: </strong>"+mensajes[i]['id_remitente']+"</br><strong>Fecha de envío: </strong>"+mensajes[i]['fecha']+"<br/>"+mensajes[i]['mensaje'];
				lista.insertAdjacentElement("beforeend",elem);		
			}
			var body = document.getElementById("principal");
			
			// eliminar el contenido actual
			body.innerHTML = "";
			body.appendChild(lista);


	}
		};
		xhttp.open("GET", "ver_mensaje_salida/"+mivar, true);     
		xhttp.send(); 
		
		return false;
	}
