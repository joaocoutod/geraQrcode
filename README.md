# geraQrcode

Primeiro instale as dependências
```
composer install
```

<br>

Instancias principais para criar o playload do pix
```php
$obPlayload = (new Playload)
                ->setPixKey('00000000000')//CHAVE PIX 
                ->setDescription('Pagamento do pedido 12345')
                ->setMerchantName('Joao Lucas')
                ->setMerchantcITY('Manaus')
                ->setAmount(100.00)
                ->setTxid('WDEV1234');
´´´
