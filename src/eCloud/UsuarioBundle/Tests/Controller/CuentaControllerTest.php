<?php

namespace eCloud\UsuarioBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CuentaControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');		
		$this->assertEquals(301, $client->getResponse()->getStatusCode(),'La portada redirige a /home');
		
		//$crawler = $client->request('GET', '/home');
		
		//Probar Login Falso
		$crawler = $client->request('POST', '/login_check', array('_username' => 'qweftrqgfdgfasdfasdfasdfasdfasdfasdfasdgtasdgvdfgdfsadfasdgtqwfsdfasdf','_password' => 'asdgfasgtaqsdfgaqfsd'));
		$response = $client->getResponse();
		$location = $response->headers->get('Location');
		$this->assertRegExp("/login$/", $location, 'Login erroneo redirige a /login');
		
		//Probar Login Correcto
		$crawler = $client->request('POST', '/login_check', array('_username' => 'q','_password' => 'q'));
		$response = $client->getResponse();
		$location = $response->headers->get('Location');
		$this->assertRegExp("/home$/", $location, 'Login correcto redirige a /home');
		
		//Comprobar si se sigue conectado
		$crawler = $client->request('GET', '/api/conectado');
		$this->assertEquals("true", $client->getResponse()->getContent(),'Se está conectado.');
		
		//Guardar Cookie de SESSION.
		$cookie = $response->headers->get('Set-Cookie');
		
		//Cargar Ficheros		
		$crawler = $client->request('GET', '/ficheros');
		$this->assertEquals(200, $client->getResponse()->getStatusCode(),'Se cargan ficheros');		
		
    }
}
