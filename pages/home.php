<?php include 'header.php'; ?>

<div class="row mt-5 mb-5">
    <div class="col-md-12">
        <h2 class="title-page"><strong>The Doric Block Explorer</strong></h2>
        <form action="" method="post">
            <div class="row">
                <div class="col-md-8">
                    <input type="text" name="term" class="form-control form-control-lg" placeholder="Search by Address / Tx Hash / Block / Token" />
                </div>
                <div class="col-md-2"><button class="btn btn-lg btn-primary">Search</button></div>
            </div>
        </form>
    </div>
</div>

<div class="row mb-3 ">
    <div class="col-md-3 col-xs-6">
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="my-0 fw-normal">Blocks</h4>
            </div>
            <div class="card-body">
                <h2 id="numBlocksHome"><div class="fa-1x"><i class="fa fa-spinner fa-spin"></i></div></h2>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-xs-6">
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="my-0 fw-normal">Transactions</h4>
            </div>
            <div class="card-body">
                <h2 id="numTransactionsHome"><div class="fa-1x"><i class="fa fa-spinner fa-spin"></i></div></h2>
            </div>
        </div>
    </div>

    

    <div class="col-md-3">
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="my-0 fw-normal">Wallets</h4>
            </div>
            <div class="card-body">
                <h2 id="numWalletsHome"><div class="fa-1x"><i class="fa fa-spinner fa-spin"></i></div></h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="my-0 fw-normal">Contracts</h4>
            </div>
            <div class="card-body">
                <h2 id="numContractsHome"><div class="fa-1x"><i class="fa fa-spinner fa-spin"></i></div></h2>
            </div>
        </div>
    </div>


</div>

<div class="row mb-3 mt-4">

    <div class="col-md-6">
    <div class="card mb-4 shadow-sm">
    <div class="card-header">
        <h4 class="my-0 fw-normal">Latest Blocks</h4>
    </div>
    <div class="card-body">
        <table class="table table-striped">
        
        <tbody id="lastBlocksHome"><tr><td colspan="10"><div class="fa-3x"><i class="fa fa-spinner fa-spin"></i></div></td></tr></tbody>
        </table>
    </div>
    </div>
    </div>

    <div class="col-md-6">
    <div class="card mb-4 shadow-sm">
    <div class="card-header">
        <h4 class="my-0 fw-normal">Latest Transactions</h4>
    </div>
    <div class="card-body">
        <table class="table table-striped">
        
        <tbody id="lastTransactionsHome"><tr><td colspan="10"><div class="fa-3x"><i class="fa fa-spinner fa-spin"></i></div></td></tr></tbody>
        </table>
    </div>
    </div>
    </div>
    
    </div>
</div>

    <script>
        function getLastBlocks(){
            $.post("<?php echo $baseurl; ?>api",{
                "action": "lasts_blocks",
                "size": 10
            },function(response){
                var html = "";
                for(var i in response.data){
                    html = html + "<tr>";
                        html = html + "<td><a href='block/"+response.data[i].number+"'>"+response.data[i].number+"</a> ("+response.data[i].hex_number+")<br><small>"+response.data[i].forHumans+"</small></td>";
                        
                        html = html + "<td>"+response.data[i].transactions+" txns<br><small>"+response.data[i].size+" bytes</small></td>";

                        html = html + "<td><a href='block/"+response.data[i].number+"'>"+response.data[i].hash.slice(0,25)+"...</a><br><strong>Miner:</strong> Root</td>";
                    html = html + "</tr>";
                }
                $('#lastBlocksHome').html(html);
            });
        }

        function getLastTransactions(){
            $.post("<?php echo $baseurl; ?>api",{
                "action": "lasts_transactions",
                "size": 10
            },function(response){
                var html = "";
                for(var i in response.data){
                    html = html + "<tr>";
                        html = html + "<td><a href='tx/"+response.data[i].hash+"'>"+response.data[i].hash.slice(0,25)+"...</a><br><small>"+response.data[i].forHumans+"</small></td>";
                        
                        html = html + "<td>";
                        html = html + "<strong>From: </strong><a href='address/"+response.data[i].from+"'>"+response.data[i].from.slice(0,15)+"...</a><br><strong>To:</strong> ";
                        if(typeof response.data[i].token=="undefined")
                            html = html + "<a href='address/"+response.data[i].to+"'>"+response.data[i].to.slice(0,15)+"...</a>";
                        else
                            html = html + "<a href='address/"+response.data[i].token.to+"'>"+response.data[i].token.to.slice(0,15)+"...</a>";
                        html = html + "</td>";
                            //html = html + "Contract: <a href='address/"+response.data[i].token.to+"'>"+response.data[i].token.name+"</a>";
                        html = html + "</td>";

                        if(response.data[i].value>0)
                            html = html + "<td align='right'><span class='label label-yellow arrowed'>"+response.data[i].value+" DRC</span></td>";
                        else if(typeof response.data[i].transaction_token!="undefined" && (typeof response.data[i].token!="undefined"))
                            html = html + "<td align='right'><span class='label label-pink arrowed-right' style='color:#fff;'>"+response.data[i].transaction_token._value+" "+response.data[i].token.symbol+"</span></td>";
                        else html = html + "<td align='right'></td>";
                    html = html + "</tr>";
                }
                $('#lastTransactionsHome').html(html);
            });
        }

        function getResume(){
            $.post("<?php echo $baseurl; ?>api",{
                "action": "resume"
            },function(response){
                $('#numBlocksHome').html(response.data.blocks);
                $('#numTransactionsHome').html(response.data.transactions);
                
                $('#numWalletsHome').html(response.data.wallets);
                $('#numContractsHome').html(response.data.tokens);
            });
        }

        $(document).ready(function(){

            getLastBlocks();
            var lastBlock = setInterval( function(){ getLastBlocks(); }, 5000); 

            getLastTransactions();
            var lastTransaction = setInterval( function(){ getLastTransactions(); }, 5000);

            getResume();
            var resume = setInterval( function(){ getResume(); }, 2000);   
        });
    </script>

<?php include 'footer.php'; ?>