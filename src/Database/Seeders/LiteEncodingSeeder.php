<?php

namespace Projects\WellmedBackbone\Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Middlewares\EncodingWrapper;
use Hanafalah\LaravelSupport\Concerns\Support\HasRequest;
use Hanafalah\ModuleEmployee\Data\EmployeeData;
use Illuminate\Database\Seeder;
use Hanafalah\LaravelSupport\Jobs\JobRequest;
use Illuminate\Support\Str;

class LiteEncodingSeeder extends Seeder
{
    use HasRequest;

    protected $dummy_encodings = [
        'BILLING'=> '{"separator":{"distance":1,"separator":"\/"},"structure":[{"type":"alphanumeric","value":"BILL","length":4,"resetable":null,"format":null},{"type":"date","value":null,"length":null,"resetable":true,"format":"YYYYMMDD"},{"type":"incrementing","value":null,"length":4,"resetable":null,"format":null}]}', 
        'TRANSACTION'=> '{"separator":{"distance":1,"separator":"-"},"structure":[{"type":"alphanumeric","value":"TRX","length":3,"resetable":null,"format":null},{"type":"date","value":null,"length":null,"resetable":true,"format":"YYMMDD"},{"type":"incrementing","value":null,"length":4,"resetable":null,"format":null}]}', 
        'TREATMENT'=> '{"separator":{"distance":1,"separator":"-"},"structure":[{"type":"alphanumeric","value":"TR","length":2,"resetable":null,"format":null},{"type":"incrementing","value":null,"length":5,"resetable":null,"format":null}]}', 
        'MEDICAL_TREATMENT'=> '{"separator":{"distance":1,"separator":null},"structure":[{"type":"alphanumeric","value":"TP","length":2,"resetable":null,"format":null},{"type":"incrementing","value":null,"length":5,"resetable":null,"format":null}]}', 
        'VISIT_PATIENT'=> '{"separator":{"distance":1,"separator":"\/"},"structure":[{"type":"alphanumeric","value":"POL","length":3,"resetable":null,"format":null},{"type":"date","value":null,"length":null,"resetable":true,"format":"YYMMDD"},{"type":"incrementing","value":null,"length":3,"resetable":null,"format":null}]}', 
        'VISIT_REGISTRATION'=> '{"separator":{"distance":1,"separator":null},"structure":[{"type":"alphanumeric","value":"REG","length":3,"resetable":null,"format":null},{"type":"date","value":null,"length":null,"resetable":true,"format":"YYMMDD"},{"type":"incrementing","value":null,"length":4,"resetable":null,"format":null}]}', 
        'VISIT_EXAMINATION'=> '{"separator":{"distance":1,"separator":"-"},"structure":[{"type":"alphanumeric","value":"VIX","length":3,"resetable":null,"format":null},{"type":"date","value":null,"length":null,"resetable":true,"format":"YYMMDD"},{"type":"incrementing","value":null,"length":4,"resetable":null,"format":null}]}', 
        'REFERRAL'=> '{"separator":{"distance":1,"separator":"-"},"structure":[{"type":"alphanumeric","value":"REFF","length":4,"resetable":null,"format":null},{"type":"date","value":null,"length":null,"resetable":true,"format":"YYMMDD"},{"type":"incrementing","value":null,"length":3,"resetable":null,"format":null}]}', 
        'INVOICE'=> '{"separator":{"distance":1,"separator":"-"},"structure":[{"type":"alphanumeric","value":"INV","length":3,"resetable":null,"format":null},{"type":"date","value":null,"length":null,"resetable":true,"format":"YYMMDD"},{"type":"incrementing","value":null,"length":4,"resetable":null,"format":null}]}', 
        'PRESCRIPTION'=> '{"separator":{"distance":1,"separator":"-"},"structure":[{"type":"alphanumeric","value":"REC","length":3,"resetable":null,"format":null},{"type":"date","value":null,"length":null,"resetable":true,"format":"YYYYMMDD"},{"type":"incrementing","value":null,"length":3,"resetable":null,"format":null}]}', 
        'MEDICAL_RECORD'=> '{"separator":{"distance":1,"separator":null},"structure":[{"type":"alphanumeric","value":"RM","length":2,"resetable":null,"format":null},{"type":"date","value":null,"length":null,"resetable":null,"format":"YYMMDD"},{"type":"incrementing","value":null,"length":4,"resetable":null,"format":null}]}', 
        'ITEM'=> '{"separator":{"distance":1,"separator":null},"structure":[{"type":"alphanumeric","value":"ITEM","length":4,"resetable":null,"format":null},{"type":"incrementing","value":null,"length":5,"resetable":null,"format":null}]}', 
        'MEDICAL_ITEM'=> '{"separator":{"distance":1,"separator":null},"structure":[{"type":"alphanumeric","value":"MED","length":3,"resetable":null,"format":null},{"type":"incrementing","value":null,"length":5,"resetable":null,"format":null}]}'
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        $data = JobRequest::all();
        $encodings = app(config('database.models.Encoding'))->get();
        $model = app(config('database.models.ModelHasEncoding'));
        foreach ($encodings as $encoding) {
            if (isset($this->dummy_encodings[$encoding->label])){
                $json = $this->dummy_encodings[$encoding->label];
                $json = json_decode($json,true);
                $model_has_encoding = $model->where('encoding_id',$encoding->getKey())->first();
                $model_has_encoding->setAttribute('separator',$json['separator']);
                $model_has_encoding->setAttribute('structure',$json['structure']);
                $model_has_encoding->save();
            }
        }
        app(EncodingWrapper::class)->installationSetup();
    }
}
