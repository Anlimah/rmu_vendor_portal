<?php

namespace Src\Controller;

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
        if (!empty($data)) {
            $trans_id = time();
            if ($trans_id) {
                return $this->voucher->SaveFormPurchaseData($data, $trans_id);
            } else {
                return array("success" => false, "message" => "Transaction ID failed!");
            }
        }
    }

    public function verifyVendorPurchase(int $vendor_id, int $transaction_id)
    {
    }
}
