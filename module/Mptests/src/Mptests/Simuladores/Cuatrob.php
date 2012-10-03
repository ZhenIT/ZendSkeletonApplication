<?php

namespace Mptests\Simuladores;

use Zend\Http\Client;
use Zend\Http\Response;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Session\Container;
use Mptests\Simuladores\Simulador;

class Cuatrob extends Simulador {

    const RETURN_METHOD = 'GET';
    var $sistema = 'Pasat 4B';

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
            $idiomas = array("french" => "fr",
                "german" => "de",
                "english" => "en",
                "catalan" => "ca",
                "espanol" => "es"
            );
            return $idiomas[$val];
        }
    }

    public function getAmount(HttpRequest $request) {
        $client = new Client($this->getUrlCompra($request), array(
                    'timeout' => 30
                ));
        $res = $client->send();
        $vals = array();
        if ($res->isOk()) {
            preg_match('/M([0-9]{3})([0-9]*)/', $res->getBody(), $vals);
        }

        $this->transaction->response1 .= $res->getStatusCode() . "\n" . $res->getBody();
        $this->transactionTable->saveTransaction($this->transaction);

        return number_format($vals[2]/100,2) . " " . $this->toIso($vals[1],self::ISO_TYPE_CURR);
    }

    public function getLang(HttpRequest $request) {
        return $this->toIso($request->getPost('Idioma', null),self::ISO_TYPE_LANG);
    }

    public function notify($result) {
        $client = new Client($this->getUrlResultado($result), array(
                    'timeout' => 30
                ));
        $res = $client->send();

        $this->transaction->response2 .= $res->getStatusCode() . "\n" . $res->getBody();
    }

    public function getUrlOk() {
        return $this->getUrlRecibo();
    }

    public function getUrlFail() {
        return $this->getUrlRecibo();
    }

    public function safeReferencia(HttpRequest $request) {
        $sessionContainer = new Container('cuatrob');
        $sessionContainer->store = $request->getPost('id_comercio');
        
        $this->transaction->referencia = $request->getPost('order');;
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
                'result' => 2,
                'coderror' => 666,
                'deserror' => 'Simulación de error'
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

    private function getUrlCompra(HttpRequest $request) {
        switch ($this->transaction->modulo) {
            case 'MAGE':
                $referer = str_replace('/index.php', '', $this->transaction->dominio);
                $referer = str_replace('/pasat4b/standard/redirect', '', $referer);
                return $referer . '/index.php/pasat4b/standard/compra?store=' . $request->getPost('id_comercio') . '&order=' . $request->getPost('order');
            case 'OC':
                $referer = str_replace('/index.php', '', $this->transaction->dominio);
                return $referer . '/index.php?route=payment/cuatrob/order&store=' . $request->getPost('id_comercio') . '&order=' . $request->getPost('order');
           case 'PRE':
                $referer = str_replace('/index.php', '', $this->transaction->dominio);
                return $referer . '/index.php?fc=module&module=qb&controller=compra&store=' . $request->getPost('id_comercio') . '&order=' . $request->getPost('order');                                
            default:
                break;
        }
    }

    private function getUrlResultado($result) {
        switch ($this->transaction->modulo) {
            case 'MAGE':
                $referer = str_replace('/index.php', '', $this->transaction->dominio);
                $referer = str_replace('/pasat4b/standard/redirect', '', $referer);
                return $referer . '/index.php/pasat4b/standard/resultado?' . http_build_query($this->getReturnData($result));
            case 'OC':
                $referer = substr($this->transaction->dominio, strpos($this->transaction->dominio,'/index.php'));
                return $referer . '/index.php?route=payment/cuatrob/callback&' . http_build_query($this->getReturnData($result));
            case 'PRE':
                $referer = substr($this->transaction->dominio, strpos($this->transaction->dominio,'/index.php'));
                return $referer . '/index.php?fc=module&module=qb&controller=result&' . http_build_query($this->getReturnData($result));                
            default:
                break;
        }
    }

    private function getUrlRecibo() {
        switch ($this->transaction->modulo) {
            case 'MAGE':
                $referer = str_replace('/index.php', '', $this->transaction->dominio);
                $referer = str_replace('/pasat4b/standard/redirect', '', $referer);
                return $referer . '/index.php/pasat4b/standard/recibo';
            case 'OC':
                $referer = substr($this->transaction->dominio, strpos($this->transaction->dominio,'/index.php'));
                return $referer . '/index.php?route=checkout/success';
            case 'PRE':
                $referer = substr($this->transaction->dominio, strpos($this->transaction->dominio,'/index.php'));
                return $referer . '/index.php?fc=module&module=qb&controller=recibo';                 
            default:
                break;
        }
    }

}