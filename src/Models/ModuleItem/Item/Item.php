<?php

namespace Projects\WellmedBackbone\Models\ModuleItem\Item;

use Hanafalah\ModuleItem\Models\Item as ModelsItem;

class Item extends ModelsItem
{
    protected $casts = [
        'name' => 'string',
        'selling_price' => 'int',
        'reference_type' => 'string',
        'inventory_reference_type' => 'string',
        'medical_item_reference_type' => 'string',
    ];

    public function getPropsQuery(): array{
        return [
            'inventory_reference_type'     => 'props->prop_reference->reference_type',
            'medical_item_reference_type'  => 'props->prop_reference->reference_type',
        ];
    }
    public function coa(){return $this->belongsToModel('Coa');}
}
