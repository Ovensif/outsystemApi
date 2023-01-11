<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Crlist;
use App\Models\Acrlist;

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

    public function getTopWidget()
    {
        $get_top_vendor = Crlist::select('group_name as vendor', DB::raw("count(group_name) as total"))->groupBy("vendor")->limit(1)->get();
        $top_vendor = $get_top_vendor->toArray();

        $get_top_regional = Crlist::select('regional', DB::raw("count(regional) as total"))->groupBy("regional")->limit(1)->get();
        $top_regional = $get_top_regional->toArray();

        return response(['status' => true, "data" => array(
            "top_vendor"    => ['vendor' => $top_vendor[0]['vendor'], 'total' => $top_vendor[0]['total']], 
            "top_regional"  => ['regional' => $top_regional[0]['regional'], 'total' => $top_regional[0]['total']])], 200);

    }

    public function getAcrList(Request $req)
    {
        $total_page = $req->has("limit") ? $req->input('limit') : 5;
        $current_page = $req->has("page") ? $req->input('page') : 1;

        $acr_list = Acrlist::orderBy('id', 'DESC')->paginate($total_page, ['*'], $current_page);
        
        return response($acr_list, 200);

    }


    public function postCreateAcr(Request $req)
    {
        // Get Latest Number then add 1
        $latest_ticket = Acrlist::select('cr_number', 'id')->orderBy('id', 'DESC')->limit(1)->get();
        $latest_number_ticket = $latest_ticket->toArray();

        $number_ticket = explode("_", $latest_number_ticket[0]['cr_number']);
        $next_number_ticket = $number_ticket[1]+1;
        $next_ticket = "ACR_".$next_number_ticket."_".date("Ymd");

        // Set Next Id, for Now! it should be doing it automaticlly
        $next_id = $latest_number_ticket[0]['id']+1;

        $current_user = $req->input('user_name');
        $vendor_name = $req->input('vendor');
        $status_ticket = 'Open';
        $regional = $req->input('regional');
        $type = $req->input('type_cr');
        
        $create_acr = Acrlist::create([
            'id'                => $next_id,
            'cr_number'         => $next_ticket,
            'user_displayname'  => $current_user,
            'group_name'        => $vendor_name,
            'status'            => $status_ticket,
            'regional'          => $regional,
            'type'              => $type
        ]);

        $return_acr = $create_acr->toArray();

        if(empty($return_acr)) return response(['status' => false, 'message' => 'Failed to create new CR', 'data' => []], 200);

        return response(['status' => true, 'message' => 'Successfully create new CR', 'data' => $return_acr], 200);

    }
}
