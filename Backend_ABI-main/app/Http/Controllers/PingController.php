<?php

// app/Http/Controllers/PingController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ping;

class PingController extends Controller
{
    public function showPingForm()
    {
        $ping = Ping::latest()->first();
        return view('inventario.ping', compact('ping'));
    }

    public function handlePing(Request $request)
    {
        $ping = $request->input('ping');
        $latestPing = Ping::latest()->first();

        if ($latestPing && $ping == $latestPing->ping) {
            return redirect()->route('inventarios.create');
        } else {
            return redirect()->route('inventario.ping')->with('error', 'Pin incorrecto');
        }
    }

    public function showEditPingForm()
    {
        $ping = Ping::latest()->first();
        return view('inventario.edit-ping', compact('ping'));
    }

    public function updatePing(Request $request)
    {
        $request->validate([
            'ping' => 'required|integer|unique:pings,ping'
        ]);

        Ping::create([
            'ping' => $request->input('ping')
        ]);

        return redirect()->route('inventario.index')->with('success', 'Pin actualizado correctamente');
    }
}

