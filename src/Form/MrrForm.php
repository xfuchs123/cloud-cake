<?php

namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class MrrForm extends Form
{
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema->addField('date', 'date');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyDate('date', 'Please fill out the date')
            ->date('date', ['dmy'], 'Please fill out the date in format "dd.mm.yyyy"');

        return $validator;
    }

}
