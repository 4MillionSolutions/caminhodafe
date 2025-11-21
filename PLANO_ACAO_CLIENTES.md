# Plano de Ação - Melhorias clientes.blade.php

**Projeto:** Caminho da Fé  
**Módulo:** Gestão de Clientes  
**Data:** 21 de novembro de 2025  
**Responsável:** Equipe de Desenvolvimento  
**Status:** 🟡 Planejamento

---

## 📊 Resumo Executivo

Este plano de ação visa corrigir **10 problemas identificados** e implementar **13 melhorias** no módulo de clientes, elevando a qualidade do código de **7.0** para **9.0+**.

**Tempo estimado total:** 16-20 horas  
**Prioridade:** Alta  
**Impacto esperado:** Melhoria significativa em performance, UX e manutenibilidade

---

## 🎯 Objetivos

### Objetivos Principais
1. ✅ Corrigir todos os bugs críticos e médios
2. ✅ Melhorar a experiência do usuário
3. ✅ Aumentar a segurança e validação
4. ✅ Otimizar performance
5. ✅ Facilitar manutenção futura

### KPIs de Sucesso
- ✅ 0 bugs críticos
- ✅ 100% dos campos validados
- ✅ Tempo de carregamento < 2s
- ✅ Score de qualidade > 9.0
- ✅ 0 duplicação de código

---

## 📋 Fases do Projeto

### **FASE 1: Correções Críticas** 🔴
**Prazo:** 1-2 dias  
**Esforço:** 4-6 horas

### **FASE 2: Melhorias Importantes** 🟡
**Prazo:** 2-3 dias  
**Esforço:** 6-8 horas

### **FASE 3: Funcionalidades Avançadas** 🟢
**Prazo:** 3-5 dias  
**Esforço:** 6-8 horas

### **FASE 4: Testes e Validação** ✅
**Prazo:** 1-2 dias  
**Esforço:** 2-4 horas

---

## 🔴 FASE 1: Correções Críticas

### 1.1 Remover Duplicação de Scripts
**Problema:** jQuery e plugins carregados duas vezes  
**Prioridade:** 🔴 CRÍTICA  
**Tempo:** 30 min  
**Complexidade:** Baixa

#### Tarefas
- [ ] Remover scripts do `@section('content_header')`
- [ ] Manter apenas em `@section('scripts')`
- [ ] Adicionar cache busting consistente
- [ ] Testar funcionamento das máscaras

#### Implementação
```blade
<!-- REMOVER do content_header -->
@section('content_header')
    @if(isset($tela) and $tela == 'pesquisa')
        <div class="form-group row">
            <h1 class="m-0 text-dark col-sm-11 col-form-label">Pesquisa de {{ $nome_tela }}</h1>
            <div class="col-sm-1">
                @include('layouts.nav-open-incluir', ['rotaIncluir' => $rotaIncluir])
            </div>
        </div>
    @endif
    <!-- REMOVER SCRIPTS DAQUI -->
@stop

<!-- MANTER apenas em scripts -->
@section('scripts')
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('js/jquery.mask.js') }}?v={{ config('app.version', '1.0') }}"></script>
    <script src="{{ asset('js/main_custom.js') }}?v={{ config('app.version', '1.0') }}"></script>
    <script src="{{ asset('js/validarAno.js') }}?v={{ config('app.version', '1.0') }}"></script>
@endsection
```

#### Critérios de Aceitação
- ✅ Scripts carregados apenas uma vez
- ✅ Máscaras funcionando corretamente
- ✅ Sem erros no console
- ✅ Performance melhorada

---

### 1.2 Adicionar Campo Data de Nascimento
**Problema:** Campo exibido na listagem mas ausente no formulário  
**Prioridade:** 🔴 CRÍTICA  
**Tempo:** 1 hora  
**Complexidade:** Média

#### Tarefas
- [ ] Adicionar campo no formulário
- [ ] Implementar máscara de data
- [ ] Adicionar validação de idade mínima/máxima
- [ ] Testar salvamento e edição

