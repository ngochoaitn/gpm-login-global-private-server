<?php

namespace App\Services;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_Permission;
use Exception;

class GoogleDriveService
{
    protected $client;
    protected $driveService;
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
        $this->initializeClient();
    }

    /**
     * Initialize Google Drive client
     */
    private function initializeClient()
    {
        try {
            $this->client = new Google_Client();

            // Set the path to your service account key file
            $credentialsPath = storage_path('credentials/google-drive-credentials.json');
            $this->client->setAuthConfig($credentialsPath);

            // Set the required scopes
            $this->client->addScope(Google_Service_Drive::DRIVE_FILE);

            // Initialize the Drive service
            $this->driveService = new Google_Service_Drive($this->client);
        } catch (Exception $e) {
            throw new Exception('Failed to initialize Google Drive client: ' . $e->getMessage());
        }
    }

    /**
     * Upload file to Google Drive
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $fileName
     * @param string $folderId (optional) - Google Drive folder ID, null for root directory
     * @return array
     */
    public function uploadFile($file, string $fileName, string $folderId = null)
    {
        try {
            // Create the file metadata
            // If $folderId is null, file will be uploaded to root directory
            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $fileName,
                'parents' => $folderId ? [$folderId] : null
            ]);

            // Get file content
            $fileContent = file_get_contents($file->getPathname());

            // Upload the file
            $uploadedFile = $this->driveService->files->create($fileMetadata, [
                'data' => $fileContent,
                'mimeType' => $file->getMimeType(),
                'uploadType' => 'multipart'
            ]);

            // Make the file publicly accessible (optional)
            $this->makeFilePublic($uploadedFile->getId());

            // Get the public URL
            $publicUrl = $this->getPublicUrl($uploadedFile->getId());

            return [
                'success' => true,
                'message' => 'ok',
                'data' => [
                    'file_id' => $uploadedFile->getId(),
                    'file_name' => $fileName,
                    'public_url' => $publicUrl,
                    'view_url' => "https://drive.google.com/file/d/{$uploadedFile->getId()}/view",
                    'download_url' => "https://drive.google.com/uc?export=download&id={$uploadedFile->getId()}"
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to upload file to Google Drive',
                'data' => ['error' => $e->getMessage()]
            ];
        }
    }

    /**
     * Delete file from Google Drive
     *
     * @param string $fileId
     * @return array
     */
    public function deleteFile(string $fileId)
    {
        try {
            $this->driveService->files->delete($fileId);

            return [
                'success' => true,
                'message' => 'ok',
                'data' => []
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete file from Google Drive',
                'data' => ['error' => $e->getMessage()]
            ];
        }
    }

    /**
     * Make file publicly accessible
     *
     * @param string $fileId
     * @return void
     */
    private function makeFilePublic(string $fileId)
    {
        try {
            $permission = new \Google_Service_Drive_Permission([
                'type' => 'anyone',
                'role' => 'reader'
            ]);

            $this->driveService->permissions->create($fileId, $permission);
        } catch (Exception $e) {
            // Log the error but don't fail the upload
            \Log::warning('Failed to make Google Drive file public: ' . $e->getMessage());
        }
    }

    /**
     * Get public URL for a file
     *
     * @param string $fileId
     * @return string
     */
    private function getPublicUrl(string $fileId)
    {
        return "https://drive.google.com/uc?export=view&id={$fileId}";
    }

    /**
     * Create a folder in Google Drive
     *
     * @param string $folderName
     * @param string $parentFolderId (optional)
     * @return array
     */
    public function createFolder(string $folderName, string $parentFolderId = null)
    {
        try {
            $folderMetadata = new Google_Service_Drive_DriveFile([
                'name' => $folderName,
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents' => $parentFolderId ? [$parentFolderId] : null
            ]);

            $folder = $this->driveService->files->create($folderMetadata);

            return [
                'success' => true,
                'message' => 'ok',
                'data' => [
                    'folder_id' => $folder->getId(),
                    'folder_name' => $folderName
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create folder',
                'data' => ['error' => $e->getMessage()]
            ];
        }
    }

    /**
     * Upload file to root directory of Google Drive
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $fileName
     * @return array
     */
    public function uploadFileToRoot($file, string $fileName)
    {
        return $this->uploadFile($file, $fileName, null);
    }
    public function getOrCreateProfilesFolder()
    {
        try {
            // Check if we have the folder ID stored in settings
            $folderIdSetting = $this->settingService->getSetting('google_drive_profiles_folder_id');

            if ($folderIdSetting && $folderIdSetting->value) {
                // Verify the folder still exists
                try {
                    $this->driveService->files->get($folderIdSetting->value);
                    return $folderIdSetting->value;
                } catch (Exception $e) {
                    // Folder doesn't exist anymore, create a new one
                }
            }

            // Create new folder
            $result = $this->createFolder('GPM-Profiles');

            if ($result['success']) {
                $folderId = $result['data']['folder_id'];

                // Store the folder ID in settings
                $this->settingService->setSetting('google_drive_profiles_folder_id', $folderId);

                return $folderId;
            }

            return null;
        } catch (Exception $e) {
            \Log::error('Failed to get or create profiles folder: ' . $e->getMessage());
            return null;
        }
    }
}