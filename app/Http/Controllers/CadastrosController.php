<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CadastrosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, $aba = null, $id = null)
    {

        if(!empty($id)){
            $request->merge(['id' => $id]);
        }

        if(empty($aba)){
            $aba = 'clientes';
        }

        switch ($aba) {
            case 'clientes':

                $Clientes = new ClientesController();

                $data = $Clientes->index($request)->getData();

                break;
            case 'tecnicos':

                $Tecnicos = new TecnicosController();
                $data = $Tecnicos->index($request)->getData();

                break;

            case 'prestadores':

                $Prestadores = new PrestadoresController();
                $data = $Prestadores->index($request)->getData();

                break;

            case 'servicos':

                $servicos = new ServicosController();
                $data = $servicos->index($request)->getData();

                break;

            case 'imoveis':

                $Imoveis = new ImoveisController();
                $data = $Imoveis->index($request)->getData();

                break;
            default:

            break;
        }

        if (request()->ajax()) {
            // retorna só os dados como JSON
            return response()->json($data);
        }
        return view('cadastros', $data);
    }

    public function acao(Request $request, $aba, $acao)
    {

        $data = array();
        switch ($aba) {
            case 'clientes':

                $Clientes = new ClientesController();
                $response = $Clientes->{$acao}($request);
                if ($response instanceof \Illuminate\Http\RedirectResponse) {
                    return $response;
                }
                $data = $response->getData();

                break;

            case 'prestadores':
                $Prestadores = new PrestadoresController();
                $response = $Prestadores->{$acao}($request);
                if ($response instanceof \Illuminate\Http\RedirectResponse) {
                    return $response;
                }
                $data = $response->getData();

                break;
            case 'tecnicos':

                $Tecnicos = new TecnicosController();
                $response = $Tecnicos->{$acao}($request);

                if ($response instanceof \Illuminate\Http\RedirectResponse) {
                    return $response;
                }
                $data = $response->getData();

                break;
            case 'servicos':

                $servicos = new ServicosController();
                $response = $servicos->{$acao}($request);

                if ($response instanceof \Illuminate\Http\RedirectResponse) {
                    return $response;
                }
                $data = $response->getData();

                break;
            default:

            break;
        }

        return $data;

    }

}