#### Implementação
```blade
<div class="row row-cols-md-3 g-3 mt-2">
    <div class="col-md-4">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" maxlength="200" 
               placeholder="Digite um email válido" value="{{ $clientes[0]->email ?? '' }}">
    </div>
    <div class="col-md-4">
        <label for="data_nascimento" class="form-label">Data de Nascimento*</label>
        <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" 
               required max="{{ date('Y-m-d') }}" 
               value="{{ isset($clientes[0]->data_nascimento) ? \Carbon\Carbon::parse($clientes[0]->data_nascimento)->format('Y-m-d') : '' }}">
        <small class="form-text text-muted">Formato: dd/mm/aaaa</small>
    </div>
</div>
```

#### Validações Backend (Controller)
```php
$request->validate([
    'data_nascimento' => 'required|date|before:today|after:1900-01-01',
]);
```

#### Critérios de Aceitação
- ✅ Campo visível e funcional
- ✅ Validação de data implementada
- ✅ Dados salvos corretamente no banco
- ✅ Formatação consistente

---

### 1.3 Padronizar Caminhos de Recursos
**Problema:** Caminhos relativos podem falhar  
**Prioridade:** 🔴 CRÍTICA  
**Tempo:** 30 min  
**Complexidade:** Baixa

#### Tarefas
- [ ] Substituir todos os caminhos relativos por `asset()`
- [ ] Padronizar cache busting
- [ ] Verificar carregamento em diferentes rotas

#### Implementação
```blade
<!-- ANTES -->
<script src="../vendor/jquery/jquery.min.js?cache={{time()}}"></script>
<script src="js/jquery.mask.js?cache={{time()}}"></script>

<!-- DEPOIS -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery.mask.js') }}?v={{ config('app.version', '1.0') }}"></script>
```

#### Critérios de Aceitação
- ✅ Todos os recursos usando `asset()`
- ✅ Cache busting padronizado
- ✅ Funciona em todas as rotas

---

### 1.4 Corrigir Inconsistência de Nomenclatura
**Problema:** Listagem usa `name`, formulário usa `nome`  
**Prioridade:** 🔴 CRÍTICA  
**Tempo:** 1 hora  
**Complexidade:** Média

#### Tarefas
- [ ] Verificar estrutura da tabela no banco
- [ ] Padronizar para usar `nome` em todo o código
- [ ] Atualizar Model se necessário
- [ ] Testar CRUD completo

#### Análise
```bash
# Verificar estrutura do banco
DESCRIBE clientes;
```

#### Implementação
```blade
<!-- Padronizar na listagem -->
<td>{{ $cliente->nome }}</td>  <!-- ao invés de $cliente->name -->
```

#### Critérios de Aceitação
- ✅ Nomenclatura consistente
- ✅ Sem erros de propriedade indefinida
- ✅ CRUD funcionando 100%

---

## 🟡 FASE 2: Melhorias Importantes

### 2.1 Implementar Validação de CPF
**Objetivo:** Validar CPF no frontend e backend  
**Prioridade:** 🟡 ALTA  
**Tempo:** 2 horas  
**Complexidade:** Média

#### Tarefas
- [ ] Criar função JavaScript de validação de CPF
- [ ] Integrar com o formulário
- [ ] Adicionar validação no backend
- [ ] Exibir mensagens de erro claras

#### Implementação Frontend
```javascript
// public/js/validarCPF.js
function validarCPF(cpf) {
    cpf = cpf.replace(/[^\d]+/g, '');
    
    if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false;
    
    let soma = 0;
    let resto;
    
    for (let i = 1; i <= 9; i++) {
        soma += parseInt(cpf.substring(i-1, i)) * (11 - i);
    }
    
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.substring(9, 10))) return false;
    
    soma = 0;
    for (let i = 1; i <= 10; i++) {
        soma += parseInt(cpf.substring(i-1, i)) * (12 - i);
    }
    
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.substring(10, 11))) return false;
    
    return true;
}

$(document).ready(function() {
    $('#documento').on('blur', function() {
        const cpf = $(this).val();
        if (cpf && !validarCPF(cpf)) {
            $('#cpf-error').show();
            $(this).addClass('is-invalid');
        } else {
            $('#cpf-error').hide();
            $(this).removeClass('is-invalid');
        }
    });
});
```

