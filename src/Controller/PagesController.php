<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use App\Form\MrrForm;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\I18n\Date;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\I18n\Time;
use Cake\ORM\Query;
use Cake\View\Exception\MissingTemplateException;

/**
 * Static content controller
 *
 * This controller will render views from templates/Pages/
 *
 * @link https://book.cakephp.org/4/en/controllers/pages-controller.html
 */
class PagesController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        Date::setToStringFormat('dd.MM.yyyy');
        FrozenDate::setToStringFormat('dd.MM.yyyy');
        Time::setToStringFormat('dd.MM.yyyy H:m:s');
        FrozenTime::setToStringFormat('dd.MM.yyyy H:m:s');
        $this->loadComponent('Paginator');
    }


    public function index(): void
    {
        $this->loadModel('Services');

        $paginateOpts =  [
            'contain' => [
                'Currencies' => [
                    'fields' => ['currency_name' => 'Currencies.name']
                ],
                'BillingPeriods' => [
                    'fields' => ['type']
                ]
            ],
            'limit' => 5,
            'sortableFields' => [
                'Services.name',
                'Services.unit_cost',
                'Services.valid_from',
                'Services.valid_to',
                'BillingPeriods.type',
                'Currencies.name'
            ]

        ];

        $this->set('services', $this->paginate($this->Services, $paginateOpts));
    }

    /**
     * @param $serviceId
     * @return Response|null
     */
    public function delete($serviceId): ?Response
    {
        $this->loadModel('Services');
        try{
            $entity = $this->Services->get($serviceId);
            $this->Services->delete($entity);
            $this->Flash->success('Service was deleted successfully');
        } catch (RecordNotFoundException $exception) {
            $this->Flash->error('Couldnt delete the service because the service was not found');
        }
        return $this->redirect('/');

    }

    /**
     * @param string $serviceId
     */
    public function edit(string $serviceId = '')
    {
        $this->set('billingPeriodOpts', $this->loadModel('BillingPeriods')->find('list')->toArray());
        $this->set('currencies', $this->loadModel('Currencies')->find('list')->toArray());
        $this->loadModel('Services');

        if ($serviceId === '0') {
            $service = $this->Services->newEmptyEntity();
        } else{
            $service = $this->Services->get($serviceId);
        }

        if ($this->request->is('post')) {
            $service = $this->Services->patchEntity($service, $this->request->getData(), ['associated' => ['Currencies', 'BillingPeriods']]);

            if ($service->getErrors() || (!$this->Services->save($service)))
            {
                $this->Flash->error('There was one or more errors in your input. Please check them and try again.');
                $this->set('service', $service);
                $this->set('errors', $service->getErrors());

                return;
            }


            $this->Flash->success('Service saved successfully');
            $this->redirect('/');
        }
        $this->set('service', $service);

        $this->set('values',[]);
        $this->set('errors', []);

    }

    /**
     * @param $serviceId
     */
    public function detail(string $serviceId): void
    {
        $this->loadModel('Services');
        $service = $this->Services->get($serviceId, [
            'contain' => [
            'Currencies' => [
                'fields' => ['currency_name' => 'Currencies.name']
            ],
            'BillingPeriods' => [
                'fields' => ['billing_period' => 'type']
            ]
            ],

        ])->toArray();

        $this->set('service', $service);
    }

    /**
     * @return void;
     */
    public function mrr(): void
    {
        $mrrForm = new MrrForm();

        if ($this->request->is('post')) {
            if ($mrrForm->execute($this->request->getData()) && !$mrrForm->getErrors())
            {
                $this->loadModel('Services');
                $date = new Date($mrrForm->getData('date'));
                /** @var Query $query */
                $query = $this->Services->find()->contain(['Currencies','BillingPeriods']);

                $query->select(['bill' => $query->func()->sum(
                    'eur_exchange_rate * to_monthly_exchange * unit_cost'
                )]);

                $query->where(function(QueryExpression $exp, Query $q) use ($date){
                    $orConditions = $exp->or(function (QueryExpression $or) use ($date){
                        return $or->isNull('valid_to')
                            ->gte('valid_to', $date->format('Y-m-d'));
                    });
                    return $exp->add($orConditions)->lte('valid_from', $date->format('Y-m-d'));
                });


                $this->set('bill', is_null($query->first()->get('bill'))?0:$query->first()->get('bill'));
                $this->set('date', $date->format("d.m.Y"));
            } else {
                $this->set('errors', $mrrForm->getErrors());
                $this->Flash->error('There was error in your input...');
            }

        }

        $this->set('mrrForm',$mrrForm);

    }
}
