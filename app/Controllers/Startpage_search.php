<?php

namespace App\Controllers;

class Startpage_search extends BaseController
{
    public function index(): string
    {
        $data['js']    = ['startpage-search'];
        $data['css']   = [];
        $data['title'] = 'Start Page Searches';

        return view('startpage_search', $data);
    }
}
