<?php 
if(!isset($modificacion_ruta)){

  $modificacion_ruta = "";
}
include "auth_check.php"
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title ?? 'ITSADATA'; ?></title>
       <link href= <?php echo  $modificacion_ruta."../vendor/bootstrap/css/bootstrap.min.css" ?>  rel="stylesheet">
    <link href=<?php echo  $modificacion_ruta."../public/css/sidebar.css"?> rel="stylesheet">
    <link href=<?php echo  $modificacion_ruta."../vendor/twbs/bootstrap-icons/font/bootstrap-icons.min.css"?> rel="stylesheet">
    <link rel="stylesheet" href=<?php echo  $modificacion_ruta."../node_modules/sweetalert2/dist/sweetalert2.min.css"?>>
    
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css"/>

  </head>
<body>

<div class="d-flex">
    <?php include 'sidebar.php'; ?>
    <main id="app-content" class="flex-grow-1 p-3 collapsed">
      <?php include "navbar.php"?>
      
      