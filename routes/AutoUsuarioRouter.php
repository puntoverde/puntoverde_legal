<?php

$router->group(['prefix'=>'auto-usuario'],function() use($router){
    $router->get('','AutoUsuarioController@getAutoUsuario');
    $router->put('/{id:[0-9]+}','AutoUsuarioController@bajaAutoUsuario');
    $router->put('/{id:[0-9]+}/color','AutoUsuarioController@changeColorAutoUsuario');
    $router->put('/{id:[0-9]+}/placas','AutoUsuarioController@changePlacasAutoUsuario');
});