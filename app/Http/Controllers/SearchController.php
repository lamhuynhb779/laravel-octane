<?php

namespace App\Http\Controllers;

use App\Services\ElasticSearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    //
    public function search(Request $request, ElasticSearchService $service)
    {
        $query = $request->get("query");

        $response = $service->getClient()->search([
            'index' => 'posts',
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $query,
                        'fields' => ['title', 'content'],
                    ],
                ],
                'highlight' => [
                    'fields' => [
                        'title' => new \stdClass(),
                        'content' => new \stdClass(),
                    ]
                ],
            ],
        ]);

        return response()->json($response->asArray());
    }
}