#### Implementação Backend
```php
// app/Rules/ValidCPF.php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidCPF implements Rule
{
    public function passes($attribute, $value)
    {
        $cpf = preg_replace('/[^0-9]/', '', $value);
        
        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        
        return true;
    }
    
    public function message()
    {
        return 'O CPF informado não é válido.';
    }
}

// No Controller
$request->validate([
    'documento' => ['required', new ValidCPF],
]);
```

#### Critérios de Aceitação
- ✅ Validação frontend funcionando
- ✅ Validação backend implementada
- ✅ Mensagens de erro claras
- ✅ CPFs inválidos bloqueados

---

### 2.2 Adicionar Feedback Visual
**Objetivo:** Informar usuário sobre ações realizadas  
**Prioridade:** 🟡 ALTA  
**Tempo:** 1 hora  
**Complexidade:** Baixa

#### Tarefas
- [ ] Adicionar mensagens de sucesso/erro
- [ ] Implementar loading durante submissão
- [ ] Adicionar confirmação de cancelamento
- [ ] Melhorar feedback de validação

#### Implementação - Mensagens Flash
```blade
@section('content')
    <!-- Mensagens de Feedback -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
```

#### Implementação - Loading
```javascript
// public/js/main_custom.js
$(document).ready(function() {
    // Loading no submit
    $('form').on('submit', function() {
        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true);
        btn.html('<i class="fas fa-spinner fa-spin"></i> Salvando...');
    });
    
    // Confirmação de cancelamento
    $('button[onclick*="history.back"]').on('click', function(e) {
        if ($('form').find('input, select, textarea').serialize() !== formOriginalData) {
            if (!confirm('Existem alterações não salvas. Deseja realmente cancelar?')) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        }
    });
    
    // Salvar dados originais do formulário
    let formOriginalData = $('form').find('input, select, textarea').serialize();
});
```

#### No Controller
```php
// Após salvar com sucesso
return redirect()->route('pesquisar-clientes')
    ->with('success', 'Cliente cadastrado com sucesso!');

// Em caso de erro
return back()
    ->with('error', 'Erro ao cadastrar cliente. Tente novamente.')
    ->withInput();
```

#### Critérios de Aceitação
- ✅ Mensagens flash exibidas corretamente
- ✅ Loading durante submissão
- ✅ Confirmação de cancelamento
- ✅ Auto-dismiss após 5 segundos

---

### 2.3 Melhorar Validações Frontend
**Objetivo:** Validar dados antes de enviar ao servidor  
**Prioridade:** 🟡 ALTA  
**Tempo:** 2 horas  
**Complexidade:** Média

#### Tarefas
- [ ] Implementar validação de email em tempo real
- [ ] Validar telefone (formato e DDD válido)
- [ ] Validar CEP (formato e existência)
- [ ] Adicionar contador de caracteres

