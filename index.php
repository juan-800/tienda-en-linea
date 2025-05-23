<?php
require 'config/config.php';
require 'config/database.php';

$db = new Database();
$con = $db->conectar();
$sql = $con->prepare("SELECT id, nombre, precio FROM productos WHERE activo=1");
$sql->execute();
$resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

/*session_destroy()*/;


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda en línea</title>
    <!-- Librería Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- css-->
    <link href="css/estilos.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>
<body>
    <!-- Navbar --> 
    <header>
        <div class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a href="index.php" class="navbar-brand"><strong>Tienda Naturiss</strong></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#navbarHeader" aria-controls="navbarHeader" 
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarHeader">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a href="#" class="nav-link active">Catálogo</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Contacto</a>
                        </li>
                    </ul>
                    <a href="checkout.php" class="btn btn-primary">
                    <i class="fa fa-shopping-cart"></i> <!-- Ícono de carrito -->
                    <span id="num_cart" class="badge bg-secondary"><?php echo $num_cart;?></span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido principal -->
    <main>
    <div class="container">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-2">
            <?php
            foreach ($resultado as $row) { ?> 
            
            <div class="col">
                <div class="card shadow-sm h-100 p-2">
                    <?php
                    $id= $row['id'];
                    $imagen = "./images/producto/" . $id . "/principal.jpg";

                    if(!file_exists($imagen)){
                    $imagen = "images/no-photo.jpg";
                    }
                    ?>
                    <img src="<?php echo $imagen?>" alt="">

                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['nombre'];?></h5>
                        <p class="card-text">$ <?php echo number_format($row['precio'],2,'.',',');?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <a href="details.php?id=<?php echo $row['id']; ?>&token=<?php echo
                                hash_hmac('sha1',$row['id'], kEY_TOKEN); ?> " class="btn btn-sm btn-primary">Detalles</a> 
                            </div>

                            <button class="btn btn-sm btn-outline-success" type="button" onclicK="addProducto
                            (<?php echo $row['id'] ?>, '<?php echo hash_hmac('sha1', $row['id'], 
                            kEY_TOKEN); ?>')">Agregar</button> 
                        </div>     
                    </div>
                </div>
            </div> 
            
            <?php } ?> 
        </div>
    </div>
</main>


<!-- Bootstrap  bundle-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function addProducto(id,token){
        let url = 'clases/carrito.php';
        let formData = new FormData();
        formData.append('id', id);
        formData.append('token', token);
        fetch(url,{
            method:  'POST',
            body: formData,
            mode: 'cors'
        }).then(response => response.json()) 
        .then(data => {
            if(data.ok){
                let elemento = document.getElementById("num_cart")
                elemento.innerHTML = data.numero
            }
        }) 
        .catch(error => console.error('Error:', error)); // Manejo de errores
    }
</script>
</body>
</html>
