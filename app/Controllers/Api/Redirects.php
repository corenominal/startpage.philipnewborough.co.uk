<?php

namespace App\Controllers\Api;

class Redirects extends BaseController
{
    public function create()
    {
        if ($check = $this->requireAdmin()) {
            return $check;
        }

        if (! $this->request->is('json')) {
            return $this->response->setJSON(['error' => 'Expecting JSON data.']);
        }

        $data  = $this->request->getJSON(true);
        $model = model('StartRedirectsModel');

        $test = $model->where('phrase', $data['phrase'])->first();

        if (! $test) {
            $model->insert($data);
            return $this->response->setJSON($data);
        }

        return $this->response->setJSON(['error' => 'Redirect already exists.']);
    }

    /**
     * Return a 403 response if the caller is not an admin, or null if allowed.
     */
    private function requireAdmin(): ?\CodeIgniter\HTTP\ResponseInterface
    {
        if (empty($GLOBALS['is_admin'])) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Forbidden.',
            ]);
        }

        return null;
    }
}
