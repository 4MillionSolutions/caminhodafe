<?php

namespace App\Http\Controllers;

use App\Models\Arquivos;
use Illuminate\Support\Facades\Storage;

class ArquivosController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function download($id)
    {
        $arquivo = Arquivos::findOrFail($id);
        $path = $arquivo->caminho; // ex: 'documentos/contrato.pdf'

        return Storage::download($path, $arquivo->nome_original);
    }
}
