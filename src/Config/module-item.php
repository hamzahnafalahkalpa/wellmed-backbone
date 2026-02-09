<?php

use Projects\WellmedBackbone\Imports\Item;

return [
    'item_reference_types' => [
        'medicine' => [
            'schema' => 'Medicine'
        ],
        'healthcare_equipment' => [
            'schema' => 'HealthcareEquipment'
        ],
        'medic_tool' => [
            'schema' => 'MedicTool'
        ],
        'reagent' => [
            'schema' => 'Reagent'
        ]
    ],
    'imports' => [
        'Item' => Item::class
    ],
    'inventory_types' => [
        'healthcare_equipment' => [
            'schema' => 'HealthcareEquipment'
        ]
    ]
];