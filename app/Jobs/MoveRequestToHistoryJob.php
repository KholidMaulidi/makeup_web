<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Request as UserRequest;
use App\Models\HistoryRequest;
use Carbon\Carbon;

class MoveRequestToHistoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $currentDateTime = Carbon::now('Asia/Jakarta');
        $requests = UserRequest::where('status', 'approved')
            ->where('end_time', '<=', $currentDateTime->toTimeString())
            ->get();

        foreach ($requests as $request) {
            HistoryRequest::create([
                'id_user' => $request->id_user,
                'id_mua' => $request->id_mua,
                'package_id' => $request->package_id,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => 'completed',
            ]);

            $request->delete();
        }
    }
}
