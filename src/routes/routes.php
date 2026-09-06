<?php

require_once __DIR__ . "/../services/snapshots_service.php";

$routes = [

	"POST" => [ 
		'/snapshots' => [SnapshotsService::class, 'snapshots'],
		
	],


    "GET" => [ 
		// '/snapshots' => [SnapshotsService::class, 'snapshots'],

	],

];

require_once __DIR__ . "/config_routes.php";