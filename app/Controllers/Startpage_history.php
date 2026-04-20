<?php

namespace App\Controllers;

class Startpage_history extends BaseController
{
    public function index(): string
    {
        $data['js']    = ['startpage-history'];
        $data['css']   = [];
        $data['title'] = 'Start Page History';

        return view('startpage_history', $data);
    }
}
