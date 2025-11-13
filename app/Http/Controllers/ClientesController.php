<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\ValidaPermissaoAcessoController;
use Illuminate\Http\Request;
use App\Http\Controllers\Helpers;
use App\Models\Arquivos;
use App\Models\Clientes;
use App\Models\Tecnicos;
use Google\Service\AdExchangeBuyerII\Date;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables; // se usar o pacote yajra/laravel-datatables


class ClientesController extends Controller
{
    public $permissoes_liberadas = [];

    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index(Request $request)
    {

        $this->permissoes_liberadas = (new ValidaPermissaoAcessoController())->validaAcaoLiberada(1, (new ValidaPermissaoAcessoController())->retornaPerfil());
        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        $clientes = new Clientes();
        $clientes = $clientes::select(
            'id',
            'nome_empresa',
            'nome',
            'tipo_pessoa',
            'documento',
            'endereco',
            'complemento',
            'numero',
            'cep',
            'bairro',
            'cidade',
            'estado',
            'telefone_cliente',
            'telefone',
            'email',
            'email_cliente',
            'observacoes',
            'ativo',
            DB::raw("DATE_FORMAT(clientes.created_at, '%d/%m/%Y') as created_at")
        );
        if ($id) {
            $clientes = $clientes->where('id', '=', $id);
        }

        if (!empty($request->input('ativo'))){
            $clientes = $clientes->where('ativo', '=', $request->input('ativo'));
        } else {
            $clientes = $clientes->where('ativo', '=', '1');
        }

        if (!empty($request->input('nome'))) {
            $clientes = $clientes->where('nome', 'like', '%'.$request->input('nome').'%')
                                 ->orWhere('nome_empresa', 'like', '%'.$request->input('nome').'%');
        }


        $clientes = $clientes->get();

        if (request()->ajax()) {
            foreach($clientes as &$cliente){
                $arquivos = new Arquivos();
                    $arquivos = $arquivos->where('tipo_relacionamento_id', '=', 1) // 1 para Cliente
                                        ->where('relacionamento_id', '=', $cliente->id)
                                        ->get();

                    $html_arquivos = '';
                    foreach($arquivos as $arquivo){

                        $url_download = route('arquivos.download', $arquivo->id);

                        $tamanho_em_mb = Helpers::formatBytes($arquivo->tamanho);
                        $data_hora = date('d/m/Y H:i:s', strtotime($arquivo->created_at));
                        $html_arquivos .= "
                            <tr>
                                <td>{$arquivo->nome_original}</td>
                                <td>$tamanho_em_mb</td>
                                <td>$data_hora</td>
                                <td><a href='{$url_download}'><i id='download' class='fa fa-download'></i></a></td>
                            </tr>
                        ";
                    }

                    $cliente->arquivos_html = $html_arquivos;
            }
        }

        $tela = 'pesquisa';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'clientes',
            'clientes'=> $clientes,
            'estados'=> (new Helpers())->getEstados(),
            'request' => $request,
            'rotaIncluir' => 'incluir-clientes',
            'rotaAlterar' => 'alterar-clientes'
        );

        if (request()->ajax()) {
            return response()->json($data);
        }

