<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UpdateMods extends Command
{
    private const PAGE_SIZE = 100;

    protected $signature = 'update:mods';
    protected $description = 'This script will download the latest factorio mods metadata';

    public function handle()
    {
        DB::statement("create table if not exists mods_import (name text, title text, author text, version text, factorio_version text, released_at text)");

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

            DB::table('mods_import')->insert($prepared);
            $this->line(Carbon::now()->toDateTimeString(). ' ' . ($page * self::PAGE_SIZE) . ' of ' . $total . ' mods have been updated.');
        }

        // Let's minimize the offline time.
        DB::statement("delete from mods");
        DB::statement("insert into mods select * from mods_import");
        DB::statement("delete from mods_import");

        return 0;
    }
}