#### Implementação
```javascript
// public/js/validacoes-clientes.js
$(document).ready(function() {
    // Validação de Email
    $('#email').on('blur', function() {
        const email = $(this).val();
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !regex.test(email)) {
            showError($(this), 'Email inválido');
        } else {
            hideError($(this));
        }
    });
    
    // Validação de Telefone
    $('#telefone').on('blur', function() {
        const telefone = $(this).val().replace(/\D/g, '');
        const dddsValidos = ['11', '12', '13', '14', '15', '16', '17', '18', '19', 
                             '21', '22', '24', '27', '28', '31', '32', '33', '34', 
                             '35', '37', '38', '41', '42', '43', '44', '45', '46', 
                             '47', '48', '49', '51', '53', '54', '55', '61', '62', 
                             '63', '64', '65', '66', '67', '68', '69', '71', '73', 
                             '74', '75', '77', '79', '81', '82', '83', '84', '85', 
                             '86', '87', '88', '89', '91', '92', '93', '94', '95', 
                             '96', '97', '98', '99'];
        
        if (telefone && telefone.length >= 10) {
            const ddd = telefone.substring(0, 2);
            if (!dddsValidos.includes(ddd)) {
                showError($(this), 'DDD inválido');
                return;
            }
        }
        hideError($(this));
    });
    
    // Busca CEP
    $('#cep').on('blur', function() {
        const cep = $(this).val().replace(/\D/g, '');
        
        if (cep.length === 8) {
            $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, function(data) {
                if (!data.erro) {
                    $('#endereco').val(data.logradouro);
                    $('#bairro').val(data.bairro);
                    $('#cidade').val(data.localidade);
                    $('#estado').val(data.uf);
                    $('#numero').focus();
                } else {
                    showError($('#cep'), 'CEP não encontrado');
                }
            });
        }
    });
    
    // Funções auxiliares
    function showError(element, message) {
        element.addClass('is-invalid');
        const errorDiv = $('<div class="invalid-feedback"></div>').text(message);
        element.siblings('.invalid-feedback').remove();
        element.after(errorDiv);
    }
    
    function hideError(element) {
        element.removeClass('is-invalid');
        element.siblings('.invalid-feedback').remove();
    }
});
```

#### Adicionar no Blade
```blade
@section('scripts')
    @parent
    <script src="{{ asset('js/validacoes-clientes.js') }}?v={{ config('app.version') }}"></script>
@endsection
```

#### Critérios de Aceitação
- ✅ Email validado em tempo real
- ✅ Telefone com DDD verificado
- ✅ CEP busca endereço automaticamente
- ✅ Feedback visual imediato

---

### 2.4 Implementar Paginação
**Objetivo:** Melhorar performance em grandes volumes  
**Prioridade:** 🟡 ALTA  
**Tempo:** 1 hora  
**Complexidade:** Baixa

#### Tarefas
- [ ] Adicionar paginação no controller
- [ ] Exibir links de paginação
- [ ] Manter filtros na paginação
- [ ] Adicionar seletor de itens por página

#### Implementação Controller
```php
// ClienteController.php
public function index(Request $request)
{
    $query = Cliente::query();
    
    if ($request->filled('nome')) {
        $query->where('nome', 'like', '%' . $request->nome . '%');
    }
    
    if ($request->filled('documento')) {
        $query->where('documento', 'like', '%' . $request->documento . '%');
    }
    
    if ($request->filled('ativo')) {
        $query->where('ativo', $request->ativo === 'A' ? 1 : 0);
    }
    
    $perPage = $request->input('per_page', 15);
    $clientes = $query->orderBy('nome')->paginate($perPage)->withQueryString();
    
    return view('clientes', [
        'tela' => 'pesquisa',
        'nome_tela' => 'Clientes',
        'clientes' => $clientes,
        'request' => $request,
        'rotaIncluir' => 'incluir-clientes',
        'rotaAlterar' => 'alterar-clientes',
    ]);
}
```

#### Implementação Blade
```blade
<!-- Após a tabela -->
<div class="row mt-3">
    <div class="col-md-6">
        <p>Exibindo {{ $clientes->firstItem() }} a {{ $clientes->lastItem() }} de {{ $clientes->total() }} registros</p>
    </div>
    <div class="col-md-6 d-flex justify-content-end align-items-center">
        <label class="mr-2 mb-0">Itens por página:</label>
        <select class="form-control form-control-sm" style="width: auto;" onchange="window.location.href='?per_page='+this.value+'&{{ http_build_query($request->except('per_page')) }}'">
            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
            <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
        </select>
    </div>
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $clientes->links() }}
</div>
```

#### Critérios de Aceitação
- ✅ Paginação funcionando
- ✅ Filtros mantidos entre páginas
- ✅ Seletor de itens por página
- ✅ Performance melhorada

---

## 🟢 FASE 3: Funcionalidades Avançadas

### 3.1 Implementar Ordenação de Colunas
**Objetivo:** Permitir ordenar resultados por qualquer coluna  
**Prioridade:** 🟢 MÉDIA  
**Tempo:** 2 horas  
**Complexidade:** Média

