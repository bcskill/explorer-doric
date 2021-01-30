<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Doric Block Explorer">
    <meta name="author" content="Doric">
    <meta name="generator" content="CG">
    <title><?php 
      if(isset($titlePage)) echo $titlePage." - ";
      echo $_ENV['SITE_NAME']; 
    ?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo $baseurl; ?>assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $baseurl; ?>assets/style.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="<?php echo $baseurl; ?>assets/dist/js/bootstrap.min.js"></script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-J0S0EVV62W"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-J0S0EVV62W');
    </script>
  </head>
  <body>

<header>
  <div class="navbar navbar-light bg-white border-bottom pt-3 pb-3">
    <div class="container">
      <p class="h5 my-0 me-md-auto fw-normal"><a href="<?php echo $baseurl; ?>" class="brand"><img class="mb-2" src="<?php echo $baseurl; ?>assets/brand/doric-logo-black.png" alt="" width="50"  > <strong>BLOCK EXPLORER</strong></a></p>
      <nav class="my-2 my-md-0 me-md-3">
        <a class="p-2 text-dark" href="<?php echo $baseurl; ?>">Home</a>
        <a class="p-2 text-dark" href="<?php echo $baseurl; ?>blocks">Blocks</a>
        <a class="p-2 text-dark" href="<?php echo $baseurl; ?>transactions">Transactions</a>
        <a class="p-2 text-dark" href="<?php echo $baseurl; ?>">Wallets</a>
        <a class="p-2 text-dark" href="<?php echo $baseurl; ?>">Blockchain</a>
        <a class="p-2 text-dark" href="<?php echo $baseurl; ?>">API</a>
      </nav>
    </div>
  </div>
</header>

<main class="container">