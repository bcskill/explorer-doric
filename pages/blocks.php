<?php $titlePage = "All Blocks"; ?>
<?php include 'header.php'; ?>
<?php 
    $blocks = array();
    $sql        = "SELECT * FROM blocks ORDER BY timestamp DESC LIMIT 50";
    $query      = $mysqli->query($sql);
    if (!$query) die("Error: ".$mysqli->error);
    if($query->num_rows>0) {
        while($l  = $query->fetch_assoc()){

            $sql2        = "SELECT * FROM transactions WHERE blockNumber='".$l['number']."'";
            $query2      = $mysqli->query($sql2);
            $l['transactions'] = $query2->num_rows;

            $blocks[] = $l;
        }
    }
?>

<div class="row mt-5 mb-5">
    <div class="col-md-12">
    <h2 class="title-page"><strong>Blocks</strong></h2>

    <div class="card">
        <div class="card-header">
            <h4>All Blocks</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Block</th>
                        <th>Hash</th>
                        <th>Age</th>
                        <th>Txn</th>
                        <th>Difficulty</th>
                        <th>Gas Used</th>
                        <th>Gas Limit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($blocks AS $block) { ?>
                    <tr>
                        <td>0x<?php echo dechex($block['number']); ?> (<a href="<?php echo $baseurl; ?>block/<?php echo $block['number']; ?>"><?php echo $block['number']; ?></a>)</td>
                        <td><a href="<?php echo $baseurl; ?>block/<?php echo $block['number']; ?>"><?php echo $block['hash']; ?></a></td>
                        <td><?php echo \Carbon\Carbon::createFromTimeString($block['timestamp'])->diffForHumans(); ?></td>
                        <td><?php echo $block['transactions']; ?></td>
                        <td><?php echo $block['difficulty']; ?></td>
                        <td><?php echo $block['gas_used']; ?></td>
                        <td><?php echo $block['gas_limit']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    
    </div>
</div>


<?php include 'footer.php'; ?>