# geraQrcode

Primeiro instale as dependências
```
composer install
```

<br>

Instancias principais para criar o playload do pix
```php
$obPlayload = (new Playload)
                ->setPixKey('03195925290')
                ->setDescription('Pagamento do pedido 12345')
                ->setMerchantName('Joao')
                ->setMerchantcITY('Manaus')
                ->setAmount(100.00)
                ->setTxid('WDEV1234');
´´´
