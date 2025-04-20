<?php
require 'config/config.php';
require 'config/database.php';

$db = new Database();
$con = $db->conectar();

$id = isset($_GET['id'])? $_GET['id'] : '' ;
$token = isset($_GET['token'])? $_GET['token'] : '' ;

if($id == ''|| $token == ''){
    echo 'Error al procesar la petecion';
    exit;
}else{
    $token_tpm = hash_hmac('sha1',$id,kEY_TOKEN);

    if($token == $token_tpm){

        $sql = $con->prepare("SELECT count(id) FROM productos WHERE id=? AND activo=1");
        $sql->execute([$id]);

        if($sql->fetchColumn()>0){
            $sql = $con->prepare("SELECT nombre, descripcion, precio, descuento FROM productos WHERE id=? AND activo=1 LIMIT 1");
            $sql->execute([$id]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $nombre = $row['nombre'];
            $descripcion = $row['descripcion'];
            $precio = $row['precio'];
            $descuento = $row['descuento'];
            $precio_desc = $precio - (($precio *$descuento)/100);
            $dir_images= 'images/producto/' . $id . '/';

            $rutaImg = $dir_images. 'principal.jpg';

            if(!file_exists($rutaImg)){
                $rutaImg = 'images/no-photo.jpg';
            }

            $imagenes = array();
            if(file_exists($dir_images)){
                $dir = dir($dir_images);

                while(($archivo = $dir->read()) != false){
                    if($archivo != 'principal.jpg' && (strpos($archivo,'jpg') ||strpos($archivo,'jpeg') )){
                        $imagenes[] = $dir_images . $archivo;
                    
                    }
                }
            }

            $dir->close();

        }


    }else{
        echo 'Error al procesar la petecion';
        exit;
    }


}




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
        <div class="row">
            <div class="col-md-6 order-md-1">

            <div id="carouselImages" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="<?php echo $rutaImg; ?>" class="d-block w-100"  alt="">
            </div>

            <?php foreach($imagenes as $img){?>
            <div class="carousel-item">
            <img src="<?php echo $img; ?>" class="d-block w-100"  alt="">
                
            </div>
            <?php }?>
            
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
        </div>




            
            </div>
            <div class="col-md-6 order-md-2">
                <h2><?php  echo $nombre;?></h2>

                <?php if($descuento>0){?>
                    <p><del><?php  echo MONEDA . number_format( $precio,2, '.',',');?></del></p>
                    <h2>
                        <?php  echo MONEDA . number_format( $precio_desc,2, '.',',');?>
                        <small class="text-success"><?php echo $descuento; ?>% descuento</small>
                    </h2>

                    <?php } else {?>

                        <h2><?php  echo MONEDA . number_format( $precio,2, '.',',');?></h2>
                    <?php }?>


                <p class="lead">
                    <?php echo $descripcion;?>
                </p>
                <div class="d-grid gap-3 col-10 mx-auto">
                    <button class="btn btn-primary" type="button">Comprar ahora</button>
                    <button class="btn btn-outline-primary" type="button" onclicK="addProducto(<?php echo
                    $id ?>, '<?php echo $token_tpm ?>')">Agregar al carrito</button>
                </div>

            </div>

        </div>

    </div>
</main>

<!-- Bootstrap  bundle-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function addProducto(id,token){
        let url = 'clases/carrito.php';
        let formData = new FormData();
        formData.append('id', id)
        formData.append('token', token)
        fetch(url,{
            method: 'POST',
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