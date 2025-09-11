<?php 
if(!isset($modificacion_ruta)){
  $modificacion_ruta = "";
}
// auth_check.php should be included only in pages that require authentication
// include "auth_check.php"
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title ?? 'GORA'; ?></title>
    <link href="/GORA/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/GORA/public/css/sidebar.css" rel="stylesheet">
    <link href="/GORA/vendor/twbs/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/GORA/node_modules/sweetalert2/dist/sweetalert2.min.css">
    
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css"/>
    

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

  </head>
<body>

<div class="d-flex">
    <?php include 'sidebar.php'; ?>
    <main id="app-content" class="flex-grow-1 p-3 collapsed">
      <?php include "navbar.php"?>
      
      