<?php

namespace Projects\WellmedBackbone\Models\ModuleDisease;

use Hanafalah\MicroTenant\Concerns\Models\HasTenantValidation;
use Hanafalah\ModuleDisease\Models\Disease as ModuleDiseaseDisease;
use Illuminate\Support\Str;

class Disease extends ModuleDiseaseDisease{
    use HasTenantValidation;

    /**
     * Elasticsearch configuration
     *
     * @var array
     */
    protected array $elastic_config = [
        'enabled' => true,
        'index_name' => 'disease',
        'variables' => [
            'id',
            'name',
            'local_name',
            'code',
            'flag'
        ],
        'hydrate' => false,
    ];

    /**
     * Get static index name without tenant prefix
     * Disease data is shared across all tenants
     *
     * @return string
     */
    public function getStaticIndexName(): string
    {
        // $prefix = config('elasticsearch.prefix', '');
        $separator = config('elasticsearch.separator', '.');
        $indexName = config('app.env').$separator.$this->elastic_config['index_name'];

        return $prefix ? $prefix . $separator . $indexName : $indexName;
    }

    public function whenTenantCreation(){
        if (!Str::contains($this->flag, 'Icd')) {
            if (!isset($this->tenant_id)) $this->tenant_id = \tenancy()->tenant->getKey();
        }
    }
}
