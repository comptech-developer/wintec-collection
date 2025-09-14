<?php

namespace App\Console\Commands;

use App\Models\Waumin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Datatransactioncommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:datatransactioncommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
       try {
        $wauminList = Waumin::all();

        foreach ($wauminList as $waumin) {
            // Get branch_id from jumuiya
            $branch = DB::table('jumuiya')
                ->select('branch_id')
                ->where('id', $waumin->jumuiya_id)
                ->first();

            // Skip if branch not found
            if (!$branch) {
                $this->warn("Skipping Waumin ID {$waumin->id}: jumuiya not found");
                continue;
            }

            // Update branch_id without touching updated_at
            $waumin->timestamps = false;
            $waumin->branch_id = $branch->branch_id;
            $waumin->save();
        }

        $this->info('Command executed successfully for all rows!');
    } catch (\Throwable $th) {
        $this->error('Command execution failed: ' . $th->getMessage());
    }
    }
}
