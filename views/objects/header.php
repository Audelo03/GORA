<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title ?? 'ITSADATA'; ?></title>
      <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../public/css/sidebar.css" rel="stylesheet">
    <link href="../vendor/twbs/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../node_modules/sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="../vendor/datatables/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"/>
<link rel="stylesheet" href="../public/css/sidebar.css">
  </head>
<body>

<div class="d-flex">
    <?php include 'sidebar.php'; ?>
    <main id="app-content" class="flex-grow-1 p-3 collapsed">
      