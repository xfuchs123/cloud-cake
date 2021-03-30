<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Initial extends AbstractMigration
{
    public $autoId = false;

    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-up-method
     * @return void
     */
    public function up(): void
    {
        $this->table('currencies')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('eur_exchange_rate', 'decimal', [
                'default' => null,
                'null' => false,
                'precision' => 10,
                'scale' => 5,
                'signed' => false,
            ])
            ->create();
        //as of 28.3.2021
        $rows = [
            [
                'id'    => 1,
                'name'  => 'Euro',
                'eur_exchange_rate' => 1.00
            ],
            [
                'id'    => 2,
                'name'  => 'United States Dollar',
                'eur_exchange_rate' => 0.85
            ],
            [
                'id'    => 3,
                'name'  => 'Australian Dollar',
                'eur_exchange_rate' => 0.65
            ],
            [
                'id'    => 4,
                'name'  => 'Chinese yuan',
                'eur_exchange_rate' => 0.13
            ],
            [
                'id'    => 5,
                'name'  => 'Czech Koruna',
                'eur_exchange_rate' => 0.04
            ],
            [
                'id'    => 6,
                'name'  => 'Pound Sterling',
                'eur_exchange_rate' => 1.17
            ],
            [
                'id'    => 7,
                'name'  => 'Poland zloty',
                'eur_exchange_rate' => 0.21
            ],
            [
                'id'    => 8,
                'name'  => 'Swiss Franc',
                'eur_exchange_rate' => 0.90
            ],
            [
                'id'    => 9,
                'name'  => 'Russian Ruble',
                'eur_exchange_rate' => 0.011
            ],
            [
                'id'    => 10,
                'name'  => 'Japanese Yen',
                'eur_exchange_rate' => 0.0077
            ]
        ];

        $this->table('currencies')->insert($rows)->save();

        $this->table('billing_periods')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('type', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('to_monthly_exchange', 'decimal', [
                'default' => '1.00',
                'null' => false,
                'precision' => 10,
                'scale' => 5,
                'signed' => false,
            ])
        ->create();
        $rows = [
            [
                'id' => 1,
                'type' => 'monthly',
                'to_monthly_exchange' => 1.00,
            ],
            [
                'id' => 2,
                'type' => 'quarterly',
                'to_monthly_exchange' => 0.25,
            ],
            [
                'id' => 3,
                'type' => 'bi-yearly',
                'to_monthly_exchange' => 0.16666,
            ],
            [
                'id' => 4,
                'type' => 'yearly',
                'to_monthly_exchange' => 0.08333,
            ]
        ];
        $this->table('billing_periods')->insert($rows)->save();

        $this->table('services')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('unit_cost', 'decimal', [
                'default' => '0.00',
                'null' => false,
                'precision' => 10,
                'scale' => 2,
                'signed' => false,
            ])
            ->addColumn('billing_period_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('valid_from', 'date', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('valid_to', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addTimestamps('created', 'modified')
            ->addColumn('notes', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('currency_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => false,
            ])
            ->addIndex(
                [
                    'currency_id',
                    'billing_period_id'
                ]
            )
            ->create();

        $this->table('services')
            ->addForeignKey(
                'currency_id',
                'currencies',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                ]
            )
            ->addForeignKey(
                'billing_period_id',
                'billing_periods',
                'id',
                [
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                ]
            )
            ->update();
    }

    /**
     * Down Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-down-method
     * @return void
     */
    public function down(): void
    {
        $this->table('services')
            ->dropForeignKey(
                ['currency_id','billing_period_id']
            )->save();
        $this->execute('DELETE FROM currencies');
        $this->execute('DELETE FROM billing_periods');
        $this->table('services')->drop()->save();
        $this->table('currencies')->drop()->save();
        $this->table('billing_periods')->drop()->save();
    }
}
