<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Google Drive - Upload de Arquivos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
    <div class="container">
        <h1 class="mb-4 text-center">📁 Upload para Google Drive</h1>

        {{-- Alertas --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Formulário de Upload --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('drive.upload') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-3">
                    @csrf
                    <input type="text" name="pessoa" class="form-control" placeholder="Nome da Pessoa" required>
                    <input type="file" name="file" class="form-control" required>
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </form>
            </div>
        </div>

        {{-- Lista de Arquivos --}}
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                Arquivos no Google Drive
            </div>
            <ul class="list-group list-group-flush">
                @forelse($files as $file)
                    {{-- @php dd($file); @endphp --}}
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ basename($file['name']) }}</span>
                        <div>
                            <a href="{{ route('drive.download.id', ['id' => $file['id']]) }}" target="_blank" class="btn btn-sm btn-success">Baixar</a>
                            <form action="{{ route('drive.delete', $file['id']) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Nenhum arquivo encontrado.</li>
                @endforelse
            </ul>
        </div>
    </div>
</body>
</html>
