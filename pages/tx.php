<?php $titlePage = "Transaction Details"; ?>
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
    
    $sql        = "SELECT * FROM transactions WHERE hash='".$slug[1]."'";
    $query      = $mysqli->query($sql);
    if (!$query) die("Error: ".$mysqli->error);
    if($query->num_rows>0) {
        $l  = $query->fetch_assoc();

        $sql2 = "SELECT * FROM tokens_operations WHERE hash='".$l['hash']."'";
        $query2 = $mysqli->query($sql2);
        if (!$query2) die($mysqli->error);
        if($query2->num_rows>0) {
            $row  = $query2->fetch_assoc();
            $l['transaction_token']  = $row;
            if(isset($tokens[$row['address']])) $l['token'] = $tokens[$row['address']];
            if(isset($row['logs'][0]['address'])) $l['token'] = $tokens[$row['logs'][0]['address']];
        }

        $transaction = $l;
        
    }

    $sql        = "SELECT * FROM blocks WHERE hash='".$transaction['blockHash']."'";
    $query      = $mysqli->query($sql);
    if (!$query) die("Error: ".$mysqli->error);
    if($query->num_rows>0) {
        $l  = $query->fetch_assoc();
        $block = $l;
    }

    $receipt = $eth->eth_getTransactionReceipt( $slug[1] );
    if( isset($receipt->logs[0]->address) && isset($tokens[$receipt->logs[0]->address]) ) {
        $transaction['token'] = $tokens[$receipt->logs[0]->address];
    }
?>

<div class="row mt-5 mb-5">
    <div class="col-md-12">
    <h2 class="title-page"><strong>Transaction Details</strong></h2>

    <div class="card">
        <div class="card-header">
            <h4>Overview</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td>Transaction Hash:</td><td><?php echo $transaction['hash']; ?></td>
                    </tr>
                    <tr>
                        <td>Status:</td><td><span class='badge bg-success'>Success</span><?php 
                        // if( hexdec( $receipt->status ) == "1" ) 
                        //     echo "<span class='badge bg-success'>Success</span>"; 
                        // else  
                        //     echo "<span class='badge bg-Light'>Pendente</span>";
                    ?></td>
                    </tr>
                    <tr>
                        <td>Block:</td><td>0x<?php echo dechex($transaction['blockNumber']); ?> (<a href="<?php echo $baseurl; ?>block/<?php echo $transaction['blockNumber']; ?>"><?php echo $transaction['blockNumber']; ?></a>)</td>
                    </tr>
                    <tr>
                        <td>Timestamp:</td><td><?php echo $transaction['timestamp']; ?></td>
                    </tr>
                    <tr>
                        <td>From:</td><td><a href="<?php echo $baseurl; ?>address/<?php echo $transaction['from']; ?>"><?php echo $transaction['from']; ?></a></td>
                    </tr>
                    <?php if(isset($transaction['input']) && $transaction['input']!="0x") { ?>
                    <tr>
                        <td>Interacted With (To):</td>
                        <td>
                            Contract: 
                            <?php if(isset($receipt->contractAddress) && $receipt->contractAddress!="") { ?>
                            <a href="<?php echo $baseurl; ?>address/<?php echo $receipt->contractAddress; ?>"><?php echo $receipt->contractAddress; ?></a>
                            <?php } else if(isset($receipt->logs[0]->address)) { ?>
                            <a href="<?php echo $baseurl; ?>address/<?php echo $receipt->logs[0]->address; ?>"><?php echo $receipt->logs[0]->address; ?></a>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Tokens Transferred:</td>
                        <td>
                            <strong>From:</strong> <a href="<?php echo $baseurl; ?>address/<?php echo $receipt->from; ?>"><?php echo substr($receipt->from,0,20)."..."; ?></a>
                            <?php if(isset($transaction['token']['to']) && !isset($transaction['transaction_token']['_to'])) { ?>
                            <strong>To:</strong> <a href="<?php echo $baseurl; ?>address/<?php echo $transaction['token']['to']; ?>"><?php echo substr($transaction['token']['to'],0,20)."..."; ?> (<?php echo $transaction['token']['name']; ?> | <?php echo $transaction['token']['symbol']; ?>)</a>
                            <?php } ?>
                            <?php if(isset($transaction['transaction_token']['_to'])) { ?>
                            <strong>To:</strong> <a href="<?php echo $baseurl; ?>address/<?php echo $transaction['transaction_token']['_to']; ?>"><?php echo substr($transaction['transaction_token']['_to'],0,20)."..."; ?> (<?php echo $transaction['token']['name']; ?> | <?php echo $transaction['token']['symbol']; ?>)</a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } else { ?>
                    <tr>
                        <td>To:</td><td>
                        <?php  if(isset($transaction['to'])) { ?>
                        <a href="<?php echo $baseurl; ?>address/<?php echo $transaction['to']; ?>">
                        <?php echo $transaction['to']; ?>
                        </a>
                        <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                    
                    </tr>
                    <tr>
                        <td>Value:</td><td><span class="label label-yellow arrowed"><?php 
                            if(strlen($transaction['value'])>10) 
                                echo floatval( $convert->from( $transaction['value'] ) );
                            else 
                                echo $transaction['value'];
                        ?> DRC</span> <?php 
                            if(isset($receipt->logs[0]->data)){
                                echo "<span class='label label-large label-pink arrowed-right' style='color:#fff;'>";
                                echo floatval( $convert->from( hexdec($receipt->logs[0]->data) ) ); 
                                if(isset($transaction['token']['symbol'])) echo " ".$transaction['token']['symbol'];
                                echo "</span>";
                            }
                        ?></td>
                    </tr>
                    <tr>
                        <td>Transaction Fee:</td><td><?php echo hexdec($transaction['transactionIndex']); ?></td>
                    </tr>
                    <tr>
                        <td>Gas Price:</td><td><?php echo  number_format($convert->from( hexdec($transaction['gasPrice']) ), 10, ".",""); ?> DRC</td>
                    </tr>
                    <tr>
                        <td>Gas Limit:</td><td><?php echo  $convert->from( hexdec($block['gas_limit']), "gwei" );  ?></td>
                    </tr>
                    <tr>
                        <td>Gas Used by Transaction:</td><td><?php echo  $convert->from( hexdec($block['gas_used']), "gwei" );  ?></td>
                    </tr>
                    <tr>
                        <td>Nonce:</td><td><?php echo hexdec( $transaction['nonce'] ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    </div>
</div>


<?php include 'footer.php'; ?>