<?php

namespace App\Controllers\Admin;

use App\Models\StartShortcutCategoryModel;
use App\Models\StartShortcutModel;

class Shortcuts extends BaseController
{
    private const ICON_UPLOAD_PATH = FCPATH . 'icons/';
    private const ICON_ALLOWED_TYPES = ['image/png', 'image/jpeg', 'image/gif', 'image/webp', 'image/svg+xml', 'image/x-icon', 'image/vnd.microsoft.icon'];
    private const ICON_MAX_SIZE = 512; // KB

    public function index(): string
    {
        $categoryModel = new StartShortcutCategoryModel();
        $shortcutModel = new StartShortcutModel();

        $categories = $categoryModel->orderBy('sort_order', 'ASC')->findAll();

        foreach ($categories as &$cat) {
            $cat['shortcuts'] = $shortcutModel
                ->where('category_id', $cat['id'])
                ->orderBy('sort_order', 'ASC')
                ->findAll();
        }
        unset($cat);

        $data['categories'] = $categories;
        $data['js']         = ['admin/shortcuts'];
        $data['css']        = [];
        $data['title']      = 'Shortcuts';

        return view('admin/shortcuts', $data);
    }

    // ── Categories ─────────────────────────────────────────────────────────────

    public function categoryAdd()
    {
        $json = $this->request->getJSON(true);
        $name = trim($json['name'] ?? '');

        if ($name === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Category name is required.',
            ]);
        }

        $model = new StartShortcutCategoryModel();

        $maxOrder = $model->selectMax('sort_order')->first();
        $sortOrder = ($maxOrder['sort_order'] ?? 0) + 1;

        $id = $model->insert(['name' => $name, 'sort_order' => $sortOrder]);

        return $this->response->setJSON([
            'status' => 'success',
            'id'     => $id,
            'name'   => esc($name),
        ]);
    }

    public function categoryEdit()
    {
        $json = $this->request->getJSON(true);
        $id   = (int) ($json['id'] ?? 0);
        $name = trim($json['name'] ?? '');

        if ($id <= 0 || $name === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Valid ID and name are required.',
            ]);
        }

        $model = new StartShortcutCategoryModel();

        if ($model->find($id) === null) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Category not found.',
            ]);
        }

        $model->update($id, ['name' => $name]);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function categoryDelete()
    {
        $json = $this->request->getJSON(true);
        $id   = (int) ($json['id'] ?? 0);

        if ($id <= 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Valid ID is required.',
            ]);
        }

        $categoryModel = new StartShortcutCategoryModel();
        $shortcutModel = new StartShortcutModel();

        if ($categoryModel->find($id) === null) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Category not found.',
            ]);
        }

        // Delete shortcuts in this category first (soft delete)
        $shortcuts = $shortcutModel->where('category_id', $id)->findAll();
        foreach ($shortcuts as $shortcut) {
            $this->deleteIconFile($shortcut['icon_filename']);
            $shortcutModel->delete($shortcut['id']);
        }

        $categoryModel->delete($id);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function categoryReorder()
    {
        $json       = $this->request->getJSON(true);
        $orderedIds = $json['ids'] ?? [];

        if (! is_array($orderedIds) || empty($orderedIds)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'ids array is required.',
            ]);
        }

        $orderedIds = array_values(array_filter(array_map('intval', $orderedIds), fn($id) => $id > 0));

        $model = new StartShortcutCategoryModel();
        foreach ($orderedIds as $position => $id) {
            $model->update($id, ['sort_order' => $position + 1]);
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    // ── Shortcuts ──────────────────────────────────────────────────────────────

    public function shortcutAdd()
    {
        $categoryId = (int) $this->request->getPost('category_id');
        $name       = trim($this->request->getPost('name') ?? '');
        $url        = trim($this->request->getPost('url') ?? '');

        if ($categoryId <= 0 || $name === '' || $url === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Category, name, and URL are required.',
            ]);
        }

        $categoryModel = new StartShortcutCategoryModel();
        if ($categoryModel->find($categoryId) === null) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Category not found.',
            ]);
        }

        $iconFilename = $this->handleIconUpload();
        if (is_array($iconFilename)) {
            // It's an error response
            return $this->response->setStatusCode(400)->setJSON($iconFilename);
        }

        $model = new StartShortcutModel();

        $maxOrder  = $model->selectMax('sort_order')->where('category_id', $categoryId)->first();
        $sortOrder = ($maxOrder['sort_order'] ?? 0) + 1;

        $id = $model->insert([
            'category_id'   => $categoryId,
            'name'          => $name,
            'url'           => $url,
            'icon_filename' => $iconFilename ?? '',
            'sort_order'    => $sortOrder,
        ]);

        return $this->response->setJSON([
            'status'        => 'success',
            'id'            => $id,
            'name'          => esc($name),
            'url'           => esc($url),
            'icon_filename' => esc($iconFilename ?? ''),
        ]);
    }

    public function shortcutEdit()
    {
        $id         = (int) $this->request->getPost('id');
        $categoryId = (int) $this->request->getPost('category_id');
        $name       = trim($this->request->getPost('name') ?? '');
        $url        = trim($this->request->getPost('url') ?? '');

        if ($id <= 0 || $categoryId <= 0 || $name === '' || $url === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'ID, category, name, and URL are required.',
            ]);
        }

        $model    = new StartShortcutModel();
        $existing = $model->find($id);

        if ($existing === null) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Shortcut not found.',
            ]);
        }

        $categoryModel = new StartShortcutCategoryModel();
        if ($categoryModel->find($categoryId) === null) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Category not found.',
            ]);
        }

        $iconFilename = $existing['icon_filename'];

        $uploadedFile = $this->request->getFile('icon');
        if ($uploadedFile !== null && $uploadedFile->isValid() && ! $uploadedFile->hasMoved()) {
            $newIcon = $this->handleIconUpload();
            if (is_array($newIcon)) {
                return $this->response->setStatusCode(400)->setJSON($newIcon);
            }
            // Delete old icon if a new one was uploaded
            if ($newIcon !== null) {
                $this->deleteIconFile($existing['icon_filename']);
                $iconFilename = $newIcon;
            }
        }

        $model->update($id, [
            'category_id'   => $categoryId,
            'name'          => $name,
            'url'           => $url,
            'icon_filename' => $iconFilename,
        ]);

        return $this->response->setJSON([
            'status'        => 'success',
            'icon_filename' => esc($iconFilename),
        ]);
    }

    public function shortcutDelete()
    {
        $json = $this->request->getJSON(true);
        $id   = (int) ($json['id'] ?? 0);

        if ($id <= 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Valid ID is required.',
            ]);
        }

        $model    = new StartShortcutModel();
        $existing = $model->find($id);

        if ($existing === null) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Shortcut not found.',
            ]);
        }

        $this->deleteIconFile($existing['icon_filename']);
        $model->delete($id);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function shortcutReorder()
    {
        $json       = $this->request->getJSON(true);
        $categoryId = (int) ($json['category_id'] ?? 0);
        $orderedIds = $json['ids'] ?? [];

        if ($categoryId <= 0 || ! is_array($orderedIds) || empty($orderedIds)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'category_id and ids array are required.',
            ]);
        }

        $orderedIds = array_values(array_filter(array_map('intval', $orderedIds), fn($id) => $id > 0));

        $model = new StartShortcutModel();
        foreach ($orderedIds as $position => $id) {
            $model->where('category_id', $categoryId)->update($id, ['sort_order' => $position + 1]);
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function handleIconUpload(): string|array|null
    {
        $file = $this->request->getFile('icon');

        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        if ($file->getMimeType() !== null && ! in_array($file->getMimeType(), self::ICON_ALLOWED_TYPES, true)) {
            return [
                'status'  => 'error',
                'message' => 'Invalid file type. Allowed: PNG, JPEG, GIF, WebP, SVG, ICO.',
            ];
        }

        if ($file->getSize() > self::ICON_MAX_SIZE * 1024) {
            return [
                'status'  => 'error',
                'message' => 'Icon file must be under ' . self::ICON_MAX_SIZE . 'KB.',
            ];
        }

        $newName = $file->getRandomName();
        $file->move(self::ICON_UPLOAD_PATH, $newName);

        return $newName;
    }

    private function deleteIconFile(string $filename): void
    {
        if ($filename === '') {
            return;
        }

        $path = self::ICON_UPLOAD_PATH . $filename;
        if (is_file($path)) {
            unlink($path);
        }
    }
}
