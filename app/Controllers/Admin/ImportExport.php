<?php

namespace App\Controllers\Admin;

use App\Models\StartHistoryModel;
use App\Models\StartRedirectsModel;
use App\Models\StartSearchModel;

class ImportExport extends BaseController
{
    /**
     * Display the Import / Export page.
     *
     * @return string Rendered view output.
     */
    public function index(): string
    {
        $data['js']    = ['admin/import_export'];
        $data['title'] = 'Import / Export';

        return view('admin/import_export', $data);
    }

    /**
     * Export search history as a JSON file download.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function exportHistory()
    {
        $model   = new StartHistoryModel();
        $records = $model->orderBy('id', 'ASC')->findAll();

        $payload = [
            'exported_at' => date('c'),
            'total'       => count($records),
            'history'     => $records,
        ];

        $filename = 'history-' . date('Y-m-d') . '.json';

        return $this->response
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setContentType('application/json')
            ->setBody(json_encode($payload));
    }

    /**
     * Export redirects (including soft-deleted) as a JSON file download.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function exportRedirects()
    {
        $model   = new StartRedirectsModel();
        $records = $model->withDeleted()->orderBy('id', 'ASC')->findAll();

        $payload = [
            'exported_at' => date('c'),
            'total'       => count($records),
            'redirects'   => $records,
        ];

        $filename = 'redirects-' . date('Y-m-d') . '.json';

        return $this->response
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setContentType('application/json')
            ->setBody(json_encode($payload));
    }

    /**
     * Export search engines (including soft-deleted) as a JSON file download.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function exportSearch()
    {
        $model   = new StartSearchModel();
        $records = $model->withDeleted()->orderBy('id', 'ASC')->findAll();

        $payload = [
            'exported_at'    => date('c'),
            'total'          => count($records),
            'search_engines' => $records,
        ];

        $filename = 'search-' . date('Y-m-d') . '.json';

        return $this->response
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setContentType('application/json')
            ->setBody(json_encode($payload));
    }

    /**
     * Import search history from an uploaded JSON file.
     *
     * Upserts records by query term (`q`). Existing records have their
     * count updated; new records are inserted with preserved timestamps.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function importHistory()
    {
        $file = $this->request->getFile('import_file');

        $validationError = $this->validateUpload($file);
        if ($validationError !== null) {
            return $validationError;
        }

        $content = file_get_contents($file->getTempName());
        $data    = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['history']) || !is_array($data['history'])) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON(['status' => 'error', 'message' => 'Invalid JSON format or missing "history" key.']);
        }

        $model    = new StartHistoryModel();
        $db       = $model->db;
        $inserted = 0;
        $updated  = 0;

        foreach ($data['history'] as $item) {
            $q = isset($item['q']) ? trim((string) $item['q']) : '';

            if ($q === '') {
                continue;
            }

            $count     = isset($item['count']) ? max(1, (int) $item['count']) : 1;
            $createdAt = isset($item['created_at']) ? (string) $item['created_at'] : date('Y-m-d H:i:s');
            $updatedAt = isset($item['updated_at']) ? (string) $item['updated_at'] : date('Y-m-d H:i:s');

            $existing = $model->where('q', $q)->first();

            if ($existing) {
                $db->table('start_history')
                    ->where('id', (int) $existing['id'])
                    ->update([
                        'count'      => $count,
                        'updated_at' => $updatedAt,
                    ]);
                $updated++;
            } else {
                $db->table('start_history')->insert([
                    'q'          => $q,
                    'count'      => $count,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]);
                $inserted++;
            }
        }

        return $this->response->setJSON([
            'status'   => 'success',
            'inserted' => $inserted,
            'updated'  => $updated,
        ]);
    }

    /**
     * Import redirects from an uploaded JSON file.
     *
     * Upserts records by phrase. Existing records (including soft-deleted)
     * are updated; new records are inserted with preserved timestamps.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function importRedirects()
    {
        $file = $this->request->getFile('import_file');

        $validationError = $this->validateUpload($file);
        if ($validationError !== null) {
            return $validationError;
        }

        $content = file_get_contents($file->getTempName());
        $data    = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['redirects']) || !is_array($data['redirects'])) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON(['status' => 'error', 'message' => 'Invalid JSON format or missing "redirects" key.']);
        }

        $model    = new StartRedirectsModel();
        $db       = $model->db;
        $inserted = 0;
        $updated  = 0;

        foreach ($data['redirects'] as $item) {
            $phrase = isset($item['phrase']) ? trim((string) $item['phrase']) : '';
            $url    = isset($item['url']) ? trim((string) $item['url']) : '';

            if ($phrase === '' || $url === '') {
                continue;
            }

            $comments  = isset($item['comments']) ? (string) $item['comments'] : '';
            $createdAt = isset($item['created_at']) ? (string) $item['created_at'] : date('Y-m-d H:i:s');
            $updatedAt = isset($item['updated_at']) ? (string) $item['updated_at'] : date('Y-m-d H:i:s');
            $deletedAt = isset($item['deleted_at']) ? $item['deleted_at'] : null;

            $existing = $model->withDeleted()->where('phrase', $phrase)->first();

            if ($existing) {
                $db->table('start_redirects')
                    ->where('id', (int) $existing['id'])
                    ->update([
                        'url'        => $url,
                        'comments'   => $comments,
                        'updated_at' => $updatedAt,
                        'deleted_at' => $deletedAt,
                    ]);
                $updated++;
            } else {
                $db->table('start_redirects')->insert([
                    'phrase'     => $phrase,
                    'url'        => $url,
                    'comments'   => $comments,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                    'deleted_at' => $deletedAt,
                ]);
                $inserted++;
            }
        }

        return $this->response->setJSON([
            'status'   => 'success',
            'inserted' => $inserted,
            'updated'  => $updated,
        ]);
    }

    /**
     * Import search engines from an uploaded JSON file.
     *
     * Upserts records by phrase. Existing records (including soft-deleted)
     * are updated; new records are inserted with preserved timestamps.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function importSearch()
    {
        $file = $this->request->getFile('import_file');

        $validationError = $this->validateUpload($file);
        if ($validationError !== null) {
            return $validationError;
        }

        $content = file_get_contents($file->getTempName());
        $data    = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['search_engines']) || !is_array($data['search_engines'])) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON(['status' => 'error', 'message' => 'Invalid JSON format or missing "search_engines" key.']);
        }

        $model    = new StartSearchModel();
        $db       = $model->db;
        $inserted = 0;
        $updated  = 0;

        foreach ($data['search_engines'] as $item) {
            $phrase = isset($item['phrase']) ? trim((string) $item['phrase']) : '';
            $url    = isset($item['url']) ? trim((string) $item['url']) : '';

            if ($phrase === '' || $url === '') {
                continue;
            }

            $comments  = isset($item['comments']) ? (string) $item['comments'] : '';
            $createdAt = isset($item['created_at']) ? (string) $item['created_at'] : date('Y-m-d H:i:s');
            $updatedAt = isset($item['updated_at']) ? (string) $item['updated_at'] : date('Y-m-d H:i:s');
            $deletedAt = isset($item['deleted_at']) ? $item['deleted_at'] : null;

            $existing = $model->withDeleted()->where('phrase', $phrase)->first();

            if ($existing) {
                $db->table('start_search')
                    ->where('id', (int) $existing['id'])
                    ->update([
                        'url'        => $url,
                        'comments'   => $comments,
                        'updated_at' => $updatedAt,
                        'deleted_at' => $deletedAt,
                    ]);
                $updated++;
            } else {
                $db->table('start_search')->insert([
                    'phrase'     => $phrase,
                    'url'        => $url,
                    'comments'   => $comments,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                    'deleted_at' => $deletedAt,
                ]);
                $inserted++;
            }
        }

        return $this->response->setJSON([
            'status'   => 'success',
            'inserted' => $inserted,
            'updated'  => $updated,
        ]);
    }

    /**
     * Validate an uploaded file.
     *
     * Checks that the file is valid, does not exceed 10 MB, and has a .json
     * extension. Returns a JSON error response on failure, or null on success.
     *
     * @param \CodeIgniter\HTTP\Files\UploadedFile|null $file
     * @return \CodeIgniter\HTTP\ResponseInterface|null
     */
    private function validateUpload($file)
    {
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'No valid file uploaded.']);
        }

        if ($file->getSize() > 10 * 1024 * 1024) {
            return $this->response
                ->setStatusCode(413)
                ->setJSON(['status' => 'error', 'message' => 'File too large. Maximum size is 10 MB.']);
        }

        if (strtolower($file->getExtension()) !== 'json') {
            return $this->response
                ->setStatusCode(415)
                ->setJSON(['status' => 'error', 'message' => 'Invalid file type. Only .json files are accepted.']);
        }

        return null;
    }
}
