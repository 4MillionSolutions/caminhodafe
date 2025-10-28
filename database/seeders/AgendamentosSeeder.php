<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class AgendamentosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('agendamentos')->insert([
            [
                'data' => '2025-10-28',
                'hora' => '09:00',
                'descricao' => 'Visita ao imóvel',
                'cliente_id' => 1,
                'imovel_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'data' => '2025-10-29',
                'hora' => '14:30',
                'descricao' => 'Avaliação técnica',
                'cliente_id' => 2,
                'imovel_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'data' => '2025-10-30',
                'hora' => '10:00',
                'descricao' => 'Apresentação de proposta',
                'cliente_id' => 1,
                'imovel_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
