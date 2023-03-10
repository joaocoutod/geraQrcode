<?php
namespace App\Pix;

class Playload{

    /**
    * IDs do Payload do Pix
    * @var string
    */
    const ID_PAYLOAD_FORMAT_INDICATOR = '00';
    const ID_MERCHANT_ACCOUNT_INFORMATION = '26';
    const ID_MERCHANT_ACCOUNT_INFORMATION_GUI = '00';
    const ID_MERCHANT_ACCOUNT_INFORMATION_KEY = '01';
    const ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION = '02';
    const ID_MERCHANT_CATEGORY_CODE = '52';
    const ID_TRANSACTION_CURRENCY = '53';
    const ID_TRANSACTION_AMOUNT = '54';
    const ID_COUNTRY_CODE = '58';
    const ID_MERCHANT_NAME = '59';
    const ID_MERCHANT_CITY = '60';
    const ID_ADDITIONAL_DATA_FIELD_TEMPLATE = '62';
    const ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID = '05';
    const ID_CRC16 = '63';


    /**
     * Chave pix
     * @var string
     */
    private $pixKey;

    /**
     * descricao do pagamento
     * @var string
     */
    private $description;

    /**
     * nome do titular
     * @var string
     */
    private $merchantName;

    /**
     * cidade do titular
     * @var string
     */
    private $merchantCity;

    /**
     * id transacao pix
     * @var string
     */
    private $txid;

    /**
     * Valor da transacao
     * @var string
     */
    private $amount;


    /**
     * metodo responsavel por definir o valor de $pixKey
     * @param string  $pixKey
     */
    public function setPixKey($pixKey){
        $this->pixKey = $pixKey;
        return $this;
    }

    /**
     * metodo responsavel por definir o valor de $description
     * @param string  $description
     */
    public function setDescription($description){
        $this->description = $description;
        return $this;
    }

    /**
     * metodo responsavel por definir o valor de $merchantName
     * @param string  $merchantName
     */
    public function setMerchantName($merchantName){
        $this->merchantName = $merchantName;
        return $this;
    }

    /**
     * metodo responsavel por definir o valor de $merchantCity
     *  @param string  $txid
     */
    public function setMerchantCity($merchantCity){
        $this->merchantCity = $merchantCity;
        return $this;
    }

    /**
     * metodo responsavel por definir o valor de $txid
     * @param string  $txid
     */
    public function setTxid($txid){
        $this->txid = $txid;
        return $this;
    }

    /**
     * metodo responsavel por definir o valor de
     * @param float  $amount
     */
    public function setAmount($amount){
        $this->amount = (string)number_format($amount, 2, '.', '');
        return $this;
    }


    /**
     * responsavel por retornar o valor compleot do objeto do playload
     * @param string $id
     * @param string $value
     * @return string
     */
    public function getValue($id, $value){
        $size = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);
        return $id.$size.$value;
    }

    /**
     * metodo responsavel por retornar informcao da conta
     * @param string  
     */
    private function getMerchantAccountInformation(){
        //DOMINIO BANCO
        $gui = $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_GUI, 'br.gov.bcb.pix');
        //CHAVE PIX
        $key = $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_KEY, $this->pixKey);

        //DESCRICAO DO PAGAMENTO
        $description = strlen($this->description) ? $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION, $this->description) : '';
        
        //VALOR COMPLETO DA CONTA
        return $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION, $gui.$key.$description);
    }

    /**
     * Metodo responsavel por retronar os valores completos do campo adicional (TXID)
     * @param string 
     */
    public function getAdditionaDataFieldTemplete(){
        $txid = $this->getValue(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID, $this->txid);
        
        return $this->getValue(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE, $txid);
    }



    /**
     * Metodo responsavel por gerar o codiog completo do playload pix
     * @param string
     */
    public function getPlayload(){
        //cria playload
        $playload = $this->getValue(self::ID_PAYLOAD_FORMAT_INDICATOR, '01').
                    $this->getMerchantAccountInformation().
                    $this->getValue(self::ID_MERCHANT_CATEGORY_CODE, '0000').
                    $this->getValue(self::ID_TRANSACTION_CURRENCY, '986').
                    $this->getValue(self::ID_TRANSACTION_AMOUNT, $this->amount).
                    $this->getValue(self::ID_COUNTRY_CODE, 'BR').
                    $this->getValue(self::ID_MERCHANT_NAME, $this->merchantName).
                    $this->getValue(self::ID_MERCHANT_CITY, $this->merchantCity).
                    $this->getAdditionaDataFieldTemplete();
        
        ///RETORNAR O PLAYLOAD
        return $playload.$this->getCRC16($playload);
    }



    /**
   * M??todo respons??vel por calcular o valor da hash de valida????o do c??digo pix
   * @return string
   */
    private function getCRC16($payload) {
        //ADICIONA DADOS GERAIS NO PAYLOAD
        $payload .= self::ID_CRC16.'04';

        //DADOS DEFINIDOS PELO BACEN
        $polinomio = 0x1021;
        $resultado = 0xFFFF;

        //CHECKSUM
        if (($length = strlen($payload)) > 0) {
            for ($offset = 0; $offset < $length; $offset++) {
                $resultado ^= (ord($payload[$offset]) << 8);
                for ($bitwise = 0; $bitwise < 8; $bitwise++) {
                    if (($resultado <<= 1) & 0x10000) $resultado ^= $polinomio;
                    $resultado &= 0xFFFF;
                }
            }
        }

        //RETORNA C??DIGO CRC16 DE 4 CARACTERES
        return self::ID_CRC16.'04'.strtoupper(dechex($resultado));
    }
}   



?>