<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ServicesTable extends Table
{
    public function initialize(array $config): void
    {
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'modified' => 'always',
                ]
            ]
        ]);
        $this->belongsTo('Currencies')
            ->setForeignKey('currency');
        $this->belongsTo('BillingPeriods')
            ->setForeignKey('billing_period');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->requirePresence(['name','unit_cost','billing_period','valid_from','currency'])
            ->notEmptyString('name', 'This field cannot be blank')
            ->notEmptyDate('valid_from', 'Please, select date')
            ->allowEmptyDate('valid_to')
            ->add('name', [
                'length' => [
                    'rule' => ['maxLength', 255],
                    'message' => 'Service name cannot exceed 255 characters',
                ]
            ])
            ->date('valid_from', ['dmy'], 'Please enter date in format "dd.mm.yyyy"')
        ->add('valid_to', 'date_string_not_empty',[
            'rule' => ['date', ['dmy']],
            'message' => 'Please enter date in format "dd.mm.yyyy"',
            'on' => function($value, $context){
                return !empty($value);
            }
        ])
            ->add('unit_cost',[
                'numeric' => [
                    'rule' => ['numeric'],
                    'last' => true,
                    'message' => 'Please input a number',
                ]
            ] )
            ->add('unit_cost', 'min_value', [
                'rule' => function($value, array $context) {
                    if ((double)$value < 0) {
                        return 'Please enter non-negative number';
                    }
                    return true;
                },
                'last' => true,
            ])
            ->add('unit_cost', [
                'decimal' => [
                    'rule' => ['decimal', 2],
                    'message' =>  'Please enter at most 2 decimal digits',
                    'on' => function($value, $context){
                        return !is_int($value);
                    }
                ]
            ]);
        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('currency', 'Currencies'), ['message' => 'Please select a valid currency']);
        $rules->add($rules->existsIn('billing_period', 'BillingPeriods'), ['message' => 'Please select a valid billing period']);

        return $rules;
    }

}
