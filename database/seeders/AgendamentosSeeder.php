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
                'numero_sequencial' => 'AGD-001',
                'data' => '2025-10-28',
                'hora_inicio' => '09:00',
                'observacoes' => 'Visita ao imóvel',
                'cliente_id' => 1,
                'imovel_id' => 1,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'numero_sequencial' => 'AGD-002',
                'data' => '2025-10-29',
                'hora_inicio' => '14:30',
                'observacoes' => 'Avaliação técnica',
                'cliente_id' => 2,
                'imovel_id' => 2,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'numero_sequencial' => 'AGD-003',
                'data' => '2025-10-30',
                'hora_inicio' => '10:00',
                'observacoes' => 'Apresentação de proposta',
                'cliente_id' => 1,
                'imovel_id' => 3,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
