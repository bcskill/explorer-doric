<?php 
    header('Content-Type: application/json');
    ini_set('max_execution_time', -1);
    ini_set('display_errors',1);
    ini_set('display_startup_errors',1);
    error_reporting(E_ALL);

    require __DIR__.'/vendor/autoload.php';

    $env = Dotenv\Dotenv::createImmutable(__DIR__);
    $env->load();

    use \Gutierre69\Converter;
    use \Gutierre69\Ethereum;
    use \Carbon\Carbon;

    $convert = new Converter();

    $mysqli = new mysqli($_ENV['DATABASE_HOST'], $_ENV['DATABASE_USER'], $_ENV['DATABASE_PASSWORD'], $_ENV['DATABASE_NAME']);
    if (mysqli_connect_errno()) die( trigger_error(mysqli_connect_error()) );

    $return = array();

    if($_REQUEST['action']=="lasts_blocks") {

        $sql        = "SELECT * FROM blocks ORDER BY timestamp DESC LIMIT ".$_REQUEST['size'];
        $query      = $mysqli->query($sql);
        if (!$query) die("Error: ".$mysqli->error);
        if($query->num_rows>0) {
            while($l  = $query->fetch_array()){

                $sql2        = "SELECT * FROM transactions WHERE blockHash='".$l['hash']."'";
                $query2      = $mysqli->query($sql2);

                $l['transactions'] = $query2->num_rows;
                $l['hex_number'] = "0x".dechex($l['number']);

                //$l['timestamp'] = date("Y-m-d H:i:s", hexdec($l['timestamp']));

                //$l['forHumans'] = Carbon::createFromTimeString($l['timestamp'])->locale('pt_BR')->diffForHumans();
                $l['forHumans'] = Carbon::createFromTimeString($l['timestamp'])->diffForHumans(); 

                $return[]= $l;
            };
        }

    }

    if($_REQUEST['action']=="lasts_transactions") {

        $tokens = array();
        $sql2 = "SELECT * FROM tokens";
        $query2 = $mysqli->query($sql2);
        if (!$query2) die($mysqli->error);
        if($query2->num_rows>0) {
            while($row  = $query2->fetch_assoc()){
                $tokens[$row['address']]['name'] = $row['name'];
                $tokens[$row['address']]['symbol'] = $row['symbol'];
                $tokens[$row['address']]['to'] =   $row['address'];
            }
        }

        $sql        = "SELECT * FROM transactions ORDER BY timestamp DESC LIMIT ".$_REQUEST['size'];
        $query      = $mysqli->query($sql);
        if (!$query) die("Error: ".$mysqli->error);
        if($query->num_rows>0) {
            while($l  = $query->fetch_assoc()){

                if(strlen($l['value'])>10) $l['value'] = floatval( $convert->from( $l['value'] ) );

                $sql2 = "SELECT * FROM tokens_operations WHERE hash='".$l['hash']."'";
                $query2 = $mysqli->query($sql2);
                if (!$query2) die($mysqli->error);
                if($query2->num_rows>0) {
                    $row  = $query2->fetch_assoc();
                    $l['transaction_token']  = $row;
                    if(isset($tokens[$row['address']])) $l['token'] = $tokens[$row['address']];
                }

                //$l['forHumans'] = Carbon::createFromTimeString($l['timestamp'])->locale('pt_BR')->diffForHumans(); 
                $l['forHumans'] = Carbon::createFromTimeString($l['timestamp'])->diffForHumans(); 

                $return[]= $l;
            };
        }

    }

    if($_REQUEST['action']=="transactions") {

        $references = array();
        $sql = "SELECT * FROM transactions WHERE `from`='".$_REQUEST['wallet']."' OR `to`='".$_REQUEST['wallet']."' ORDER BY timestamp DESC";
        $query = $mysqli->query($sql);
        if (!$query) die($mysqli->error);
        if($query->num_rows>0) {
            while($row  = $query->fetch_assoc()){
                $return[] = $row;
            }
        }

        $sql = "SELECT * FROM tokens_operations WHERE `_to`='".$_REQUEST['wallet']."' ORDER BY blockTimeStamp DESC";
        $query = $mysqli->query($sql);
        if (!$query) die($mysqli->error);
        if($query->num_rows>0) {
            while($row  = $query->fetch_array()){

                $sql2 = "SELECT * FROM transactions WHERE hash='".$row['hash']."'";
                $query2 = $mysqli->query($sql2);
                if (!$query2) die($mysqli->error);
                if($query2->num_rows>0) {
                    $is_contract = true;
                    $l = $query2->fetch_assoc();
                    $l['transaction_token']  = $row;
                    if(isset($tokens[$row['address']])) $l['token'] = $tokens[$row['address']];
                    $l['to'] = $row['_to'];
                    $l['value'] = $row['_value'];

                    $return[] = $l;
                }

                
            }
        }

    }

    if($_REQUEST['action']=="resume") {
        

        $sql        = "SELECT * FROM blocks ORDER BY number DESC limit 1";
        $query      = $mysqli->query($sql);
        $row        = $query->fetch_array();
        $return['blocks'] = $row['number'];

        $sql        = "SELECT * FROM transactions";
        $query      = $mysqli->query($sql);

        $return['transactions'] = $query->num_rows;

        
        $sql        = "SELECT * FROM first_tx";
        $query      = $mysqli->query($sql);
        $return['wallets'] = $query->num_rows;

        $sql        = "SELECT * FROM tokens";
        $query      = $mysqli->query($sql);
        $return['tokens'] = $query->num_rows;
    }

    echo json_encode(["data" => $return]);

?>