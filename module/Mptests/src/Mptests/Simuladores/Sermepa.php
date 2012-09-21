<?php

namespace Mptests\Simuladores;

use Mptests\Simuladores\Simulador;

class Sermepa extends Simulador {
    var $sistema = 'Sermepa';
    
    public function getAmount($request, $modulo) {
        return $request->getRequest('Ds_Merchant_Amount', null);
    }

    public function getUrlFail() {
        
    }

    public function getUrlOk() {
        
    }

    public function notify($result) {
        
    }

    public function getReturnData($result) {
        $sessionContainer = new Container('cuatrob');
        if ($result)
            $arr_result = array(
                'result' => 0,
                'pszApprovalCode' => 'XXXXXXXXXXXXXXXXXX',
                'pszTxnID' => '0000000000000000',
            );
        else
            $arr_result = array(
                'result' => 0,
                'coderror' => 666,
                'deserror' => 'Simulaciópn de error'
            );
        return array_merge(
                        array(
                    'pszPurchorderNum' => $this->transaction->referencia,
                    'pszTxnDate' => date('d/m/y'),
                    'tipotrans' => 'CES',
                    'store' => $sessionContainer->store,
                        ), $arr_result
        );
    }

}