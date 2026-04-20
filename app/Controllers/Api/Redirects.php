<?php

namespace App\Controllers\Api;

class Redirects extends BaseController
{
    public function create()
    {
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
}
