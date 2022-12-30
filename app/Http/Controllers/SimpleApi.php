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

    public function getTotalCrChart()
    {
        
        // Get total chart per Status
        $status = Crlist::select("status", DB::raw("count(status) as total"))->groupBy("status")->get();
        $status_total = array('Progress' => 0 , 'Close' => 0, 'Open' => 0);

        foreach ($status->toArray() as $key => $value) :
            
            if(in_array($value['status'], ['Approved'])):
                $status_total['Progress'] += $value['total'];
            endif;

            if(in_array($value['status'], ['Cancel', 'Reject', 'Close'])):
                $status_total['Close'] += $value['total'];
            endif;

            if(in_array($value['status'], ['Open'])):
                $status_total['Open'] += $value['total'];
            endif;

        endforeach;

        // Get Total chart per Regional
        $total_chart_regional = Crlist::select("regional", DB::raw("count(status) as total"))->groupBy("regional")->get();
        $total_regional = array(
            "SUMBAGUT"      => 0,
            "SUMBAGSEL"     => 0,
            "JABOTABEK"     => 0,
            "WEST JAVA"     => 0,
            "CENTRAL JAVA"  => 0,
            "EAST JAVA"     => 0,
            "BALI NUSRA"    => 0,
            "KALIMANTAN"    => 0,
            "SULAWESI"      => 0,
            "SUMBAGTENG"    => 0,
            "PUMA"          => 0
        );

        foreach ($total_chart_regional->toArray() as $key => $value) :
            $total_regional[$value['regional']] += $value['total'];
        endforeach;

        return response(['status' => true, "data" => ['donut' => $status_total, 'regional' => $total_regional]], 200);

    }
}
