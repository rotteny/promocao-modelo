<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminNotificacaoController extends Controller
{
    /**
     * Marca todas as notificações não lidas como lidas.
     */
    public function marcarLidas()
    {
        $admin = Auth::guard('admin')->user();
        $admin->unreadNotifications->markAsRead();

        return back()->with('success', 'Notificações marcadas como lidas.');
    }
}
