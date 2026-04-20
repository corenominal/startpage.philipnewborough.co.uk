<?php

namespace App\Controllers;

class Startpage extends BaseController
{
    public function index(): mixed
    {
        $historyModel   = model('StartHistoryModel');
        $redirectsModel = model('StartRedirectsModel');
        $searchModel    = model('StartSearchModel');

        // Handle query string (e.g. from browser address bar via OpenSearch)
        if (null !== $this->request->getGet('q')) {
            $q = trim((string) $this->request->getGet('q'));

            if ($q !== '') {
                // Record or update history
                $existing = $historyModel->where('q', $q)->first();
                if ($existing) {
                    $historyModel->update($existing['id'], ['count' => $existing['count'] + 1]);
                } else {
                    $historyModel->insert(['q' => $q, 'count' => 1]);
                }

                // Check for exact redirect match
                $redirect = $redirectsModel->where('phrase', $q)->first();
                if ($redirect) {
                    return redirect()->to($redirect['url']);
                }

                // Check for search engine phrase prefix match (e.g. "g foo bar")
                $searchEngines = $searchModel->findAll();
                foreach ($searchEngines as $engine) {
                    $prefix = $engine['phrase'] . ' ';
                    if (strncmp($q, $prefix, strlen($prefix)) === 0) {
                        $term = substr($q, strlen($prefix));
                        $url  = str_replace('%s', urlencode($term), $engine['url']);
                        return redirect()->to($url);
                    }
                }

                // Fallback: Google search
                return redirect()->to('https://www.google.com/search?q=' . urlencode($q));
            }
        }

        $data['history']        = $historyModel->orderBy('updated_at', 'DESC')->findAll(50);
        $data['redirects']      = $redirectsModel->findAll();
        $data['search_engines'] = $searchModel->findAll();

        $data['datatables'] = true;
        $data['js']    = ['startpage'];
        $data['css']   = [];
        $data['title'] = 'Start Page';

        return view('startpage', $data);
    }

    public function opensearch(): string
    {
        $this->response->setHeader('Content-type', 'text/xml');
        return view('xml/opensearch');
    }
}
