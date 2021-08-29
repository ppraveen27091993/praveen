<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use App\Module;
class CsvUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $details;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details=$details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $count = count($this->details) - 1;
        foreach ($this->details as $data => $value) {
            if ($data >= 1) {

                $insertData[] = [
                    'module_code' => $value[0],
                    'module_name' => $value[1],
                    'module_term' => $value[2],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),

                ];
            }
        }
        $insert = Module::insert($insertData);
        \Mail::to('charush@accubits.com')->send(new \App\Mail\CsvMail($count . ' Entries successfully updated.'));

    }
}
