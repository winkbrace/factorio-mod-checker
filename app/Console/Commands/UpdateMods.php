<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UpdateMods extends Command
{
    const PAGE_SIZE = 100;
    protected $signature = 'update:mods {--f|force}';
    protected $description = 'This script will download the latest factorio mods metadata';

    public function handle()
    {
        $row = DB::selectOne("select max(updated_at) as updated_at from last_update");

        // Only refresh once per hour
        if ($this->option('force') || empty($row->updated_at) || Carbon::parse($row->updated_at)->lt(Carbon::now()->subHour())) {
            $this->importModsInfo();
            DB::insert("insert into last_update (updated_at) values (:now)", ['now' => Carbon::now()->toISOString()]);
        } else {
            $this->line('The last refresh was less than an hour ago.');
        }

        return 0;
    }

    private function importModsInfo() : void
    {
        DB::statement("delete from mods");

        $response = Http::get('https://mods.factorio.com/api/mods', ['page_size' => self::PAGE_SIZE, 'page' => 1])->json();

        $total = $response['pagination']['count'];
        $pages = $response['pagination']['page_count'];

        for ($page = 1; $page <= $pages; $page++) {
            $result = Http::get('https://mods.factorio.com/api/mods', ['page_size' => self::PAGE_SIZE, 'page' => $page])->json()['results'];
            $prepared = [];
            foreach ($result as $row) {
                try {
                    if (empty($row['latest_release'])) {
                        continue;
                    }

                    $prepared[] = [
                        'name' => $row['name'],
                        'title' => $row['title'],
                        'author' => $row['owner'],
                        'version' => $row['latest_release']['version'],
                        'factorio_version' => $row['latest_release']['info_json']['factorio_version'],
                        'released_at' => $row['latest_release']['released_at'],
                    ];
                } catch (\Throwable $e) {
                    $this->error("problem with " . json_encode($row));
                    die;
                }
            }

            DB::table('mods')->insert($prepared);
            $this->line(($page * self::PAGE_SIZE) . ' of ' . $total . ' mods have been updated.');
        }
    }
}
