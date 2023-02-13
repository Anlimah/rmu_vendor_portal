<?php

namespace Src\Controller;

use Src\System\DatabaseMethods;
use Src\Controller\ExposeDataController;

class AdmitApplicant
{
    private $dm = null;
    private $expose = null;

    public function __construct($prog_category, $cert_type, $data)
    {
        $this->dm = new DatabaseMethods();
        $this->expose = new ExposeDataController();
    }

    
}
