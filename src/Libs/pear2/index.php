<?php
use PEAR2\Net\RouterOS;
use PEAR2\Net\RouterOS\Client;

require_once 'vendor/Autoload.php';
try {
    $client = new RouterOS\Client('123.253.96.22', 'api_user', '#System:Users.C');
    echo 'OK';
} catch (Exception $e) {
    die($e);
}
//die();
//$addRequest = new RouterOS\Request('/ppp/secret/add');
//
//$addRequest->setArgument('name', 'api_1234567');
//$addRequest->setArgument('profile', '1M');
//$addRequest->setArgument('password', '12356');
//$addRequest->setArgument('service', 'pptp');
//$addRequest->setArgument('comment', 'create user from pear2 lib');
//$addRequest->setArgument('disabled', 'yes');
//
//if ($client->sendSync($addRequest)->getType() !== RouterOS\Response::TYPE_FINAL) {
//    die("Error when creating ARP entry for '192.168.88.101'");
//}
//exit;
/* Working example */
//$responses = $client->sendSync(new RouterOS\Request('/ppp/secret/getall'));
//
//foreach ($responses as $response) {
//    if ($response->getType() === RouterOS\Response::TYPE_DATA) {
//        print_r( $response );
//    }
//}
// exit;

/* Working example */

// Tabla
//echo "<table align='center' border='1' bordercolor='black'><form action='' method='POST'>";
//echo "<tr bgcolor='#D8D8D8'><td align=left size=3>Nombre</td><td align=left size=3>Servicio</td><td size=3>Tiempo Activo</td><td align=left size=3>Direccion</td><td align=left size=3>Reiniciar</td></tr>";
//
////Actualizar pagina
//echo "<meta http-equiv='refresh' content='2'>";
//
//$ppps = $client->sendSync(new RouterOS\Request('/ppp/secret/print'))->getAllOfType(RouterOS\Response::TYPE_DATA);
//
//foreach ($ppps as $ppp) {
//    $id = $ppp('.id');
//    echo "<tr>";
//    echo "<td>". $ppp('name') ."</td>";
//    echo "<td>" . $ppp('service'). "</td>";
//    echo "<td>" . $ppp('uptime'). "</td>";
//    echo "<td>". $ppp('address') ."</td>";
//    echo "<td><input type='submit' value='Reiniciar' name='Reiniciar' /></td></tr>";
//}
//
//echo  "</form></table>";
//try{
//    $util = new RouterOS\Util($client = new RouterOS\Client('123.253.96.22', 'api_user', '#System:Users.C'));
//    foreach ($util->setMenu('/log')->getAll() as $entry) {
//        echo $entry('time') . ' ' . $entry('topics') . ' ' . $entry('message') . "<br/>";
//    }
//} catch (Exception $e) {
//    die($e);
//}

//Not Working
//$printRequest = new RouterOS\Request('/interface pppoe-client monitor');
//
//$id = $client->sendSync($printRequest)->getAllOfType(RouterOS\Response::TYPE_DATA);
//foreach ($id as $response) {
//    print_r( $response );
//    echo $response('uptime'), '--', $response('name'), '--', $response('address'), "\n";
//}
//exit;

$addRequest = new RouterOS\Request('/ip/arp/add');

$addRequest->setArgument('address', '192.168.88.101');
$addRequest->setArgument('mac-address', '00:00:00:00:00:01');
$addRequest->setArgument('interface', 'ether5');
$addRequest->setTag('arp1');
$client->sendAsync($addRequest);

$client->loop();

$responses = $client->extractNewResponses();
foreach ($responses as $response) {
    if ($responses->getType() !== RouterOS\Response::TYPE_FINAL) {
        echo "Error with {$response->getTag()}!\n";
    } else {
        echo "OK with {$response->getTag()}!\n";
    }
}