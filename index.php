<?php

require __DIR__."/vendor/autoload.php";

use \App\Pix\Playload;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

//INSTANCIA PRINCIPAL DO PLAYLOAD PIX
$obPlayload = (new Playload)
                ->setPixKey('03195925290')//CHAVE PIX 
                ->setDescription('Pagamento do pedido 12345')
                ->setMerchantName('Joao')
                ->setMerchantcITY('Manaus')
                ->setAmount(100.00)
                ->setTxid('WDEV1234');

//CODIGO DE PAGAMENTO
$playloadQrcode = $obPlayload->getPlayload();

//QRCODE
$obQrCode = new QrCode($playloadQrcode);

//IMG DO QRCODE
$img = (new Output\Png)->output($obQrCode, 400);

//exibi somente a img
//header('Content-Type: image/png');
//echo $img;

?>


<h1>QRCODE PIX</h1>


<br>


<img src="data:image/png;base64, <?=base64_encode($img)?>" alt="">


<br><br>

CODIGO PIX: <br>
<strong><?=$playloadQrcode?></strong>