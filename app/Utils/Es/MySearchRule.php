<?php

namespace App\Utils\Es;

use ScoutElastic\SearchRule;

class MySearchRule extends SearchRule
{
    /**
     * @inheritdoc
     */
    public function buildHighlightPayload()
    {
        return [
            'fields' => [
                'name' => [
                    'type' => 'plain'
                ],
                'address' => [
                    'type' => 'plain'
                ],
                'text' => [
                    'type' => 'plain'
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function buildQueryPayload()
    {
        return [
            'should' => [
                [
                    'match' => [
                        'name' => $this->builder->query
                    ]
                ],
                [
                    'match' => [
                        'address' => $this->builder->query
                    ]
                ],
                [
                    'match' => [
                        'text' => $this->builder->query,
                    ]
                ],
            ],
            'minimum_should_match' => 1
        ];
    }
}
