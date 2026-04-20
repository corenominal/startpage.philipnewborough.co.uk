<?php

namespace App\Controllers;

class Startpage_history extends BaseController
{
    public function index(): string
    {
        $data['history']    = model('StartHistoryModel')->orderBy('updated_at', 'DESC')->findAll();
        $data['datatables'] = true;
        $data['js']         = ['startpage-history'];
        $data['css']        = [];
        $data['title']      = 'Start Page History';

        return view('startpage_history', $data);
    }

    public function delete(): \CodeIgniter\HTTP\ResponseInterface
    {
        $json = $this->request->getJSON(true);
        $ids  = $json['ids'] ?? [];

        $ids = array_values(array_filter(array_map('intval', $ids), fn($id) => $id > 0));

        if (empty($ids)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'No valid IDs provided.',
            ]);
        }

        $model = model('StartHistoryModel');
        $model->whereIn('id', $ids)->delete();

        return $this->response->setJSON([
            'status'  => 'success',
            'deleted' => count($ids),
        ]);
    }
}
