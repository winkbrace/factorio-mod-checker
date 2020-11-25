<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mod;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        return view('home', ['mods' => $request->session()->get('mods')]);
    }

    public function upload(Request $request)
    {
        $this->validate($request, [
            'mod-list' => 'required|file|mimetypes:application/json'
        ]);

        $userMods = json_decode($request->file('mod-list')->get(), true);

        if (empty($userMods) || empty($userMods['mods']) || empty($userMods['mods'][0]) || empty($userMods['mods'][0]['name']) || empty($userMods['mods'][0]['enabled'])) {
            return redirect('/')->withErrors(['mod-list' => 'Please upload the mod-list.json file as found in C:\\Users\\[Your Name]\\AppData\\Roaming\\factorio\\mods']);
        }

        $userMods = collect($userMods['mods'])->mapWithKeys(fn ($mod) => [$mod['name'] => $mod['enabled']]);

        $mods = Mod::whereIn('name', $userMods->keys())->orderByDesc('released_at')->get();
        $mods->each(fn (Mod $mod) => $mod->enabled = $userMods[$mod->name] ?? false);

        return redirect('/')->with(compact('mods'));
    }
}
