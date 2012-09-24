<?php

namespace Mptests\Simuladores;

use Mptests\Simuladores\Simulador;

class Sermepa extends Simulador {

    var $sistema = 'Sermepa';

    public function toIso($val, $type) {
        if ($type == self::ISO_TYPE_CURR) {
            $monedas = array(
                "978" => "EUR",
                "840" => "USD",
                "826" => "GBP",
                "392" => "JPY",
                "156" => "CNY"
            );
            return $monedas[$val];
        }
        if ($type == self::ISO_TYPE_LANG) {
            $idiomas = array(
                '001' => 'es',
                '002' => 'en',
                '003' => 'ca',
                '004' => 'fr',
                '005' => 'de',
                '006' => 'nl',
                '007' => 'it',
                '008' => 'sv',
                '009' => 'pt',
                '011' => 'pl',
                '012' => 'gl',
                '013' => 'eu'
            );
            return $idiomas[$val];
        }
    }

    public function getAmount($request, $modulo) {
        return $request->getRequest('Ds_Merchant_Amount', 0) . ' ' . $this->toIso($request->getRequest('Ds_Merchant_Currency'), self::ISO_TYPE_CURR);
    }

    public function getLang(HttpRequest $request) {
        return $this->toIso($request->getPost('Ds_Merchant_ConsumerLanguage', null), self::ISO_TYPE_LANG);
    }

    public function getUrlFail() {
        
    }

    public function getUrlOk() {
        
    }

    public function notify($result) {
        
    }

    public function getReturnData($result) {
        $sessionContainer = new Container('sermepa');
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

    private function getUrlOK(HttpRequest $request) {
        switch ($this->transaction->modulo) {
            case 'MAGE':
                $referer = str_replace('/index.php', '', $this->transaction->dominio);
                $referer = str_replace('/pasat4b/standard/redirect', '', $referer);
                return $referer . '/index.php/pasat4b/standard/compra?store=' . $request->getPost('id_comercio') . '&order=' . $request->getPost('order');
            case 'PRE':
                return;
            default:
                break;
        }
    }

    private function getMerchantUrl($result) {
        switch ($this->transaction->modulo) {
            case 'MAGE':
                $referer = str_replace('/index.php', '', $this->transaction->dominio);
                $referer = str_replace('/pasat4b/standard/redirect', '', $referer);
                return $referer . '/index.php/pasat4b/standard/resultado?' . http_build_query($this->getReturnData($result));
            case 'PRE':
                return;
            default:
                break;
        }
    }

    private function getUrlKO() {
        switch ($this->transaction->modulo) {
            case 'MAGE':
                $referer = str_replace('/index.php', '', $this->transaction->dominio);
                $referer = str_replace('/pasat4b/standard/redirect', '', $referer);
                return $referer . '/index.php/pasat4b/standard/recibo';
            case 'PRE':
                return;
            default:
                break;
        }
    }

}