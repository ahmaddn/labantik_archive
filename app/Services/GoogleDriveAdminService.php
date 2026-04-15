<?php

namespace App\Services;

use App\Models\GoogleToken;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Http\UploadedFile;

class GoogleDriveAdminService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect'));
        $this->client->setScopes([Drive::DRIVE_FILE]);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent select_account');
    }

    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    public function handleCallback(string $code): array
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            throw new \Exception('OAuth Error: ' . $token['error_description']);
        }

        return $token;
    }

    protected function getAuthorizedClient(): Client
    {
        $tokenRecord = GoogleToken::where('type', 'admin')->latest()->first();

        if (!$tokenRecord) {
            throw new \Exception('Admin belum connect Google Drive. Silakan hubungi Super Admin.');
        }

        $tokenArray = [
            'access_token'  => $tokenRecord->access_token,
            'refresh_token' => $tokenRecord->refresh_token,
            'expires_in'    => $tokenRecord->expires_in,
            'created'       => $tokenRecord->token_created_at->timestamp,
        ];

        $this->client->setAccessToken($tokenArray);

        // Auto-refresh jika expired
        if ($this->client->isAccessTokenExpired()) {
            $newToken = $this->client->fetchAccessTokenWithRefreshToken(
                $tokenRecord->refresh_token
            );

            if (isset($newToken['error'])) {
                throw new \Exception('Gagal refresh token: ' . $newToken['error_description']);
            }

            $tokenRecord->update([
                'access_token'    => $newToken['access_token'],
                'expires_in'      => $newToken['expires_in'],
                'token_created_at' => now(),
            ]);

            $this->client->setAccessToken($newToken);
        }

        return $this->client;
    }

    public function uploadFile(UploadedFile $file, ?string $customFileName = null, ?string $parentFolderId = null): array
    {
        $client  = $this->getAuthorizedClient();
        $service = new Drive($client);

        $folderId = $parentFolderId ?? config('services.google.drive_folder_id');

        $driveFile = new DriveFile();

        // ✅ Gunakan custom filename jika ada, kalau tidak pakai original
        $fileName = $customFileName ?? $file->getClientOriginalName();
        $driveFile->setName($fileName);
        $driveFile->setParents([$folderId]);

        $result = $service->files->create($driveFile, [
            'data'       => file_get_contents($file->getRealPath()),
            'mimeType'   => $file->getMimeType(),
            'uploadType' => 'multipart',
            'fields'     => 'id, name, mimeType, size, webViewLink, webContentLink',
        ]);

        return [
            'google_file_id'    => $result->getId(),
            'name'              => $result->getName(),
            'mime_type'         => $result->getMimeType(),
            'size'              => $result->getSize() ?? $file->getSize(),
            'web_view_link'     => $result->getWebViewLink(),
            'web_content_link'  => $result->getWebContentLink(),
        ];
    }

    public function ensureYearFolder(string $year): string
    {
        $client  = $this->getAuthorizedClient();
        $service = new Drive($client);

        $parentId = config('services.google.drive_folder_id');

        // Query for existing folder with this name under parent
        $q = sprintf("mimeType='application/vnd.google-apps.folder' and name='%s' and '%s' in parents and trashed=false", addslashes($year), addslashes($parentId));

        $response = $service->files->listFiles([
            'q' => $q,
            'fields' => 'files(id, name)',
            'spaces' => 'drive',
        ]);

        $files = $response->getFiles();
        if (!empty($files) && count($files) > 0) {
            return $files[0]->getId();
        }

        // Create the folder
        $folder = new DriveFile();
        $folder->setName($year);
        $folder->setMimeType('application/vnd.google-apps.folder');
        if ($parentId) {
            $folder->setParents([$parentId]);
        }

        $created = $service->files->create($folder, [
            'fields' => 'id, name',
        ]);

        return $created->getId();
    }


    public function deleteFile(string $googleFileId): void
    {
        $client  = $this->getAuthorizedClient();
        $service = new Drive($client);

        $service->files->delete($googleFileId);
    }
}
