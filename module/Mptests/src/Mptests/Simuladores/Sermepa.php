<?php
namespace Mptests\Simuladores;

use Mptests\Simuladores\Simulador;

class Sermepa implements Simulador
{
    public function getAmount($request,$modulo) {
        return $request->getRequest('Ds_Merchant_Amount', null);
    }

    public function getUrlFail() {
        
    }

    public function getUrlOk() {
        
    }

    public function notify($result) {
        
    }
}