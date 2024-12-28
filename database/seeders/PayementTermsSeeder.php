<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PayementTermsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('payement_terms')->insert(
            [
                [
                    'payement_term' => 'COD',
                ],
                [
                    'payement_term' => 'Prepay',
                ],
                [
                    'payement_term' => 'Partial Pay',
                ],
                [
                    'payement_term' => 'Net 30',
                ],
                [
                    'payement_term' => 'Net 15'
                ]
            ]
        );
    }
}