        return view('clientes', $data);
    }

    public function getData(Request $request)
    {
        $this->permissoes_liberadas = (new ValidaPermissaoAcessoController())->validaAcaoLiberada(1, (new ValidaPermissaoAcessoController())->retornaPerfil());
        $clientes = new Clientes();

        $clientes = $clientes::select(['id', 'nome_empresa as nome_empresa', 'nome as nome', 'ativo'])
                            ->where('ativo', '=', '1');

        return DataTables::of($clientes)
            ->filter(function ($query) use ($request) {
                if ($search = $request->get('search')['value'] ?? null) {
                    $query->where(function($q) use ($search) {
                        $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('ativo', 'like', "%{$search}%");
                    });
                }
            })
            ->setRowId(function($row) {
                return 'cliente_' . $row->id;
            })
            ->editColumn('ativo', function ($row) {
                return $row->ativo ? 'Ativo' : 'Inativo';
            })
            ->addColumn('acoes', function ($row) {

                $alterar = $excluir = $duplicar ='';

                if (in_array(2, $this->permissoes_liberadas)){

                    $alterar = '<i data-id="'.$row->id.'" data-toggle="modal" data-target="#modal_alteracao" title="Editar" class="fas fa-edit alterar_clientes habilita_editar pointer ml-3"></i>';
                }
                if (in_array(3, $this->permissoes_liberadas)){

                    $excluir = '<i data-id="'.$row->id.'" id="excluir" title="Excluir" class="fa fa-solid fa-trash pointer ml-3"></i>';

                }
                if (in_array(4, $this->permissoes_liberadas)){

                    $duplicar = '<i data-id="'.$row->id.'" id="duplicar" title="Duplicar" class="fa fa-solid fa-trash pointer ml-3"></i>';

                }

                return '
                        <i data-id="'.$row->id.'" data-toggle="modal" data-target="#modal_alteracao" title="Visualizar" class="fas fa-eye alterar_clientes desabilita_editar pointer"></i>'.
                        $alterar.
                        $excluir.
                        $duplicar;
            })
            ->rawColumns(['acoes'])
            ->make(true);
    }



    public function incluir(Request $request)
    {
        $metodo = $request->method();

        if ($metodo == 'POST') {
            $clientes = $this->salva($request);
        }
        return response()->json($clientes->toArray());

    }

    public function alterar(Request $request)
    {
        $clientes = new Clientes();
        $clientes = $clientes->where('id', '=', $request->input('id'));

        $metodo = $request->method();
        if ($metodo == 'POST') {
            $clientes = $this->salva($request);
        }

        $data = [
            "id" => $clientes->id,
            "nome" => $clientes->nome_empresa,
            "ativo" => $clientes->ativo,
            "acao" => '<button class="btn btn-sm btn-primary alterar_clientes" data-id="'.$clientes->id.'" data-toggle="modal" data-target="#modal_alteracao">Editar</button>'
        ];
        return response()->json($data);
    }

     public function excluir(Request $request)
    {
        try{
            $clientes = new Clientes();
            $clientes = $clientes->where('id', '=', $request->input('id'));
            $clientes->delete();

            $data = array(
                'nome_tela' => 'clientes',
                'clientes'=> $clientes,
                'request' => $request,
                'rotaIncluir' => 'incluir-clientes',
                'rotaAlterar' => 'alterar-clientes'
            );
            return view('clientes', $data);

        } catch (\Exception $e){
            return response(['error' => 'Erro ao excluir técnico: ' . $e->getMessage()], 500);
        }

    }

    public function salva($request) {

        $clientes = new Clientes();
        if($request->input('id')) {
            $clientes = $clientes::where('id', $request->input('id'))->first();
        }

        $ativo = ($request->input('status') == 'on') ? true : false;
        $clientes->nome_empresa = $request->input('nome_empresa');
        $clientes->nome = $request->input('nome');
        $clientes->tipo_pessoa = !empty($request->input('tipo_pessoa')) ? $request->input('tipo_pessoa') : 'F';
        $clientes->documento = Helpers::somenteNumeros($request->input('documento'));
        $clientes->endereco = $request->input('endereco');
        $clientes->complemento = $request->input('complemento');
        $clientes->numero = $request->input('numero');
        $clientes->cep = Helpers::somenteNumeros($request->input('cep'));
        $clientes->bairro = $request->input('bairro');
        $clientes->cidade = $request->input('cidade');
        $clientes->estado = $request->input('estado');
        $clientes->telefone_cliente = Helpers::somenteNumeros($request->input('telefone_cliente'));
        $clientes->telefone = Helpers::somenteNumeros($request->input('telefone'));
        $clientes->email_cliente = $request->input('email_cliente');
        $clientes->email = $request->input('email');
        $clientes->observacoes = $request->input('observacoes');

        $clientes->ativo = $ativo;
        $clientes->save();

        if ($request->hasFile('modal_arquivos')) {
            $arquivos = $request->file('modal_arquivos');
            $diretorio = 'clientes/' . $clientes->id . '/';
            foreach ($arquivos as $arquivo) {
                $nome_arquivo = $arquivo->getClientOriginalName();
                $caminho_arquivo = $arquivo->storeAs($diretorio, $nome_arquivo);

                // Aqui você pode salvar o caminho do arquivo no banco de dados
                $arquivoModel = new Arquivos();
                $arquivoModel->tipo_relacionamento_id = 1; // 1 para Cliente
                $arquivoModel->relacionamento_id = $clientes->id;
                $arquivoModel->nome_original = $nome_arquivo;
                $arquivoModel->caminho = $caminho_arquivo;
                $arquivoModel->tipo_mime = $arquivo->getClientMimeType();
                $arquivoModel->tamanho = $arquivo->getSize();
                $arquivoModel->extensao = $arquivo->getClientOriginalExtension();
                $arquivoModel->save();

            }
        }

        return $clientes;
    }



    public function getAllClientes() {
        $clientes = new Clientes();
        $query = $clientes->where('ativo', '=', '1');

        return $query->get();
    }
}
