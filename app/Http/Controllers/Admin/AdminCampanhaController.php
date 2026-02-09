<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class AdminCampanhaController extends Controller
{
    /**
     * Encerra a campanha manualmente.
     */
    public function encerrar()
    {
        Setting::setValue('campanha_encerrada', 'true', 'Campanha encerrada manualmente');
        Setting::setValue('campanha_motivo_encerramento', 'manual', 'Motivo do encerramento');

        return back()->with('success', 'Campanha encerrada com sucesso. Novos cadastros e cupons estão bloqueados.');
    }

    /**
     * Reabre a campanha (remove encerramento manual).
     */
    public function reabrir()
    {
        Setting::setValue('campanha_encerrada', 'false', 'Campanha reaberta');
        Setting::setValue('campanha_motivo_encerramento', '', 'Motivo do encerramento');

        return back()->with('success', 'Campanha reaberta com sucesso. Cadastros e cupons estão liberados.');
    }
}
