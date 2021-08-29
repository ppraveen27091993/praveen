<?php

namespace App\Http\Controllers;

use App\Http\Repositories\ModuleRepository;
use App\Module;
use App\Jobs\CsvUploadJob;
use Illuminate\Http\Request;

class ModuleController extends Controller
{

    protected $module;

    public function __construct(ModuleRepository $module)
    {
        $this->module = $module;
    }

    public function csv(Request $request)
    {

       
      
        if ($request->hasFile("file")) {
            $filename = request()->file("file")->getClientOriginalName();
            $fileext = request()->file("file")->getClientOriginalExtension();
            $ext = strtolower($fileext);

            if ($ext != 'csv') {
                return response()->json([
                    'status' => 'ok',
                    'msg' => 'Invalid file type',
                ], 400);
            }

            $path1 = $request->file('file')->store('temp');

            $path = storage_path('app') . '/' . $path1;
           
            $path = $request->file('file')->getRealPath();
            $worksheet = array_map('str_getcsv', file($path));
       

            return $this->module->moduleCsv($worksheet, $filename);

        } else {

            return response()->json([
                'status' => 'ok',
                'msg' => 'Choose a file',
            ], 400);
        }
    }

}
