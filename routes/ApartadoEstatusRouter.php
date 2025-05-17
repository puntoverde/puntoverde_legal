<?php

$router->group(['prefix'=>'apartado'],function() use($router){
    $router->get('','ApartadoEstatusController@getApartadosEstatus');
   });