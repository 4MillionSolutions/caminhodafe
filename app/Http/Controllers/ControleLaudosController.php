<?php

namespace App\Http\Controllers;

use App\Models\Laudo;
use App\Models\Condominio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ControleLaudosController extends Controller
{
    public function index(Request $request)
    {
        $query = Laudo::with('condominios');

        // Filtros
        if ($request->filled('cliente')) {
            $query->porCliente($request->cliente);
        }

        if ($request->filled('status')) {
            $query->porStatus($request->status);
        }

        if ($request->filled('data_vistoria')) {
            $query->porData($request->data_vistoria);
        }

        if ($request->filled('resp_laudo')) {
            $query->where('resp_laudo', $request->resp_laudo);
        }

        if ($request->filled('resp_vistoria')) {
            $query->where('resp_vistoria', $request->resp_vistoria);
        }

        $laudos = $query->orderBy('data_vistoria', 'desc')
                       ->orderBy('hora_vistoria', 'desc')
                       ->paginate(50);

        // Dados para análise diária
        $analiseDiaria = $this->getAnaliseDiaria($request);

        return view('controle-laudos', compact('laudos', 'analiseDiaria'));
    }

    private function getAnaliseDiaria($request)
    {
        $query = Laudo::query();

        // Aplicar mesmos filtros da consulta principal
        if ($request->filled('cliente')) {
            $query->porCliente($request->cliente);
        }

        if ($request->filled('status')) {
            $query->porStatus($request->status);
        }

        if ($request->filled('data_vistoria')) {
            $query->porData($request->data_vistoria);
        }

        // Contadores
        $totalLaudos = $query->count();

        // Contagem por Cetip
        $cetipCount = $query->whereNotNull('cetip')
                           ->where('cetip', '!=', '')
                           ->count();

        // Contagem por responsáveis internos (estagiários)
        $estagiarios = [
            'Alexsandro', 'Aline', 'Tatiane', 'Tairine', 'Gabriela', 'Adenice', 'Luis'
        ];

        $internoCount = $query->whereIn('resp_laudo', $estagiarios)->count();

        // Contagem por responsáveis externos (vistoria)
        $externoCount = $query->whereNotNull('resp_vistoria')
                             ->where('resp_vistoria', '!=', '')
                             ->count();

        // Lista de estagiários com contagem
        $estagiariosComContagem = [];
        foreach ($estagiarios as $estagiario) {
            $count = $query->where('resp_laudo', $estagiario)->count();
            if ($count > 0) {
                $estagiariosComContagem[] = [
                    'nome' => $estagiario,
                    'quantidade' => $count
                ];
            }
        }

        // Lista de engenheiros/arquitetos com contagem
        $engenheiros = [
            'Alexsandro Silveira', 'Aline Silveira', 'Luis Souza', 'Ronaldo Samapio'
        ];

        $engenheirosComContagem = [];
        foreach ($engenheiros as $engenheiro) {
            $count = $query->where('resp_vistoria', 'like', "%{$engenheiro}%")->count();
            if ($count > 0) {
                $engenheirosComContagem[] = [
                    'nome' => $engenheiro,
                    'quantidade' => $count
                ];
            }
        }

        return [
            'total_laudos' => $totalLaudos,
            'cetip_count' => $cetipCount,
            'interno_count' => $internoCount,
            'externo_count' => $externoCount,
            'estagiarios' => $estagiariosComContagem,
            'engenheiros' => $engenheirosComContagem,
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente' => 'required|string|max:255',
            'proposta' => 'required|string|max:255',
            'endereco' => 'required|string',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|max:2',
        ]);

        $laudo = Laudo::create($request->all());

        // Criar condomínio se fornecido
        if ($request->filled('condominio_nome')) {
            Condominio::create([
                'nome' => $request->condominio_nome,
                'area_terreno' => $request->condominio_area_terreno,
                'area_privativa' => $request->condominio_area_privativa,
                'laudo_id' => $laudo->id,
            ]);
        }

        return redirect()->route('controle-laudos.index')
                        ->with('success', 'Laudo criado com sucesso!');
    }

    public function update(Request $request, Laudo $laudo)
    {
        $request->validate([
            'cliente' => 'required|string|max:255',
            'proposta' => 'required|string|max:255',
            'endereco' => 'required|string',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|max:2',
        ]);

        $laudo->update($request->all());

        return redirect()->route('controle-laudos.index')
                        ->with('success', 'Laudo atualizado com sucesso!');
    }

    public function destroy(Laudo $laudo)
    {
        $laudo->delete();

        return redirect()->route('controle-laudos.index')
                        ->with('success', 'Laudo excluído com sucesso!');
    }

    public function exportar()
    {
        // Implementar exportação para Excel
        return response()->json(['message' => 'Exportação será implementada']);
    }
}