	$(document).ready(function(){
		var pagina=location.pathname;
		pagina=pagina.replace(/^\/app_dev\.php/,"");
		pagina=pagina.replace(/^\/app\.php/,"/");
		
		//Ver el menu y resaltar la parte en la que nos encontramos
		if (pagina.match(/^\/perfil/)!==null){
			$("#nav").css("visibility","visible");
			$("#menu_perfil").css("font-weight","bold");
		}else if(pagina.match(/^\/ficheros/)!==null){
			$("#nav").css("visibility","visible");
			$("#menu_ficheros").css("font-weight","bold");
		}else if(pagina.match(/^\/eventos/)!==null){
			$("#nav").css("visibility","visible");
			$("#menu_eventos").css("font-weight","bold");
		}else if(pagina.match(/^\/links/)!==null){
			$("#nav").css("visibility","visible");
			$("#menu_links").css("font-weight","bold");
		}else if(pagina.match(/^\/modificar/)!==null){
			$("#nav").css("visibility","visible");
			$("#menu_ficheros").css("font-weight","bold");
		}
	})