<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Service extends Entity
{
    protected $_accessible = [
        'name' => true,
        'unit_cost' => true,
        'billing_period' => true,
        'valid_from' => true,
        'valid_to' => true,
        'notes' => true,
        'currency' => true,
    ];

}
