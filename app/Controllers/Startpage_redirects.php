<?php

namespace App\Controllers;

class Startpage_redirects extends BaseController
{
    public function index(): string
    {
        $data['js']    = ['startpage-redirects'];
        $data['css']   = [];
        $data['title'] = 'Start Page Redirects';

        return view('startpage_redirects', $data);
    }
}