#### Tarefas
- [ ] Adicionar parâmetros de ordenação
- [ ] Criar links clicáveis nos headers
- [ ] Indicar direção da ordenação (ASC/DESC)
- [ ] Manter ordenação na paginação

#### Implementação
```blade
<!-- Cabeçalho da tabela com ordenação -->
<thead>
    <tr>
        <th>
            <a href="?sort=id&direction={{ request('sort') == 'id' && request('direction') == 'asc' ? 'desc' : 'asc' }}&{{ http_build_query($request->except(['sort', 'direction'])) }}">
                ID
                @if(request('sort') == 'id')
                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                @else
                    <i class="fas fa-sort"></i>
                @endif
            </a>
        </th>
        <th>
            <a href="?sort=nome&direction={{ request('sort') == 'nome' && request('direction') == 'asc' ? 'desc' : 'asc' }}&{{ http_build_query($request->except(['sort', 'direction'])) }}">
                Nome
                @if(request('sort') == 'nome')
                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                @else
                    <i class="fas fa-sort"></i>
                @endif
            </a>
        </th>
        <!-- Repetir para outras colunas -->
    </tr>
</thead>
```

#### Controller
```php
$sortField = $request->input('sort', 'nome');
$sortDirection = $request->input('direction', 'asc');

$clientes = $query->orderBy($sortField, $sortDirection)
    ->paginate($perPage)
    ->withQueryString();
```

#### Critérios de Aceitação
- ✅ Ordenação por todas as colunas
- ✅ Indicação visual da ordenação
- ✅ Alternância ASC/DESC
- ✅ Mantém outros filtros

---

### 3.2 Adicionar Exportação (Excel/PDF)
**Objetivo:** Permitir exportar lista de clientes  
**Prioridade:** 🟢 MÉDIA  
**Tempo:** 3 horas  
**Complexidade:** Alta

#### Tarefas
- [ ] Instalar Laravel Excel
- [ ] Criar classe de exportação
- [ ] Adicionar botões de exportação
- [ ] Aplicar filtros na exportação

#### Instalação
```bash
composer require maatwebsite/excel
```

#### Implementação - Export Class
```php
// app/Exports/ClientesExport.php
namespace App\Exports;

use App\Models\Cliente;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClientesExport implements FromQuery, WithHeadings, WithMapping
{
    protected $query;
    
    public function __construct($query)
    {
        $this->query = $query;
    }
    
    public function query()
    {
        return $this->query;
    }
    
    public function headings(): array
    {
        return [
            'ID',
            'Nome',
            'CPF',
            'Telefone',
            'Email',
            'Data Nascimento',
            'Endereço',
            'Cidade',
            'Estado',
            'Status',
        ];
    }
    
    public function map($cliente): array
    {
        return [
            $cliente->id,
            $cliente->nome,
            $cliente->documento,
            $cliente->telefone,
            $cliente->email,
            $cliente->data_nascimento ? $cliente->data_nascimento->format('d/m/Y') : '',
            $cliente->endereco_completo,
            $cliente->cidade,
            $cliente->estado,
            $cliente->ativo ? 'Ativo' : 'Inativo',
        ];
    }
}
```

#### Controller
```php
use App\Exports\ClientesExport;
use Maatwebsite\Excel\Facades\Excel;

public function export(Request $request)
{
    $query = $this->buildQuery($request);
    
    return Excel::download(
        new ClientesExport($query),
        'clientes_' . date('Y-m-d_His') . '.xlsx'
    );
}
```

#### Blade
```blade
<div class="form-group row">
    <div class="col-sm-5">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Pesquisar
        </button>
        <a href="{{ route('exportar-clientes', request()->query()) }}" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Exportar Excel
        </a>
    </div>
</div>
```

#### Critérios de Aceitação
- ✅ Exportação Excel funcionando
- ✅ Filtros aplicados na exportação
- ✅ Formatação adequada
- ✅ Nome do arquivo com data/hora

