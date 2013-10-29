function jsonToTable() {
    if(peticion.readyState == 4) {
	 
      if(peticion.status == 200) {
	 
		// Borrar datos anteriores
		//limpia(contenedor);
	  
        div = document.getElementById('tabla');
        		
		//JSON
		var arr = eval ("(" + peticion.responseText+ ")"); 
		
		
		// Formatear JSON en una tabla
		var tabla;
		var cab = "<tr>";
		var cuerpo = "";
	
		for(var i=0;i<arr.length;i++){
			var obj = arr[i];
			cuerpo = cuerpo + "<tr>";
			for(var key in obj){
				//Para saltar campos
				//alert(key);
				if(key=="accion" || key=="nombreFicheroNuevo" || key=="fecha"){
				
					var attrName = key;
					var attrValue = obj[key];
					cuerpo = cuerpo + "<td>" + attrValue + "</td>";
					if (i==0){
					//Guarda cabeceras de la tabla
					cab = cab + "<th>" + attrName + "</th>";
					}
				}
			}
			cuerpo = cuerpo + "</tr>";
		}
		tabla = "<table>" + cab + "</tr>" + cuerpo + "</table>";
		div.innerHTML = tabla;
		
		
      }
    }
	
  }