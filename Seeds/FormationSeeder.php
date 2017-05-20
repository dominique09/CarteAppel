<?php

use Phinx\Seed\AbstractSeed;

class FormationSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [
            [
                'nom' => 'Premier Répondants Médical',
                'accronyme' => 'PRM'
            ],
            [
                'nom' => 'Secouriste Général',
                'accronyme' => 'SG'
            ]
        ];

        $this->table('formations')->insert($data)->save();
    }
}
