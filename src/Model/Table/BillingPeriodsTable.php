<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class BillingPeriodsTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setDisplayField('type');
    }
}
