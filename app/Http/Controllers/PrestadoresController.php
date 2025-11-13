<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\ValidaPermissaoAcessoController;
use Illuminate\Http\Request;
use App\Http\Controllers\Helpers;
use App\Models\Arquivos;
use App\Models\Bancos;
use App\Models\Clientes;
use App\Models\Prestadores;
use App\Models\PrestadoresRegioes;
use App\Models\PrestadoresRegioesServicos;
use App\Models\Servicos;
use Google\Service\AdExchangeBuyerII\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mime\Header\DateHeader;
use Yajra\DataTables\Facades\DataTables; // se usar o pacote yajra/laravel-datatables


class PrestadoresController extends Controller
{
     public $permissoes_liberadas = [];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $this->permissoes_liberadas = (new ValidaPermissaoAcessoController())->validaAcaoLiberada(1, (new ValidaPermissaoAcessoController())->retornaPerfil());

        $id = !empty($request->input('id')) ? ($request->input('id')) : (!empty($id) ? $id : false);

        $prestadores = new Prestadores();

        if ($id) {
            $prestadores = $prestadores->where('id', '=', $id);
        }

        if (!empty($request->input('ativo'))) {
            $prestadores = $prestadores->where('ativo', '=', $request->input('ativo'));
        } else {
            $prestadores = $prestadores->where('ativo', '=', '1');
        }

        if (!empty($request->input('nome'))) {
            $prestadores = $prestadores->where('nome', 'like', '%' . $request->input('nome') . '%');
        }




        $prestadores = $prestadores->get();
        $array_latitude_longitude   = [];

        foreach ($prestadores as &$prestador) {

            $prestador->created_at = Helpers::formatDate_datahoraminutosegundo($prestador->created_at);

            $PrestadoresRegioes = new PrestadoresRegioes();
            $PrestadoresRegioes = $PrestadoresRegioes->where('prestadores_id', '=', $prestador->id)->where('ativo', '=', '1')->get();

            $PrestadoresRegioesServicos = new PrestadoresRegioesServicos();
            $PrestadoresRegioesServicos = $PrestadoresRegioesServicos->where('prestadores_id', '=', $prestador->id)->where('ativo', '=', '1')->get();

            $tabela_regioes = [];
            $servicos_habilitados = [];

            foreach($PrestadoresRegioesServicos as $PrestadoresRegioesServico){

                $servicos_habilitados[$PrestadoresRegioesServico->prestadores_regioes_id][] = $PrestadoresRegioesServico->servicos_id;

            }

            $Helpers = new Helpers();

            $estados = $Helpers->getEstados();


            foreach($PrestadoresRegioes as $regioes){


                $servicos = implode(', ', $servicos_habilitados[$regioes->id]);

                $regioes->valor = number_format($regioes->valor, 2, ',', '.');

                $nome_estado = $estados[$regioes->uf-1]['estado'];

                $array_latitude_longitude[] =[
                    'latitude' => $regioes->latitude,
                    'longitude' => $regioes->longitude
                ];

                $tabela_regioes[] = "
                    <tr>
                        <td>
                            $nome_estado
                            <input type='hidden' name='modal_tabela_stados[]' value='$regioes->uf'>
                        </td>

                        <td>
                            $regioes->cidade
                            <input type='hidden' name='modal_tabela_cidades[]' value='$regioes->cidade'>
                            <input type='hidden' name='modal_tabela_latitude[]' value='$regioes->latitude'>
                            <input type='hidden' name='modal_tabela_longitude[]' value='$regioes->longitude'>
                        </td>

                        <td>
                            {$regioes->raio}
                            <input type='hidden' name='modal_tabela_raios[]' value='$regioes->raio'>
                        </td>

                        <td>
                            {$servicos}
                            <input type='hidden' name='modal_tabela_servicos[]' value='$servicos'>
                        </td>

                        <td>
                            {$regioes->valor}
                            <input type='hidden' name='modal_tabela_valores[]' value='$regioes->valor'>
                        </td>
                    </tr>
                ";
            };

            $prestador->tabela_regioes = $tabela_regioes;
            $prestador->array_latitude_longitude = $array_latitude_longitude;


            $arquivos = new Arquivos();
            $arquivos = $arquivos->where('tipo_relacionamento_id', '=', 2) // 2 para Prestador
                                 ->where('relacionamento_id', '=', $prestador->id)
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


            $prestador->arquivos_html = $html_arquivos;



        }



