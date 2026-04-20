<?php

namespace App\Controllers\Admin;

use App\Models\StartShortcutModel;
use App\Models\StartShortcutCategoryModel;
use App\Models\StartSearchModel;
use App\Models\StartRedirectsModel;
use App\Models\StartHistoryModel;

class Home extends BaseController
{
    /**
     * Display the Admin Dashboard page.
     *
     * @return string Rendered admin dashboard view output.
     */
    public function index()
    {
        $shortcuts  = new StartShortcutModel();
        $categories = new StartShortcutCategoryModel();
        $search     = new StartSearchModel();
        $redirects  = new StartRedirectsModel();
        $history    = new StartHistoryModel();

        $data['stats'] = [
            'shortcuts'  => $shortcuts->countAllResults(),
            'categories' => $categories->countAllResults(),
            'search'     => $search->countAllResults(),
            'redirects'  => $redirects->countAllResults(),
            'history'    => $history->countAllResults(),
        ];

        $data['top_searches'] = $history->orderBy('count', 'DESC')
            ->limit(10)
            ->findAll();

        $data['js']    = ['admin/home'];
        $data['css']   = ['admin/home'];
        $data['title'] = 'Admin Dashboard';

        return view('admin/home', $data);
    }
}
