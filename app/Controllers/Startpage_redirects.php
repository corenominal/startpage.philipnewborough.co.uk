<?php

namespace App\Controllers;

use Hermawan\DataTables\DataTable;
use App\Models\StartRedirectsModel;

class Startpage_redirects extends BaseController
{
    public function index(): string
    {
        $data['datatables'] = true;
        $data['js']         = ['startpage-redirects'];
        $data['css']        = [];
        $data['title']      = 'Start Page Redirects';

        return view('startpage_redirects', $data);
    }

    public function datatable()
    {
        $model   = new StartRedirectsModel();
        $builder = $model->builder()->where('deleted_at IS NULL');

        return DataTable::of($builder)
            ->add('actions', function ($row) {
                $phrase   = esc($row->phrase, 'attr');
                $url      = esc($row->url, 'attr');
                $comments = esc($row->comments ?? '', 'attr');
                return '<div class="btn-group btn-group-sm" role="group">'
                    . '<button class="btn btn-outline-primary btn-edit" '
                    . 'data-id="' . $row->id . '" '
                    . 'data-phrase="' . $phrase . '" '
                    . 'data-url="' . $url . '" '
                    . 'data-comments="' . $comments . '" '
                    . 'aria-label="Edit"><i class="bi bi-pencil-fill"></i></button>'
                    . '<button class="btn btn-outline-danger btn-delete-row" '
                    . 'data-id="' . $row->id . '" '
                    . 'aria-label="Delete"><i class="bi bi-trash-fill"></i></button>'
                    . '</div>';
            })
            ->toJson(true);
    }

    public function add()
    {
        $json = $this->request->getJSON(true);

        $phrase   = trim($json['phrase'] ?? '');
        $url      = trim($json['url'] ?? '');
        $comments = trim($json['comments'] ?? '');

        if (empty($phrase) || empty($url)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Phrase and URL are required.',
            ]);
        }

        $model = new StartRedirectsModel();

        if ($model->where('phrase', $phrase)->first()) {
            return $this->response->setStatusCode(409)->setJSON([
                'status'  => 'error',
                'message' => 'A redirect with that phrase already exists.',
            ]);
        }

        $model->insert([
            'phrase'   => $phrase,
            'url'      => $url,
            'comments' => $comments,
        ]);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function edit()
    {
        $json = $this->request->getJSON(true);

        $id       = (int) ($json['id'] ?? 0);
        $phrase   = trim($json['phrase'] ?? '');
        $url      = trim($json['url'] ?? '');
        $comments = trim($json['comments'] ?? '');

        if ($id <= 0 || empty($phrase) || empty($url)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'ID, phrase, and URL are required.',
            ]);
        }

        $model = new StartRedirectsModel();
        $model->update($id, [
            'phrase'   => $phrase,
            'url'      => $url,
            'comments' => $comments,
        ]);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function delete()
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

        $model = new StartRedirectsModel();
        $model->whereIn('id', $ids)->delete();

        return $this->response->setJSON([
            'status'  => 'success',
            'deleted' => count($ids),
        ]);
    }
}
