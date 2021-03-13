<?php

namespace App\Services\Es;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class EsTestEs extends IndexConfigurator
{
    use Migratable;

    // It's not obligatory to determine name. By default it'll be a snaked class name without `IndexConfigurator` part.
    protected $name = 'test_es';

    // You can specify any settings you want, for example, analyzers.
    protected $settings = [
        'analysis' => [
            'analyzer' => [
                'es_std' => [
                    'type' => 'ik_smart'
                ]
            ]
        ]
    ];

}
