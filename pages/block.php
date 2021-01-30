<?php $titlePage = "Block Details"; ?>
<?php include 'header.php'; ?>
<?php 
    //$block = $eth->eth_getBlockByNumber( "0x".dechex($slug[1]) );
        $sql        = "SELECT * FROM blocks WHERE number='".$slug[1]."'";
        $query      = $mysqli->query($sql);
        if (!$query) die("Error: ".$mysqli->error);
        if($query->num_rows>0) {
            $l  = $query->fetch_assoc();
            $block = $l;
            
        }


        // buscar todas as referencias da wallet no banco de dados
    $transactions = array();
    $sql = "SELECT * FROM transactions WHERE blockNumber='".$slug[1]."' ORDER BY timestamp DESC";
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

            $transactions[] = $row;
        }
    }
?>

<div class="row mt-5 mb-5">
    <div class="col-md-12">
    <h2 class="title-page"><strong>Block Details</strong></h2>

    <div class="card">
        <div class="card-header">
            <h4>Overview</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td>Block Height:</td><td><?php echo $block['hash']; ?></td>
                    </tr>
                    <tr>
                        <td>Timestamp:</td><td><?php echo $block['timestamp']; ?></td>
                    </tr>
                    <tr>
                        <td>Transactions:</td><td><?php echo count($transactions); ?></td>
                    </tr>
                    <tr>
                        <td>Mined by:</td><td><a href="<?php echo $baseurl; ?>address/<?php echo $block['miner']; ?>"><?php echo $block['miner']; ?></a></td>
                    </tr>
                    <tr>
                        <td>Block Reward:</td><td><?php echo $block['hash']; ?></td>
                    </tr>
                    <tr>
                        <td>Uncles Reward:</td><td><?php echo $block['hash']; ?></td>
                    </tr>
                    <tr>
                        <td>Difficulty:</td><td><?php echo hexdec( $block['difficulty'] ); ?></td>
                    </tr>
                    <tr>
                        <td>Total Difficulty:</td><td><?php echo $block['hash']; ?></td>
                    </tr>
                    <tr>
                        <td>Size:</td><td><?php echo hexdec( $block['size'] ); ?> bytes</td>
                    </tr>
                    <tr>
                        <td>Gas Used:</td><td><?php echo number_format( $convert->from( hexdec( $block['gas_used']), "gwei" ), 10,".",""); ?></td>
                    </tr>
                    <tr>
                        <td>Gas Limit:</td><td><?php echo number_format( $convert->from( hexdec( $block['gas_limit']), "gwei" ), 10,".",""); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($transactions AS $transaction){ ?>
                            <tr>
                                <td><a href="<?php echo $baseurl; ?>tx/<?php echo $transaction['hash']; ?>"><?php echo substr($transaction['hash'], 0, 20); ?>...</a></td>
                                <td><?php echo "0x".dechex($transaction['blockNumber']); ?> (<?php echo $transaction['blockNumber']; ?>)</td>
                                <td><a href="<?php echo $baseurl; ?>address/<?php echo $transaction['from']; ?>"><?php echo $transaction['from']; ?></a></td>
                                <td class="text-center"><span class='badge bg-success'> <i class="fa fa-long-arrow-right"></i> </span></td>
                                <td><a href="<?php echo $baseurl; ?>address/<?php echo $transaction['to']; ?>"><?php echo $transaction['to']; ?></a></td>
                                <td><?php 
                                    if(strlen($transaction['value'])>10) 
                                        echo '<span class="label label-yellow arrowed">'.floatval( $convert->from( $transaction['value'] ) ).' DRC</span>';
                                    else 
                                        if($transaction['value']>0) echo '<span class="label label-yellow arrowed">'.$transaction['value'].' DRC</span>';
                                ?></span> <?php 
                                    if(isset($transaction['transaction_token']) && isset($transaction['transaction_token']['_value'])) {
                                        echo "<span class='label label-pink arrowed-right' style='color:#fff;'><small>";
                                        echo $transaction['transaction_token']['_value']." ".($transaction['token']['symbol']??'');
                                        echo "</small></span>";
                                    }
                                ?></td>
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