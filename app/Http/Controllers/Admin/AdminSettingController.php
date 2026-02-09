<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Carbon\Carbon;

class AdminSettingController extends Controller
{
    /**
     * Exibe a página de configurações.
     */
    public function index()
    {
        $settings = Setting::orderBy('key')->get();

        return view('admin.settings', compact('settings'));
    }

    /**
     * Salva as configurações atualizadas.
     */
    public function update()
    {
        $dados = request()->validate([
            'settings' => ['required', 'array'],
            'settings.*.key' => ['required', 'string'],
            'settings.*.value' => ['nullable', 'string'],
        ]);

        // Chaves de data que precisam de conversão datetime-local → Y-m-d H:i:s
        $camposData = ['data_inicio', 'data_fim'];

        foreach ($dados['settings'] as $setting) {
            $valor = $setting['value'] ?? '';

            // Converte formato do datetime-local (Y-m-d\TH:i) para Y-m-d H:i:s
            if (in_array($setting['key'], $camposData) && $valor) {
                try {
                    $valor = Carbon::parse($valor)->format('Y-m-d H:i:s');
                } catch (\Exception) {
                    // Mantém o valor original se não conseguir converter
                }
            }

            Setting::setValue($setting['key'], $valor);
        }

        return back()->with('success', 'Configurações atualizadas com sucesso.');
    }
}
