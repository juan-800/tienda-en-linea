<?php
define("CLIENT_ID","AWPqbAPD1M65KmDUpW0xOi4Vsa78fjjWB9Nw1iyg4NKfe_cRIgaBYCcKbO5FAfVtlod5-uJ56nz2_oTR");
define("CURRENCY","USD");
define("kEY_TOKEN","APR.wqc-354*");
define("MONEDA", "$");


session_start();
$num_cart = 0;
if(isset($_SESSION['carrito'] ['productos'])){
    $num_cart = count($_SESSION['carrito'] ['productos']);

}


?>