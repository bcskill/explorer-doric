<?php $titlePage = "All Transactions"; ?>
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

    $transactions = array();
    $sql        = "SELECT * FROM transactions ORDER BY timestamp DESC LIMIT 50";
    $query      = $mysqli->query($sql);
    if (!$query) die("Error: ".$mysqli->error);
    if($query->num_rows>0) {
        while($l  = $query->fetch_assoc()){

                $sql2 = "SELECT * FROM tokens_operations WHERE hash='".$l['hash']."'";
                $query2 = $mysqli->query($sql2);
                if (!$query2) die($mysqli->error);
                if($query2->num_rows>0) {
                    $row  = $query2->fetch_assoc();
                    $l['transaction_token']  = $row;
                    if(isset($tokens[$row['address']])) $l['token'] = $tokens[$row['address']];
                }

                // $receipt = $eth->eth_getTransactionReceipt( $l['hash'] );
                // if( isset($receipt->logs[0]->address) && isset($tokens[$receipt->logs[0]->address]) ) {
                //     $l['token'] = $tokens[$receipt->logs[0]->address];
                // }

            $transactions[] = $l;
        }
    }

    
?>

<div class="row mt-5 mb-5">
    <div class="col-md-12">
    <h2 class="title-page"><strong>Blocks</strong></h2>

    <div class="card">
        <div class="card-header">
            <h4>All Transactions</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Hash</th>
                        <th>Block</th>
                        <th>Age</th>
                        <th>From</th>
                        <th></th>
                        <th>To</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($transactions AS $transaction) { ?>
                    <tr>
                        
                        <td><a href="<?php echo $baseurl; ?>tx/<?php echo $transaction['hash']; ?>"><?php echo substr($transaction['hash'],0,25); ?>...</a></td>
                        <td>0x<?php echo dechex($transaction['blockNumber']); ?> (<a href="<?php echo $baseurl; ?>block/<?php echo $transaction['blockNumber']; ?>"><?php echo $transaction['blockNumber']; ?></a>)</td>

                        <td><?php echo \Carbon\Carbon::createFromTimeString($transaction['timestamp'])->diffForHumans(); ?></td>
                        <td><a href="<?php echo $baseurl; ?>address/<?php echo $transaction['from']; ?>"><?php echo substr($transaction['from'],0,25)."..."; ?></a></td>
                        <td align="center"><span class='badge bg-success'> <i class="fa fa-long-arrow-right"></i> </span></td>
                        <td><a href="<?php echo $baseurl; ?>address/<?php if(isset($transaction['token'])) echo $transaction['token']['to']; else echo $transaction['to']; ?>"><?php if(isset($transaction['token'])) echo $transaction['token']['name']." | ".$transaction['token']['symbol']; else echo substr($transaction['to'], 0, 25)."..."; ?></a></td>
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


<?php include 'footer.php'; ?>