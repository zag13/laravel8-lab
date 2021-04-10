<?php

namespace App\Models;

use App\Utils\Es\EsTestEs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ScoutElastic\Searchable;

class TestEsModel extends Model
{
    use HasFactory, Searchable;

    protected $table = "test_es";

    protected $indexConfigurator = EsTestEs::class;

    protected $searchRules = [

    ];

    // Here you can specify a mapping for model fields
    protected $mapping = [
        'properties' => [
            'name' => [
                'type' => 'keyword',
                // Also you can configure multi-fields, more details you can find here https://www.elastic.co/guide/en/elasticsearch/reference/current/multi-fields.html
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]
            ],
            'email' => [
                'type' => 'keyword',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]
            ],
            'company' => [
                'type' => 'text',
                'analyzer' => 'ik_smart',
                'search_analyzer' => 'ik_smart',
                'fields' => [
                    'raw' => [
                        'type' => 'text',
                    ]
                ]
            ],
            'address' => [
                'type' => 'text',
                'analyzer' => 'ik_smart',
                'search_analyzer' => 'ik_smart',
                'fields' => [
                    'raw' => [
                        'type' => 'text',
                    ]
                ]
            ],
            'country' => [
                'type' => 'keyword',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]
            ],
            'text' => [
                'type' => 'text',
                'analyzer' => 'ik_smart',
                'search_analyzer' => 'ik_smart',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]
            ],
        ]
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        return $array;
    }

}
