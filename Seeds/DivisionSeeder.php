<?php

use Phinx\Seed\AbstractSeed;

class DivisionSeeder extends AbstractSeed
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
                'nom' => 'Lévis',
                'numero' => 'D171',
            ],[
                'nom' => 'Québec',
                'numero' => 'D062',
            ],[
                'nom' => 'Région Québec',
                'numero' => 'R02-3',
            ],
        ];

        $this->table('divisions')->insert($data)->save();
    }
}
