<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessarCupomFiscal;
use App\Models\CupomFiscal;
use App\Models\Setting;

class AdminFilaController extends Controller
{
    /**
     * Desbloqueia a fila de processamento.
     */
    public function desbloquear()
    {
        Setting::setValue('fila_bloqueada', 'false', 'Fila de processamento desbloqueada');
        Setting::setValue('fila_cupom_erro_id', '', 'ID do cupom que causou o bloqueio');

        return back()->with('success', 'Fila de processamento desbloqueada com sucesso. Os cupons pendentes serão processados.');
    }

    /**
     * Reprocessa um cupom com erro: reseta o status e re-despacha na fila.
     */
    public function reprocessar(CupomFiscal $cupom)
    {
        if ($cupom->status !== CupomFiscal::STATUS_ERRO) {
            return back()->with('error', 'Apenas cupons com status "erro" podem ser reprocessados.');
        }

        // Remove números parciais (se houver)
        $cupom->numerosDaSorte()->delete();

        // Reseta o status para validado (aguardando na fila)
        $cupom->update([
            'status' => CupomFiscal::STATUS_VALIDADO,
            'erro_processamento' => null,
        ]);

        // Despacha novamente na fila
        ProcessarCupomFiscal::dispatch($cupom);

        return back()->with('success', "Cupom #{$cupom->numero} foi reenviado para processamento.");
    }

    /**
     * Lista cupons com erro para gerenciamento.
     */
    public function cuponsComErro()
    {
        $cupons = CupomFiscal::where('status', CupomFiscal::STATUS_ERRO)
            ->with('participante')
            ->orderByDesc('updated_at')
            ->get();

        $filaBloqueada = Setting::getValue('fila_bloqueada') === 'true';

        return view('admin.cupons-erro', compact('cupons', 'filaBloqueada'));
    }
}