---

### 3.3 Melhorar Responsividade
**Objetivo:** Otimizar para dispositivos móveis  
**Prioridade:** 🟢 MÉDIA  
**Tempo:** 2 horas  
**Complexidade:** Média

#### Tarefas
- [ ] Tornar tabela responsiva
- [ ] Ajustar formulário para mobile
- [ ] Testar em diferentes resoluções
- [ ] Adicionar breakpoints customizados

#### Implementação
```blade
<!-- Tabela responsiva -->
<div class="table-responsive">
    <table class="table table-striped text-center">
        <!-- ... -->
    </table>
</div>

<!-- CSS adicional -->
<style>
    @media (max-width: 768px) {
        .table thead {
            display: none;
        }
        
        .table td {
            display: block;
            text-align: right;
            border: none;
            border-bottom: 1px solid #ddd;
            position: relative;
            padding-left: 50%;
        }
        
        .table td:before {
            content: attr(data-label);
            position: absolute;
            left: 6px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
            text-align: left;
            font-weight: bold;
        }
        
        /* Ajustar formulário */
        .col-md-4, .col-md-8, .col-md-2 {
            margin-bottom: 15px;
        }
    }
</style>

<!-- Adicionar data-label nas células -->
<td data-label="ID"><a href="...">{{$cliente->id}}</a></td>
<td data-label="Nome">{{$cliente->nome}}</td>
<td data-label="Telefone">{{$cliente->telefone}}</td>
```

#### Critérios de Aceitação
- ✅ Tabela legível em mobile
- ✅ Formulário usável em telas pequenas
- ✅ Botões acessíveis no mobile
- ✅ Testado em iOS e Android

---

### 3.4 Implementar Soft Delete
**Objetivo:** Permitir recuperação de registros excluídos  
**Prioridade:** 🟢 BAIXA  
**Tempo:** 2 horas  
**Complexidade:** Média

#### Tarefas
- [ ] Adicionar migration para deleted_at
- [ ] Implementar SoftDeletes no Model
- [ ] Criar tela de lixeira
- [ ] Adicionar botão de restaurar

#### Migration
```php
Schema::table('clientes', function (Blueprint $table) {
    $table->softDeletes();
});
```

#### Model
```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
}
```

#### Controller - Métodos Adicionais
```php
public function destroy($id)
{
    $cliente = Cliente::findOrFail($id);
    $cliente->delete();
    
    return redirect()->route('pesquisar-clientes')
        ->with('success', 'Cliente excluído com sucesso! Pode ser restaurado da lixeira.');
}

public function lixeira()
{
    $clientes = Cliente::onlyTrashed()->paginate(15);
    
    return view('clientes-lixeira', compact('clientes'));
}

public function restaurar($id)
{
    Cliente::withTrashed()->find($id)->restore();
    
    return redirect()->route('pesquisar-clientes')
        ->with('success', 'Cliente restaurado com sucesso!');
}

public function forceDelete($id)
{
    Cliente::withTrashed()->find($id)->forceDelete();
    
    return redirect()->route('lixeira-clientes')
        ->with('success', 'Cliente excluído permanentemente!');
}
```

#### Critérios de Aceitação
- ✅ Exclusão lógica funcionando
- ✅ Tela de lixeira criada
- ✅ Restauração funcionando
- ✅ Exclusão permanente com confirmação

---

## ✅ FASE 4: Testes e Validação

### 4.1 Testes Manuais
**Tempo:** 2 horas

#### Checklist de Testes
- [ ] **CRUD Completo**
  - [ ] Criar cliente com todos os campos
  - [ ] Editar cliente existente
  - [ ] Visualizar detalhes
  - [ ] Excluir cliente
  - [ ] Restaurar cliente excluído

- [ ] **Validações**
  - [ ] CPF válido aceito
  - [ ] CPF inválido rejeitado
  - [ ] Email inválido rejeitado
  - [ ] Campos obrigatórios validados
  - [ ] Data de nascimento futura rejeitada