        $tela = 'pesquisa';
        $data = array(
            'tela' => $tela,
            'nome_tela' => 'prestadores',
            'prestadores' => $prestadores,
            'estados' => (new Helpers())->getEstados(),
            'servicos' => (new Servicos())->orderby('id')->get(),
            'bancos' => (new Bancos())->orderby('nome')->get(),
            'request' => $request,
            'rotaIncluir' => 'incluir-prestadores',
            'rotaAlterar' => 'alterar-prestadores'
        );

        if (request()->ajax()) {
            return response()->json($data);
        }

        return view('prestadores', $data);
    }

    public function getData(Request $request)
    {
        $this->permissoes_liberadas = (new ValidaPermissaoAcessoController())->validaAcaoLiberada(1, (new ValidaPermissaoAcessoController())->retornaPerfil());
        $prestadores = new Prestadores();

        $prestadores = $prestadores::select(['id', 'nome', 'ativo'])
            ->where('ativo', '=', '1');

        return DataTables::of($prestadores)
            ->filter(function ($query) use ($request) {
                if ($search = $request->get('search')['value'] ?? null) {
                    $query->where(function ($q) use ($search) {
                        $q->where('nome', 'like', "%{$search}%")
                            ->orWhere('ativo', 'like', "%{$search}%");
                    });
                }
            })
            ->setRowId(function ($row) {
                return 'cliente_' . $row->id;
            })
            ->editColumn('ativo', function ($row) {
                return $row->ativo ? 'Ativo' : 'Inativo';
            })
            ->addColumn('acoes', function ($row) {
                $alterar = $excluir = $duplicar ='';

                if (in_array(2, $this->permissoes_liberadas)){

                    $alterar = '<i data-id="' . $row->id . '" data-toggle="modal" data-target="#modal_alteracao" title="Editar" class="fas fa-edit alterar_prestadores pointer habilita_editar ml-3"></i>';
                }
                if (in_array(3, $this->permissoes_liberadas)){

                    $excluir = '<i data-id="' . $row->id . '" id="excluir" title="Excluir" class="fa fa-solid fa-trash pointer ml-3"></i>';

                }
                if (in_array(4, $this->permissoes_liberadas)){

                    $duplicar = '<i data-id="'.$row->id.'" id="duplicar" title="Duplicar" class="fa fa-solid fa-trash pointer ml-3"></i>';

                }

                return '<i data-id="'.$row->id.'" data-toggle="modal" data-target="#modal_alteracao" title="Visualizar" class="fas fa-eye alterar_prestadores desabilita_editar pointer"></i>'.
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
            $prestadores = $this->salva($request);
        }
        return response()->json($prestadores->toArray());
    }

    public function excluir(Request $request)
    {
        try{
            $prestadores = new Prestadores();
            $prestadores = $prestadores->where('id', '=', $request->input('id'));
            $prestadores->delete();

            $data = array(
                'nome_tela' => 'Prestadores',
                'prestadores'=> $prestadores,
                'request' => $request,
                'rotaIncluir' => 'incluir-prestadores',
                'rotaAlterar' => 'alterar-prestadores'
            );
            return view('prestadores', $data);

        } catch (\Exception $e){
            return response(['error' => 'Erro ao excluir técnico: ' . $e->getMessage()], 500);
        }

    }

    public function alterar(Request $request)
    {
        $prestadores = new Prestadores();
        $prestadores = $prestadores->where('id', '=', $request->input('id'));

        $metodo = $request->method();
        if ($metodo == 'POST') {
            $prestadores = $this->salva($request);
        }

        $data = [
            "id" => $prestadores->id,
            "nome" => $prestadores->nome,
            "ativo" => $prestadores->ativo,
            "acao" => '<button class="btn btn-sm btn-primary alterar_prestadores acao_abrir_modal_alterar" data-id="' . $prestadores->id . '" data-toggle="modal" data-target="#modal_alteracao">Editar</button>'
        ];
        return response()->json($data);
    }

    public function salva($request)
    {

        $id = DB::transaction(function () use ($request) {

            $prestadores = new Prestadores();
            if ($request->input('id')) {
                $prestadores = $prestadores::where('id', $request->input('id'))->first();
            }

            $ativo = ($request->input('status') == 'on') ? true : false;
            $prestadores->nome = $request->input('nome');
            $prestadores->tipo_pessoa = !empty($request->input('tipo_pessoa')) ? $request->input('tipo_pessoa') : 'F';
            $prestadores->documento = Helpers::somenteNumeros($request->input('documento'));
            $prestadores->profissao = $request->input('profissao');
            $prestadores->endereco = $request->input('endereco');
            $prestadores->funcao = $request->input('funcao');
            $prestadores->crea_cau = $request->input('crea_cau');
            $prestadores->complemento = $request->input('complemento');
            $prestadores->numero = $request->input('numero');
            $prestadores->cep = Helpers::somenteNumeros($request->input('cep'));
            $prestadores->bairro = $request->input('bairro');
            $prestadores->cidade = $request->input('cidade');
            $prestadores->estado = $request->input('estado');
            $prestadores->telefone = Helpers::somenteNumeros($request->input('telefone'));
            $prestadores->email = $request->input('email');
            $prestadores->banco = $request->input('banco');
            $prestadores->agencia = $request->input('agencia');
            $prestadores->conta = $request->input('conta');
            $prestadores->tipo = $request->input('tipo');
            $prestadores->tipo_chave_pix = $request->input('tipo_chave_pix');
            $prestadores->chave_pix = $request->input('chave_pix');
            $prestadores->observacoes = $request->input('observacoes');
            $prestadores->ativo = $ativo;
            $prestadores->save();

            $PrestadoresRegioes = new PrestadoresRegioes();

            $stados = $request->input('modal_tabela_stados');

            $raio = $request->input('modal_tabela_raios');
            $cidades = $request->input('modal_tabela_cidades');
            $valores = $request->input('modal_tabela_valores');
            $longitudes = $request->input('modal_tabela_longitude');
            $latitudes = $request->input('modal_tabela_latitude');

            $servicos = $request->input('modal_tabela_servicos');

            $PrestadoresRegioes::where('prestadores_id', $prestadores->id)->delete();

            //exclui todos que estão salvos para o prestador
            $PrestadoresRegioesServicos = new PrestadoresRegioesServicos();
            $PrestadoresRegioesServicos::where('prestadores_id', $prestadores->id)->delete();

            if(!empty($stados)){
                foreach ($stados as $key => $estado) {

                    $PrestadoresRegioes = new PrestadoresRegioes();
                    $PrestadoresRegioes->prestadores_id = $prestadores->id;
                    $PrestadoresRegioes->uf = $estado;
                    $PrestadoresRegioes->raio = $raio[$key];
                    $PrestadoresRegioes->cidade = $cidades[$key];
                    $PrestadoresRegioes->longitude = $longitudes[$key];
                    $PrestadoresRegioes->latitude = $latitudes[$key];
                    $PrestadoresRegioes->valor = Helpers::formatFloatValue($valores[$key]);
                    $PrestadoresRegioes->observacoes = $request->input('observacoes');
                    $PrestadoresRegioes->ativo = $ativo;
                    $PrestadoresRegioes->save();


                    $servicos_por_linha = explode(',', $servicos[$key]);
                    foreach ($servicos_por_linha as $servico) {
                        $PrestadoresRegioesServicos = new PrestadoresRegioesServicos();
                        $PrestadoresRegioesServicos->prestadores_id = $prestadores->id;
                        $PrestadoresRegioesServicos->servicos_id = $servico;
                        $PrestadoresRegioesServicos->prestadores_regioes_id = $PrestadoresRegioes->id;
                        $PrestadoresRegioesServicos->ativo = $ativo;
                        $PrestadoresRegioesServicos->save();
                    }
                }
            }

            if ($request->hasFile('modal_arquivos')) {
                $arquivos = $request->file('modal_arquivos');
                $diretorio = 'prestadores/' . $prestadores->id . '/';
                foreach ($arquivos as $arquivo) {
                    $nome_arquivo = $arquivo->getClientOriginalName();
                    $caminho_arquivo = $arquivo->storeAs($diretorio, $nome_arquivo);

                    // Aqui você pode salvar o caminho do arquivo no banco de dados
                    $arquivoModel = new Arquivos();
                    $arquivoModel->tipo_relacionamento_id = 2; // 2 para Prestador
                    $arquivoModel->relacionamento_id = $prestadores->id;
                    $arquivoModel->nome_original = $nome_arquivo;
                    $arquivoModel->caminho = $caminho_arquivo;
                    $arquivoModel->tipo_mime = $arquivo->getClientMimeType();
                    $arquivoModel->tamanho = $arquivo->getSize();
                    $arquivoModel->extensao = $arquivo->getClientOriginalExtension();
                    $arquivoModel->save();

                }
            }



            return $prestadores;
        });

        return $id;
    }



    public function getAllPrestadores()
    {
        $prestadores = new Prestadores();
        $query = $prestadores->where('ativo', '=', '1');

        return $query->get();
    }
}
