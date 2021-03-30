<?php

namespace App\Model\Table;

use ArrayObject;
use Cake\Event\EventInterface;
use Cake\I18n\Date;
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
            ->setForeignKey('currency_id');
        $this->belongsTo('BillingPeriods')
            ->setForeignKey('billing_period_id');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->requirePresence(['name','unit_cost','billing_period_id','valid_from','currency_id'])
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
            'on' => function($value){
                return !empty($value);
            }
        ])
            ->add('valid_to', 'greter_than_from',[
                'rule' => function($value, $context) {
                    if (!empty($context['data']['valid_from']) && strtotime($value) > strtotime($context['data']['valid_from'])) {
                        return true;
                    }
                    return "The date must be greater than the from field.";
                },
                'message' => 'Please enter date in format "dd.mm.yyyy"',
                'on' => function ($value) {
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
                    'rule' => ['decimal', 2, '/^\d+(\.\d{0,2})?$/'],
                    'message' =>  'Please enter at most 2 decimal digits',
                ]
            ]);
        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('currency_id', 'Currencies'), ['message' => 'Please select a valid currency']);
        $rules->add($rules->existsIn('billing_period_id', 'BillingPeriods'), ['message' => 'Please select a valid billing period']);

        return $rules;
    }

    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options)
    {
        if (!empty($data['valid_to'])) {
            $data['valid_to'] = new Date($data['valid_to']);
        }
        if (!empty($data['valid_from'])) {
            $data['valid_from'] = new Date($data['valid_from']);
        }
    }

}
