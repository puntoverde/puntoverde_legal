<?php

$router->group(['prefix'=>'invitados'],function() use($router){
    
    


    $router->get('','InvitadoController@getInvitados');//obtiene todos los invitados

    $router->get('/{id:[0-9]+}','InvitadoController@getInvitado');//obtiene un invitado 

    $router->get('/{id:[0-9]+}/historico','InvitadoController@getHistoricoInvitado');//obtiene el historico de un invitado 

    $router->get('/Socio_invita','InvitadoController@getSociosInvitaByNombre');//obtiene socio por nomkbre

    $router->get('/socios_invitan','InvitadoController@getListaSociosInvitan');//obtiene listado de socios que pueden invitar

    $router->get('/accion_disponible','InvitadoController@getAccionesLibresInvitados');//obtiene acciones disponibles con cupo antes de 20 de tipo invitado...

    $router->post('','InvitadoController@setInvitado');//agrega al invitado

    $router->put('/{id:[0-9]+}','InvitadoController@reingresoInvitado');//reingresa invitado 
    
    $router->get('/cargos-historico','InvitadoController@getInvitadosCargos');//obtiene todos los iinvitados con cargos actuales y aquellos pasados que no esten pagados
    
    $router->delete('/eliminar','InvitadoController@deleteInvitado');//elimina al invitado recibe id de historico invitado y el id de socios

        
});