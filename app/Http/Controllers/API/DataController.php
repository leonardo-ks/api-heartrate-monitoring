<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataResource;
use Illuminate\Http\Request;
use App\Models\Data;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class DataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $data = Data::where('user_id', auth()->user()->id)->latest()->get();
        return response()->json(['success' => true, 'message' => 'success', 'data' => DataResource::collection($data)]);
    }

    public function getDataByDate($start, $end){
        $data = Data::where('user_id', auth()->user()->id)->whereBetween('created_at', [$start, $end])->latest()->get();
        return response()->json(['success' => true, 'message' => 'success', 'data' => DataResource::collection($data)]);
    }

    public function getLimit(){
        $still = Data::where('user_id', auth()->user()->id)->where('step_changes', 0)->where('avg_heart_rate', '>', 40)->whereNotNull('label');
        $walk = Data::where('user_id', auth()->user()->id)->where('step_changes', '>', 0)->whereNotNull('label');
        return response()->json(['success' => true, 'message' => 'success', 'lower' => intval($still->min('avg_heart_rate')), 'upperStill' => intval($still->max('avg_heart_rate')), 'upperWalk' => intval($walk->max('avg_heart_rate'))]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'avg_heart_rate' => 'required|integer',
            'step_changes' => 'required|integer',
        ]);

        if($validator->fails()){
            return response()->json(['success' => false, 'message' => $validator->errors()]);
        }

        if($request->created_at != null) {
            $data = Data::create([
                'user_id' => auth()->user()->id,
                'avg_heart_rate' => $request->avg_heart_rate,
                'step_changes' => $request->step_changes,
                'step' => $request->step,
                'label' => $request->label,
                'created_at' => $request->created_at,
                'updated_at' => $request->created_at
            ]);
        } else {
            $data = Data::create([
                'user_id' => auth()->user()->id,
                'avg_heart_rate' => $request->avg_heart_rate,
                'step_changes' => $request->step_changes,
                'step' => $request->step,
                'label' => $request->label,
            ]);
        }



        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan', 'data' => new DataResource($data)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Data::find($id);
        if (is_null($data)) {
            return response()->json(['success' => false,'message' => 'Data not found'], 404);
        }
        return response()->json(['success' => true, 'data' => new DataResource($data)]);
    }

    public function getaverage()
    {
        $data = Data::where('user_id', auth()->user()->id)->whereDate('created_at', Carbon::today());
        if (is_null($data)) {
            return response()->json(['success' => false, 'message' => 'Data not found'], 404);
        }
        return response()->json(['success' => true, 'message' => 'success', 'today_steps'=> intval($data->max('step')), 'avg_heart_rate'=> intval($data->avg('avg_heart_rate'))]);
    }

    public function findData(Request $request)
    {
        $data = Data::where('avg_heart_rate', $request->step_changes)->where('step_changes', $request->step_changes);
        $labels = [];
        foreach($data->latest()->get() as $res) {
            if($res->label != null) {
                array_push($labels, $res->label);
            }
        }
        if ($data->count() > 0) {
            return response()->json(['success' => true, 'found' => true, 'message' => 'Data ditemukan', 'labels' => array_unique($labels)]);
        } else {
            return response()->json(['success' => false, 'found' => false, 'message' => 'Data tidak ditemukan']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Data $data)
    {
        $validator = Validator::make($request->all(),[
            'avg_heart_rate' => 'required|integer',
            'step_changes' => 'required|integer',
            'label' => 'string'
        ]);

        if($validator->fails()){
            return response()->json(['success' => false, 'message' => $validator->errors()]);
        }

        $data->avg_heart_rate = $request->avg_heart_rate;
        $data->step_changes = $request->step_changes;
        $data->label = $request->label;
        $data->save();

        return response()->json(['success' => true, 'message' => 'success', 'data' => new DataResource($data)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Data $data)
    {
        $data->delete();

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
    }
}
