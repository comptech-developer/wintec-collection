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
            $waumin = Waumin::all();
            foreach ($waumin as $key => $value) {
            $branch = DB::table('jumuiya')->select('branch_id')->where('id',$value->jumuiya_id)->first();
            $value->update(['branch_id'=>$branch->branch_id]);
            $this->info('command executed successfully!');
            }
        } catch (\Throwable $th) {
            //throw $th;
              $this->error('command executed failed!' . $th->getMessage());
        }
    }
}
