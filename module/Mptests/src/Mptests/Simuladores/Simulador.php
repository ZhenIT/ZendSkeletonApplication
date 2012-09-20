<?php

namespace Mptests\Simuladores;

use Zend\Http\PhpEnvironment\Request as HttpRequest;

abstract class Simulador {

    const ISO_TYPE_LANG = 1;
    const ISO_TYPE_CURR = 2;
    const RETURN_METHOD = 'POST';

    public $transaction;
    public $transactionTable;

    public function __construct($id, $transactionTable) {
        $this->transactionTable = $transactionTable;
        $this->transaction = $this->transactionTable->getTransaction($id);
    }

    function __destruct() {
        $this->transactionTable->saveTransaction($this->transaction);
    }

    public function getReturnMethod() {
        return $this->RETURN_METHOD;
    }

    public function getReturnData(bool $result);

    public function getAmount(HttpRequest $request);

    public function getLang(HttpRequest $request);

    public function notify($result);

    public function getUrlOk();

    public function getUrlFail();

    public function safeReferencia(HttpRequest $request);
}