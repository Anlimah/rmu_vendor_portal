<?php

namespace Src\Controller;

use Src\Gateway\OrchardPaymentGateway;
use Src\Controller\VoucherPurchase;

class PaymentController
{
    private $voucher;

    public function __construct()
    {
        $this->voucher = new VoucherPurchase();
    }

    public function vendorPaymentProcess($data)
    {
        $trans_id = time();
        if (!$trans_id) return array("success" => false, "message" => "Transaction ID generation failed!");
        return $this->voucher->SaveFormPurchaseData($data, $trans_id);
    }

    public function verifyTransactionStatus(int $transaction_id)
    {
        $response = json_decode($this->getTransactionStatusFromOrchard($transaction_id));
        return $response;
        /*if (empty($response)) return array("success" => false, "message" => "Invalid transaction Parameters! Code: -2");

        if (isset($response->trans_status)) {
            $status_code = substr($response->trans_status, 0, 3);
            if ($status_code == '000') return $this->voucher->genLoginsAndSend($transaction_id);
            $this->voucher->updateTransactionStatusInDB('FAILED', $transaction_id);
            return array("success" => false, "message" => "Payment failed! Code: " . $status_code);
        } elseif (isset($response->resp_code)) {
            if ($response->resp_code == '084') return array(
                "success" => false,
                "message" => "Payment pending! This might be due to insufficient fund in your mobile wallet or your payment session expired. Code: " . $response->resp_code
            );
            return array("success" => false, "message" => "Payment process failed! Code: " . $response->resp_code);
        }
        return array("success" => false, "message" => "Bad request: Payment process failed!");*/
    }

    /**
     * @param int $transaction_id
     * @return mixed
     */
    public function getTransactionStatusFromOrchard(int $transaction_id)
    {
        $payload = json_encode(array(
            "exttrid" => $transaction_id,
            "trans_type" => "TSC",
            "service_id" => getenv('ORCHARD_SERVID')
        ));
        $endpointUrl = "https://orchard-api.anmgw.com/checkTransaction";
        return $this->setOrchardPaymentGatewayParams($payload, $endpointUrl);
    }

    private function setOrchardPaymentGatewayParams($payload, $endpointUrl)
    {
        $client_id = getenv('ORCHARD_CLIENT');
        $client_secret = getenv('ORCHARD_SECRET');
        $signature = hash_hmac("sha256", $payload, $client_secret);

        $secretKey = $client_id . ":" . $signature;
        try {
            $pay = new OrchardPaymentGateway($secretKey, $endpointUrl, $payload);
            return $pay->initiatePayment();
        } catch (\Exception $e) {
            throw $e;
            return "Error: " . $e;
        }
    }
}
