<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Crlist;

class SimpleApi extends Controller
{
    public function getTotalCrType()
    {
        
        $test = Crlist::select("cr_type", DB::raw('count(cr_type) as total'))->whereIn('cr_type', ['ACR', 'HCR', 'NCR'])->groupBy('cr_type')->get();
        $cr_list = array();

        if(empty($test->toArray())) return response(['status' => false, 'data' => []], 404);

        foreach ($test->toArray() as $key_list => $value) :
            $cr_list[$value['cr_type']] = $value['total'];
        endforeach;

        return response(['status' => true, 'data' => $cr_list], 200);

    }
}
