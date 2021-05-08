<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/3
 * Time: 12:02 上午
 */


namespace App\Http\Controllers\Test;


use App\Http\Controllers\Core\Controller;
use App\Models\TestEs;
use App\Utils\Es\MySearchRule;
use Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;

class ESController extends Controller
{
    public function elasticsearch()
    {
        $client = ClientBuilder::create()
            ->setHosts(config('database.connections.elasticsearch.hosts'))
            ->build();

        $params = [
            'index' => 'my_index',
            'type' => 'my_type',
            'id' => 'my_id',
            'body' => ['testField' => 'abc']
        ];

        $response = $client->index($params);

        $params = [
            'index' => 'my_index',
            'type' => 'my_type',
            'id' => 'my_id'
        ];

        //$response = $client->get($params);

        $params = [
            'index' => 'my_index',
            'type' => 'my_type',
            'body' => [
                'query' => [
                    'match' => [
                        'testField' => 'abc'
                    ]
                ]
            ]
        ];

        //$response = $client->search($params);

        $params = [
            'index' => 'my_index',
            'type' => 'my_type',
            'id' => 'my_id'
        ];

        //$response = $client->delete($params);

        /*$deleteParams = [
            'index' => 'my_index'
        ];
        $response = $client->indices()->delete($deleteParams);*/

        // 索引相关
        //$client->indices();
        // 集群相关
        //$client->cluster();

        dd($response);
    }

    public function search(Request $request)
    {
        $q = $request->get('q');
        $paginator = [];
        if ($q) $paginator = TestEs::search($q)
            ->rule(MySearchRule::class)
            ->paginate(5);

        // dd($paginator);
        return view('search', compact('paginator', 'q'));
    }

    public function faker()
    {
        TestEs::factory(90)->create();
    }

}
