<?php 
ini_set('max_execution_time', -1);
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require __DIR__.'/vendor/autoload.php';

use \Gutierre69\Converter;
use \Gutierre69\Ethereum;
use \Carbon\Carbon;

$env = Dotenv\Dotenv::createImmutable(__DIR__);
$env->load();

$convert = new Converter();


$eth = new Ethereum($_ENV['RPC_ADDRESS'], $_ENV['RPC_PORT']);

$mysqli = new mysqli($_ENV['DATABASE_HOST'], $_ENV['DATABASE_USER'], $_ENV['DATABASE_PASSWORD'], $_ENV['DATABASE_NAME']);
if (mysqli_connect_errno()) die( trigger_error(mysqli_connect_error()) );


// extrai da url a URI
$script_name = $_SERVER['SCRIPT_NAME'];
$absolute_path = str_replace("index.php","", $script_name);
if(strlen($absolute_path)>1) 
    $uri = str_replace($absolute_path, "", $_SERVER['REQUEST_URI']);
else 
    $uri = $_SERVER['REQUEST_URI'];

$slug = array();

if(strlen($uri)>0) {
    $p = strpos($uri, "/");
    if($p === false) {
        $slug[0] = $uri;
    } else {
        $slug = explode("/", $uri);
    }
}

if(isset($slug[0]) && $slug[0]=="" && isset($slug[1])) {
    $ns = array();
    $c = 0;
    foreach($slug AS $s){
        if($c>0){
            $ns[] = $s;
        }
        $c++;
    }
    $slug = $ns;
}


if($_SERVER['SERVER_NAME']=="explorer.io")
    $baseurl = "http://explorer.io/";
else 
    $baseurl = "https://explorer.doric.network/";

if((isset($slug[0]) && ($slug[0]=="" || $slug[0]=="home")) || !isset($slug[0])) include "pages/home.php";
if(isset($slug[0]) && $slug[0]=="tx") include "pages/tx.php";
if(isset($slug[0]) && $slug[0]=="block") include "pages/block.php";
if(isset($slug[0]) && $slug[0]=="address") include "pages/address.php";
if(isset($slug[0]) && $slug[0]=="api") include "api.php";

if(isset($slug[0]) && $slug[0]=="blocks") include "pages/blocks.php";
if(isset($slug[0]) && $slug[0]=="transactions") include "pages/txs.php";
if(isset($slug[0]) && $slug[0]=="addresses") include "pages/addresses.php";

?>