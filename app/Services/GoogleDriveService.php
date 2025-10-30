<?php

namespace App\Services;

// use Google\Client;
use Google\Client as GoogleClientLib;
use Google\Service\Drive as GoogleDrive;
use App\Services\GoogleClient;
use Google\Service\Drive;
use Illuminate\Support\Facades\Storage;

class GoogleDriveService
{
    protected $client;
    protected $service;

    protected $folderId = '1oEnFTfnE2_VlYkilI4ULLzny8rX9Y3sh'; // ID da pasta no Google Drive


    public function __construct()
    {
        $client = new \Google\Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        // opcional, mas recomendado para consistência
        $client->setRedirectUri(route('google.callback'));
        $client->setAccessType('offline');

        // caminho do token (storage/app/google_tokens.json)
        $tokenPath = 'google_tokens.json';

        if (!Storage::exists($tokenPath)) {
            throw new \Exception('Arquivo google_tokens.json não encontrado em storage/app. Faça a autenticação inicial.');
        }

        $token = json_decode(Storage::get($tokenPath), true);

        if (empty($token) || !is_array($token)) {
            throw new \Exception('Conteúdo inválido no google_tokens.json.');
        }

        // define o token atual no client
        $client->setAccessToken($token);

        // se expirado, tenta renovar com refresh_token
        if ($client->isAccessTokenExpired()) {
            $refreshToken = $token['refresh_token'] ?? $client->getRefreshToken() ?? null;

            if (empty($refreshToken)) {
                throw new \Exception('Refresh token ausente no arquivo google_tokens.json. É necessário autorizar novamente.');
            }

            // Tenta renovar e captura resposta
            $newToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);

            if (isset($newToken['error'])) {
                // loga pra debug e lança exceção
                info('Erro ao renovar token Google', ['error' => $newToken]);
                throw new \Exception('Falha ao renovar token: ' . ($newToken['error_description'] ?? $newToken['error']));
            }

            // merge para garantir que o refresh_token não se perca
            $merged = array_merge($token, $newToken);
            if (!isset($merged['refresh_token'])) {
                $merged['refresh_token'] = $refreshToken;
            }

            // salva o token atualizado
            Storage::put($tokenPath, json_encode($merged));

            // aplica o token atualizado ao client (muito importante)
            $client->setAccessToken($merged);
        }

        // Se quiser usar o client fora do construtor, salve em propriedade
        $this->client = $client;
    }


    public function listFiles($query = null)
    {

        $params = [
            'pageSize' => 50,
            'fields' => 'nextPageToken, files(id, name, mimeType, size)',
            'q' => sprintf("'%s' in parents and trashed = false", $this->folderId),
        ];

        if ($query) {
            $params['q'] .= sprintf(" and name contains '%s'", addslashes($query));
        }

        $results = $this->service->files->listFiles($params);

        $files = collect($results->getFiles())->map(function ($file) {
            return [
                'id' => $file->getId(),
                'name' => $file->getName(),
                'mimeType' => $file->getMimeType(),
                'size' => $file->getSize(),
            ];
        });

        // info(['query' => $params['q'], 'files' => $files]);

        return $files;
    }


    /**
     * Enviar arquivo para o Drive
     */
    public function uploadFile($filePath, $fileName = null, $mimeType = null)
    {
        $fileMetadata = new Drive\DriveFile([
            'name' => $fileName ?? basename($filePath),
            'parents' => [$this->folderId],
        ]);

        if ($this->folderId) {
            $fileMetadata->setParents([$this->folderId]);
        }

        $content = file_get_contents($filePath);

        $file = $this->service->files->create(
            $fileMetadata,
            [
                'data' => $content,
                'mimeType' => $mimeType ?? mime_content_type($filePath),
                'uploadType' => 'multipart',
                'fields' => 'id, name'
            ]
        );

        return $file;
    }

    public function downloadFile($id)
    {
        try {
            $file = $this->service->files->get($id, ['fields' => 'mimeType, name']);
            $mimeType = $file->getMimeType();
            $name = $file->getName();

            $googleDocsTypes = [
                'application/vnd.google-apps.document' => 'application/pdf',
                'application/vnd.google-apps.spreadsheet' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.google-apps.presentation' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            ];

            if (isset($googleDocsTypes[$mimeType])) {
                $response = $this->service->files->export($id, $googleDocsTypes[$mimeType], ['alt' => 'media']);
            } else {
                $response = $this->service->files->get($id, ['alt' => 'media']);
            }

            $content = $response->getBody()->getContents();

            return response($content, 200)
                ->header('Content-Type', 'application/octet-stream')
                ->header('Content-Disposition', 'attachment; filename="' . $name . '"');
        } catch (\Exception $e) {
            info('Erro ao baixar arquivo: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao baixar arquivo.'], 500);
        }
    }

    /**
     * Criar pasta no Drive
     */
    public function createFolder($name, $parentId = null)
    {
        $folderMetadata = new \Google\Service\Drive\DriveFile([
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
        ]);

        // Se quiser criar dentro de uma pasta específica
        if ($parentId) {
            $folderMetadata->setParents([$parentId]);
        }

        $folder = $this->service->files->create($folderMetadata, [
            'fields' => 'id, name, parents'
        ]);

        return $folder;
    }


    /**
     *  Deletar arquivo
     */
    public function deleteFile($fileId)
    {
        return $this->service->files->delete($fileId);
    }
}
