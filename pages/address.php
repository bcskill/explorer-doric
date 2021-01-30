<?php $titlePage = "Address Details"; ?>
<?php include 'header.php'; ?>
<?php 

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

    // buscar todas as referencias da wallet no banco de dados
    $references = array();
    $sql = "SELECT * FROM transactions WHERE `from`='".$slug[1]."' OR `to`='".$slug[1]."' ORDER BY timestamp DESC";
    $query = $mysqli->query($sql);
    if (!$query) die($mysqli->error);
    if($query->num_rows>0) {
        while($row  = $query->fetch_array()){

            $sql2 = "SELECT * FROM tokens_operations WHERE hash='".$row['hash']."'";
            $query2 = $mysqli->query($sql2);
            if (!$query2) die($mysqli->error);
            if($query2->num_rows>0) {
                $is_contract = true;
                $row['transaction_token']  = $query2->fetch_assoc();
                if(isset($tokens[$row['transaction_token']['address']])) $row['token'] = $tokens[$row['transaction_token']['address']];
            }

            $references[$row['hash']] = $row;
        }
    }

    $sql = "SELECT * FROM tokens_operations WHERE `_to`='".$slug[1]."' ORDER BY blockTimeStamp DESC";
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

                $references[$row['hash']] = $l;
            }

            
        }
    }

    

    $transactions = count($references);
    $balance = $eth->eth_getBalance( $slug[1] );

    $is_contract = false;

    $sql2 = "SELECT * FROM tokens WHERE address='".$slug[1]."'";
    $query2 = $mysqli->query($sql2);
    if (!$query2) die($mysqli->error);
    if($query2->num_rows>0) {
        $is_contract = true;
        $token  = $query2->fetch_assoc();
    }
?>

<div class="row mt-5 mb-5">
    <div class="col-md-12">
    <h2 class="title-page"><strong><?php if($is_contract) echo "Contract"; else echo "Address"; ?>:</strong> <?php echo $slug[1]; ?></h2>

    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Overview</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td>Balance:</td><td><strong><?php echo number_format( $convert->from(hexdec($balance)), 10,".",""); ?> DRC</strong></td>
                            </tr>
                            <tr>
                                <td>Transactions:</td><td><?php echo count( $references ); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if($is_contract) { ?>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>About</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td>Name:</td><td><strong><?php echo $token['name']; ?></strong></td>
                            </tr>
                            <tr>
                                <td>Symbol:</td><td><strong><?php echo $token['symbol']; ?></strong></td>
                            </tr>
                            <tr>
                                <td>Max Total Supply:</td><td><strong><?php echo $convert->from( $token['maxSupply'] ); ?></strong></td>
                            </tr>
                            <tr>
                                <td>Decimals:</td><td><strong><?php echo $token['decimals']; ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>

    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Transactions</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tnx Hash</th>
                                <th>Block</th>
                                <th>From</th>
                                <th></th>
                                <th>To</th>
                                <th>Value</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($references AS $reference){ ?>
                            <tr>
                                <td><a href="<?php echo $baseurl; ?>tx/<?php echo $reference['hash']; ?>"><?php echo substr($reference['hash'], 0, 20); ?>...</a></td>
                                
                                <td>0x<?php echo dechex($reference['blockNumber']); ?> (<a href="<?php echo $baseurl; ?>block/<?php echo $reference['blockNumber']; ?>"><?php echo $reference['blockNumber']; ?></a>)</td>
                                
                                <td>
                                    <?php if(strtolower($reference['from'])!=strtolower($slug[1])) { ?>
                                    <a href="<?php echo $baseurl; ?>address/<?php echo $reference['from']; ?>"><?php echo substr($reference['from'], 0, 25); ?>...</a></td>
                                    <?php } else { ?>
                                    <?php echo substr($reference['from'], 0, 25); ?>...
                                    <?php } ?>
                                    
                                </td>
                                <td class="text-center"><?php 
                                    if(strtolower($reference['from'])==strtolower($slug[1])) echo "<span class='badge bg-danger'>OUT</span>"; 
                                    if(strtolower($reference['to'])==strtolower($slug[1])) echo "<span class='badge bg-success'>IN</span>"; 
                                ?></td>
                                <td>
                                    <?php if(strtolower($reference['to'])!=strtolower($slug[1])) { ?>
                                    <a href="<?php echo $baseurl; ?>address/<?php echo $reference['to']; ?>"><?php echo substr($reference['to'], 0, 25); ?>...</a></td>
                                    <?php } else { ?>
                                    <?php echo substr($reference['to'], 0, 25); ?>...
                                    <?php } ?>

                                    <?php if(isset($reference['token']['to']) && !isset($reference['transaction_token']['_to'])) { ?>
                                    <strong>To:</strong> <a href="<?php echo $baseurl; ?>address/<?php echo $reference['token']['to']; ?>"><?php echo substr($reference['token']['to'],0,20)."..."; ?> (<?php echo $reference['token']['name']; ?> | <?php echo $reference['token']['symbol']; ?>)</a>
                                    <?php } ?>

                                    
                                </td>
                                <td><?php 
                                    if(strlen($reference['value'])>10) 
                                        echo '<span class="label label-yellow arrowed">'.floatval( $convert->from( $reference['value'] ) ).' DRC</span>';
                                    else 
                                        if($reference['value']>0) echo '<span class="label label-yellow arrowed">'.$reference['value'].' DRC</span>';
                                ?></span> <?php 
                                    if(isset($reference['transaction_token']) && isset($reference['transaction_token']['_value'])) {
                                        echo "<span class='label label-pink arrowed-right' style='color:#fff;'><small>";
                                        echo $reference['transaction_token']['_value']." ".($reference['token']['symbol']??'');
                                        echo "</small></span>";
                                    }
                                ?></td>
                                <td><?php echo $reference['timestamp']; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    
    </div>
</div>


<?php include 'footer.php'; ?>