- [ ] **Pesquisa e Filtros**
  - [ ] Busca por nome (parcial)
  - [ ] Busca por CPF
  - [ ] Filtro por status (Ativo/Inativo)
  - [ ] Combinação de filtros
  - [ ] Filtros mantidos na paginação

- [ ] **Funcionalidades Extras**
  - [ ] Paginação funcionando
  - [ ] Ordenação por colunas
  - [ ] Exportação Excel
  - [ ] Busca automática de CEP
  - [ ] Máscaras aplicadas corretamente

- [ ] **Interface**
  - [ ] Mensagens flash exibidas
  - [ ] Loading durante submissão
  - [ ] Confirmação de cancelamento
  - [ ] Responsivo em mobile
  - [ ] Sem erros no console

---

### 4.2 Testes Automatizados
**Tempo:** 2 horas

#### Feature Tests
```php
// tests/Feature/ClienteTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function pode_listar_clientes()
    {
        Cliente::factory()->count(5)->create();
        
        $response = $this->get(route('pesquisar-clientes'));
        
        $response->assertStatus(200);
        $response->assertViewHas('clientes');
    }
    
    /** @test */
    public function pode_criar_cliente()
    {
        $dados = [
            'nome' => 'João Silva',
            'documento' => '123.456.789-00',
            'telefone' => '11987654321',
            'email' => 'joao@email.com',
            'data_nascimento' => '1990-01-01',
            'ativo' => 1,
        ];
        
        $response = $this->post(route('incluir-clientes'), $dados);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('clientes', ['nome' => 'João Silva']);
    }
    
    /** @test */
    public function nao_pode_criar_cliente_com_cpf_invalido()
    {
        $dados = [
            'nome' => 'João Silva',
            'documento' => '111.111.111-11',
            'email' => 'joao@email.com',
        ];
        
        $response = $this->post(route('incluir-clientes'), $dados);
        
        $response->assertSessionHasErrors(['documento']);
    }
    
    /** @test */
    public function pode_atualizar_cliente()
    {
        $cliente = Cliente::factory()->create();
        
        $response = $this->put(route('alterar-clientes', $cliente->id), [
            'nome' => 'Nome Atualizado',
            'documento' => $cliente->documento,
            'email' => $cliente->email,
        ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('clientes', ['nome' => 'Nome Atualizado']);
    }
    
    /** @test */
    public function pode_excluir_cliente()
    {
        $cliente = Cliente::factory()->create();
        
        $response = $this->delete(route('excluir-clientes', $cliente->id));
        
        $response->assertRedirect();
        $this->assertSoftDeleted('clientes', ['id' => $cliente->id]);
    }
}
```

#### Executar Testes
```bash
php artisan test --filter ClienteTest
```

---

## 📊 Métricas de Acompanhamento

### Dashboard de Progresso

| Fase | Tarefas | Concluídas | % | Status |
|------|---------|------------|---|--------|
| FASE 1 | 4 | 0 | 0% | 🔴 Pendente |
| FASE 2 | 4 | 0 | 0% | 🔴 Pendente |
| FASE 3 | 4 | 0 | 0% | 🔴 Pendente |
| FASE 4 | 2 | 0 | 0% | 🔴 Pendente |
| **TOTAL** | **14** | **0** | **0%** | 🔴 **Não Iniciado** |

### Bugs Resolvidos

| Severidade | Total | Resolvidos | Pendentes |
|------------|-------|------------|-----------|
| 🔴 Críticos | 4 | 0 | 4 |
| 🟡 Médios | 6 | 0 | 6 |
| 🟠 Baixos | 0 | 0 | 0 |
| **TOTAL** | **10** | **0** | **10** |

### Quality Score

| Métrica | Antes | Meta | Atual |
|---------|-------|------|-------|
| Bugs Críticos | 4 | 0 | 4 |
| Cobertura de Testes | 0% | 80% | 0% |
| Performance (s) | 3.5 | 2.0 | 3.5 |
| Score Qualidade | 7.0 | 9.0+ | 7.0 |
| UX Score | 6.5 | 9.0 | 6.5 |

