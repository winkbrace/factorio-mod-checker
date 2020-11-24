<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ListMods extends Command
{
    protected $signature = 'list:mods';
    protected $description = 'Display list of mods ordered by released date desc';

    public function handle()
    {
        $list = DB::table('mods')->select()->orderByDesc('released_at')->limit(10)->get();
        foreach ($list as $i => $row) {
            $list[$i] = (array) $row;
        }

        $this->table(array_keys($list[0]), $list);

        return 0;
    }
}
