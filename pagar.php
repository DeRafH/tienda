<?php
include 'global/config.php';
include 'global/conexion.php';
include 'carrito.php';
include 'templates/cabecera.php';
?>

<?php
if($_POST){
    $total=0;
    $SID=session_id();
    $Correo=$_POST['email'];

    foreach($_SESSION['CARRITO'] as $indice=>$producto){

        $total=$total+($producto['PRECIO']*$producto['CANTIDAD']);

    }
        $sentencia=$pdo->prepare("INSERT INTO `tblventas` 
                    (`ID`, `ClaveTransaccion`, `PaypalDatos`, `Fecha`, `Correo`, `Total`, `status`) 
        VALUES (NULL,:ClaveTranscacion, '', NOW(),:Correo,:Total, 'pendiente');");
        
        $sentencia->bindParam(":ClaveTranscacion",$SID);
        $sentencia->bindParam(":Correo",$Correo);
        $sentencia->bindParam(":Total",$total);
        $sentencia->execute();
        $idVenta=$pdo->lastInsertId();

        foreach($_SESSION['CARRITO'] as $indice=>$producto){

            $sentencia=$pdo->prepare("INSERT INTO `tbldetalleventa` (`ID`, `IDVENTA`, `IDPRODUCTO`, `PRECIOUNITARIO`, `CANTIDAD`, `DESCARGADO`)
            VALUES (NULL,:IDVENTA,:IDPRODUCTO,:PRECIOUNITARIO,:CANTIDAD, '0');");
        
            $sentencia->bindParam(":IDVENTA",$idVenta);
            $sentencia->bindParam(":IDPRODUCTO",$producto['ID']);
            $sentencia->bindParam(":PRECIOUNITARIO",$producto['PRECIO']);
            $sentencia->bindParam(":CANTIDAD",$producto['CANTIDAD']);
            $sentencia->execute();
        }
    //echo "<h3>".$total."</h3>";
}
?>
<script src="https://www.paypal.com/sdk/js?client-id=sb&currency=USD"></script>

<div class="jumbotron text-center">
    <h1 class="display-4">¡ Paso Fianl !</h1>
    <hr class="my-4">
    <p class="lead"> Estas a punto de pagar con paypal la cantidad de:
        <h4>$<?php echo number_format($total,2); ?></h4>
        <div id="paypal-button-container"></div>
    </p>
        <p>Los productos podrán ser descargados despues de que se termine el pago<br/>
        <strong>(Para reclamos dirigase al correo :miguel.chung@tecasup.edu.pe)</strong>
    </p>
</div>

<!DOCTYPE html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
</head>

<body>
    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '0.01'
                        }
                    }]
                });
            },

            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    alert('Transaction completed by ' + details.payer.name.given_name + '!');
                });
            }


        }).render('#paypal-button-container');
    </script>
</body>
    
<?php include 'templates/pie.php'; ?>