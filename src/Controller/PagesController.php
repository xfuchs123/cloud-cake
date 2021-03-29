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
use Cake\Http\Client\Exception\RequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
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
    public $paginate = [
        'contain' => ['Currencies','BillingPeriods'],
        'limit' => 5,
        'order' => [
            'Services.created' => 'asc'
        ],

    ];



    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Paginator');
    }

    public function index(?int $pageNo = 0): void
    {
        $this->loadModel('Services');

        $paginateOpts =  [
            'contain' => ['Currencies', 'BillingPeriods'],
            'limit' => 5,
            'order' => [
                'Services.created' => 'asc'
            ],

        ];

        $this->set('services', $this->paginate($this->Services->find(), $paginateOpts));
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

    public function edit($serviceId)
    {
        $this->loadModel('Services');
        $this->set('billing_periods', $this->loadModel('BillingPeriods')->find()->select(['id','type'])->all());
        $this->set('currencies', $this->loadModel('Currencies')->find()->select(['id','name'])->all());
        $updateService = $this->Services->get($serviceId);

        if (!empty($this->request->getData()))
        {
            $updateService = $this->Services->patchEntity($updateService, $this->request->getData(), ['associated' => ['Currencies', 'BillingPeriods']]);
            if ($updateService->getErrors() || (!$this->Services->save($updateService)))
            {
                $this->Flash->error('There was one or more errors in your input. Please check them and try again.');
                $this->set('values',$this->request->getData());
                $this->set('errors', $updateService->getErrors());

                return;
            }

            $this->Flash->success('Service saved successfully');
            $this->redirect('/');
        }

        $this->set('values',[]);
        $this->set('errors', []);

    }

    public function new(): void
    {
        $this->loadModel('Services');
        $this->set('billing_periods', $this->loadModel('BillingPeriods')->find()->select(['id','type'])->all());
        $this->set('currencies', $this->loadModel('Currencies')->find()->select(['id','name'])->all());

        if (!empty($this->request->getData()))
        {
            $newService = $this->Services->newEntity($this->request->getData(), ['associated' => ['Currencies', 'BillingPeriods']]);
            if ($newService->getErrors() || (!$this->Services->save($newService)))
            {
                $this->Flash->error('There was one or more errors in your input. Please check them and try again.');
                $this->set('values',$this->request->getData());
                $this->set('errors', $newService->getErrors());

                return;
            }

            $this->Flash->success('Service saved successfully');
            $this->redirect('/');
        }

        $this->set('values',[]);
        $this->set('errors', []);

    }

    /**
     * @param $serviceId
     */
    public function detail($serviceId): void
    {
        $this->loadModel('Services');
        $service = $this->Services->get($serviceId, ['contain' => ['Currencies', 'BillingPeriods']]);
        $this->set('service', $service);
    }

    public function mrr()
    {
        $mrrForm = new MrrForm();

        if ($this->request->is('post')) {
            if ($mrrForm->execute($this->request->getData()) && !$mrrForm->getErrors())
            {
                $this->loadModel('Services');
                $date = $mrrForm->getData('date');
                /** @var Query $query */
                $query = $this->Services->find()->contain(['Currencies','BillingPeriods']);

                $query->select(['bill' => $query->func()->sum(
                    'Currencies.eur_exchange_rate * BillingPeriods.to_monthly_exchange * unit_cost'
                )]);

                $query->where(function(QueryExpression $exp, Query $q) use ($date){
                    $orConditions = $exp->or(function (QueryExpression $or) use ($date){
                        return $or->isNull('valid_to')
                            ->lte('valid_to', $date);
                    });
                    return $exp->add($orConditions)->gte('valid_from', $date);
                });

                $this->set('bill', !is_null($query->first()->get('bill'))?:0);
            } else {
                $this->set('errors', $mrrForm->getErrors());
                $this->Flash->error('There was error in your input...');
            }

        }

        $this->set('mrrForm',$mrrForm);

    }

    /**
     * Displays a view
     *
     * @param string ...$path Path segments.
     * @return Response|null
     * @throws ForbiddenException When a directory traversal attempt.
     * @throws MissingTemplateException When the view file could not
     *   be found and in debug mode.
     * @throws NotFoundException When the view file could not
     *   be found and not in debug mode.
     * @throws MissingTemplateException In debug mode.
     */
    public function display(string ...$path): ?Response
    {
        if (!$path) {
            return $this->redirect('/');
        }
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        try {
            return $this->render(implode('/', $path));
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }
    }
}
