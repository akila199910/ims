<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PayementTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('payement_types')->insert(
            [
                [
                    'payment_type' => 'Cash',
                ],
                [
                    'payment_type' => 'Cheque',
                ],
                [
                    'payment_type' => 'Bank',
                ]
            ]
        );
    }
}