---

## 📅 Cronograma

### Semana 1
- **Seg-Ter:** FASE 1 (Correções Críticas)
- **Qua-Qui:** FASE 2 (Melhorias Importantes) - Parte 1

### Semana 2
- **Seg:** FASE 2 (Melhorias Importantes) - Parte 2
- **Ter-Qui:** FASE 3 (Funcionalidades Avançadas)
- **Sex:** FASE 4 (Testes e Validação)

### Timeline Visual
```
Semana 1:  [====FASE 1====][=====FASE 2=====]
Semana 2:  [===FASE 2===][======FASE 3======][==FASE 4==]
```

---

## 🎯 Critérios de Conclusão

### Definição de Pronto (Definition of Done)

Para considerar o plano concluído, os seguintes critérios devem ser atendidos:

#### Funcional
- [x] Todas as 14 tarefas principais concluídas
- [x] 0 bugs críticos
- [x] CRUD 100% funcional
- [x] Todas as validações implementadas

#### Qualidade
- [x] Cobertura de testes > 80%
- [x] 0 duplicação de código
- [x] Score de qualidade > 9.0
- [x] Performance < 2s

#### Documentação
- [x] Código comentado onde necessário
- [x] README atualizado
- [x] Documentação de API
- [x] Guia de uso para usuários

#### Aprovação
- [x] Code review aprovado
- [x] Testes passando 100%
- [x] Homologação validada
- [x] Deploy em produção

---

## 🚨 Riscos e Mitigações

| Risco | Probabilidade | Impacto | Mitigação |
|-------|---------------|---------|-----------|
| Mudança de requisitos | Média | Alto | Documentar bem e validar com stakeholders |
| Problemas de performance | Baixa | Médio | Testar com dados reais, implementar cache |
| Incompatibilidade de bibliotecas | Baixa | Alto | Testar em ambiente de dev primeiro |
| Falta de tempo | Média | Alto | Priorizar fases 1 e 2, deixar fase 3 opcional |
| Bugs em produção | Baixa | Crítico | Testes extensivos antes do deploy |

---

## 📞 Responsáveis e Comunicação

### Time do Projeto
- **Tech Lead:** A definir
- **Desenvolvedor Backend:** A definir
- **Desenvolvedor Frontend:** A definir
- **QA:** A definir

### Comunicação
- **Daily Stand-up:** 09:00 (15 min)
- **Review Semanal:** Sexta-feira 16:00
- **Canal:** Slack #projeto-clientes
- **Documentação:** Confluence

---

## 📚 Recursos e Referências

### Documentação
- [Laravel Validation](https://laravel.com/docs/validation)
- [AdminLTE](https://adminlte.io/docs)
- [jQuery Mask Plugin](https://igorescobar.github.io/jQuery-Mask-Plugin/)
- [Laravel Excel](https://docs.laravel-excel.com/)

### APIs Externas
- [ViaCEP](https://viacep.com.br/) - Busca de CEP
- [ReceitaWS](https://receitaws.com.br/) - Validação de CPF/CNPJ (opcional)

---

## ✅ Aprovações

| Papel | Nome | Aprovado | Data |
|-------|------|----------|------|
| Product Owner | | ⬜ | |
| Tech Lead | | ⬜ | |
| Desenvolvedor | | ⬜ | |
| QA | | ⬜ | |

---

**Documento criado em:** 21 de novembro de 2025  
**Última atualização:** 21 de novembro de 2025  
**Versão:** 1.0  
**Status:** 🟡 Aguardando Aprovação

---

## 🔄 Histórico de Alterações

| Data | Versão | Autor | Alterações |
|------|--------|-------|------------|
| 21/11/2025 | 1.0 | GitHub Copilot | Criação inicial do documento |

---

**Notas:**
- Este documento é vivo e deve ser atualizado conforme o projeto evolui
- Todas as tarefas devem ser rastreadas no sistema de gerenciamento (Jira/Trello)
- Manter o status atualizado diariamente
- Comunicar imediatamente qualquer bloqueio ou risco identificado
