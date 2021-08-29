<?php

namespace App\Http\Repositories;

use App\Jobs\CsvUploadJob;
use App\Module;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ModuleRepository
{
    protected $module;

    public $errors = [];

    public function __construct(Module $module)
    {
        $this->module = $module;

    }

    public function formattedData($worksheet, $filename)
    {

        $valueworksheet = $worksheet;

        $csvData = [];
        $i = 0;
        if (count($valueworksheet) == 0) {
            throw new \Exception("No data found in the uploaded file");
        }
        foreach ($valueworksheet as $data => $value) {

            if ($data >= 1) {
                $csvData[] = [
                    'Module_code' => $value[0],
                    'Module_name' => $value[1],
                    'Module_term' => $value[2],

                ];
            }
        }
        return $csvData;
    }

    public function validated($worksheet, $filename)
    {

        $valueworksheet = $this->formattedData($worksheet, $filename);

        $row = 0;

        foreach ($valueworksheet as $data) {

            $validator = Validator::make($data, [
                'Module_name' => 'required|regex:/(^[A-Za-z0-9 ]+$)+/',
                'Module_term' => 'required|regex:/(^[A-Za-z0-9 ]+$)+/',
                'Module_code' => 'required|regex:/(^[A-Za-z0-9 ]+$)+/',
            ], [
                'Module_name.required' => 'The Module name field is required.',
                'Module_term.required' => 'The Module_term field is required.',
                'Module_code.required' => 'The Module_code field is required.',
                'Module_name.regex' => 'The Module name contains symbol.',
                'Module_code.regex' => 'The Module code contains symbol.',
                'Module_term.regex' => 'The Module term contains symbol.',

            ]);

            if ($validator->fails()) {

                $this->errors[] = [

                    "row" => $row + 2,
                    "errors" => ($validator->errors()),
                ];
            }
            $row++;
        }
        return $valueworksheet;

    }

    public function moduleCsv($worksheet, $filename)
    {

        $bookings = $this->validated($worksheet, $filename);

        $valueworksheet = $worksheet;
        $count = count($valueworksheet) - 1;
        $insertData = [];

        if (count($this->errors) == 0) {
            $csvJob = (new CsvUploadJob($valueworksheet))->delay(Carbon::now()->addSeconds(1));
            dispatch($csvJob);
        
            return response()->json([
                'status' => 'ok',
                'msg' => $count . ' Entries successfully updated.',
            ], 200);
        } else {
            $error = [];
            foreach ($valueworksheet as $data => $value) {
                $count=$data+1;
                if ($data == 0) {
                    if (!empty($value[0]) && ($value[0] != 'Module_code')) {
                        $error[] = "Header column (" . $value[0] . " at 1st column) is incorrect in csv file";
                    }
                    if ($value[0] == null) {
                        $error[] = "Header column Module_code is missing in csv file";
                    }
                    if (!empty($value[1]) && ($value[1] != 'Module_name')) {
                        $error[] = "Header column (" . $value[1] . " at 1st column) is incorrect in csv file";
                    }
                    if ($value[1] == null) {
                        $error[] = "Header column Module_name is missing in csv file";
                    }
                    if (!empty($value[2]) && ($value[2] != 'Module_term')) {
                        $error[] = "Header column (" . $value[2] . " at 1st column) is incorrect in csv file";
                    }
                    if ($value[2] == null) {
                        $error[] = "Header column Module_term is missing in csv file";
                    }
                } else {

                    if ($value[0] == null) {
                        $error[] = "Module code is missing in csv file at row " . $count;
                    }
                    elseif (!preg_match("/(^[A-Za-z0-9 ]+$)+/", $value[0])) {

                        $error[] = "Module code contains symbols at row " . $count;

                    }
                    if ($value[1] == null) {
                        $error[] = "Module name is missing in csv file at row " . $count;
                    }
                    elseif (!preg_match("/(^[A-Za-z0-9 ]+$)+/", $value[1])) {

                        $error[] = "Module name contains symbols at row " . $count;

                    }
                    if ($value[2] == null) {
                        $error[] = "Module term is missing in csv file at row " . $count;
                    }
                    elseif (!preg_match("/(^[A-Za-z0-9 ]+$)+/", $value[2])) {

                        $error[] = "Module term contains symbols at row " . $count;

                    }
                }
            }
            \Mail::to('praveenrajece1993@gmail.com')->send(new \App\Mail\CsvMail($error));

            return response()->json([
                'status' => 'error',
                'msg' => "The file you've uploaded has some errors",
                'errors' => $error,
                'formatted_errors' => $this->errors,

            ], 400);
        }
    }

}
