
<?php
$claseImagen = $claseImagen ?? 'imagen';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luminous Spa</title>

    <?php if (isset($landing) && $landing): ?>
        <!-- No incluir nada aquí, el landing tiene su propio head completo -->
    <?php else: ?>
        <!-- Font Awesome solo para páginas que no son landing -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-SFQX1P3omQjR6b/sGmHv9uCnQB8iLZlJbPh1cSgmfKWnDcVjIKruSBGDB7mQk/9ZlGUGbADIBX/ufFRJVAZqmQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;700;900&display=swap" rel="stylesheet">

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Estilos generales (solo para páginas que no son landing) -->
        <link rel="stylesheet" href="/build/css/app.css">
    <?php endif; ?>
</head>

<body>
<?php if (isset($landing) && $landing): ?>
    <!-- Para el landing, solo mostrar el contenido directamente sin la estructura de contenedor-app -->
    <?php echo $contenido; ?>
<?php else: ?>
    <div class="contenedor-app">
        <div class="<?php echo $claseImagen; ?>"></div>
        <div class="app">
            <?php echo $contenido; ?>
        </div>
    </div>
<?php endif; ?>

<?php echo $script ?? ''; ?>
</body>
</html>