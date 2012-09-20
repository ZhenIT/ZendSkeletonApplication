<?php
namespace Mptests\Model;

class Transaction
{
    public $id;
    public $dominio;
    public $modulo;
    public $sistema;    
    public $referencia;
    public $response1;
    public $response2;    

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->dominio = (isset($data['dominio'])) ? $data['dominio'] : null;
        $this->modulo  = (isset($data['modulo'])) ? $data['modulo'] : null;
        $this->sistema  = (isset($data['sistema'])) ? $data['sistema'] : null;
        $this->referencia  = (isset($data['referencia'])) ? $data['referencia'] : null;
        $this->response1  = (isset($data['response1'])) ? $data['response1'] : null;
        $this->response2  = (isset($data['response2'])) ? $data['response2'] : null;
    }
}
