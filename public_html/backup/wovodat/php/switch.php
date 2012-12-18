<?php

$get = $_GET['get'];
switch ($get) {
    case 'VolcanoList':
        include_once 'Wovodat.php';
        $wovodat = new Wovodat();
        $wovodat->getVolcanoList();
        break;
    case 'EruptionList':
        include_once 'Wovodat.php';
        $wovodat = new Wovodat();
        $wovodat->getEruptionList($_GET['cavw']);
        break;
    case 'LatLon':
        include_once 'Wovodat.php';
        $wovodat = new Wovodat();
        $wovodat->getLatLon($_GET['cavw']);
        break;
    case 'AvailableStations':
        include_once 'Wovodat.php';
        $wovodat = new Wovodat();
        $wovodat->getAvailableStations($_GET['cavw']);
        break;
    case 'Stations':
        include_once 'Wovodat.php';
        $wovodat = new Wovodat();
        $wovodat->getStations($_GET['cavw'], $_GET['type']);
        break;
    case 'StationData':
        include_once 'Wovodat.php';
        $wovodat = new Wovodat();
        $wovodat->getStationData($_GET['type'], $_GET['table'], $_GET['code'], $_GET['component']);
        break;
    default:
        break;
}
?>