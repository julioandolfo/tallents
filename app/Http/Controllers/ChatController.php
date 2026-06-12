<?php

namespace App\Http\Controllers;

use App\Models\Conversa;
use App\Models\Usuario;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $conversas = Conversa::with(['userOne', 'userTwo'])
            ->where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->orderByDesc('ultima_mensagem_em')
            ->orderByDesc('id')
            ->get();

        // Usuários disponíveis para iniciar conversa.
        $usuarios = Usuario::where('id', '!=', $userId)->orderBy('name')->get();

        return view('chat.index', compact('conversas', 'usuarios'))
            ->with('layout', $this->layout());
    }

    public function show(Conversa $conversa)
    {
        $userId = auth()->id();
        abort_unless($conversa->envolve($userId), 403);

        // Marca como lidas as mensagens recebidas.
        $conversa->mensagens()->where('remetente_id', '!=', $userId)->where('lida', false)->update(['lida' => true]);

        $conversa->load(['userOne', 'userTwo', 'mensagens.remetente']);
        $outro = $conversa->outro($userId);

        $conversas = Conversa::with(['userOne', 'userTwo'])
            ->where('user_one_id', $userId)->orWhere('user_two_id', $userId)
            ->orderByDesc('ultima_mensagem_em')->orderByDesc('id')->get();

        return view('chat.show', compact('conversa', 'outro', 'conversas'))
            ->with('layout', $this->layout());
    }

    public function iniciar(Usuario $usuario)
    {
        abort_if($usuario->id === auth()->id(), 422);

        $conversa = Conversa::entre(auth()->id(), $usuario->id);

        return redirect()->route('chat.show', $conversa);
    }

    public function enviar(Request $request, Conversa $conversa)
    {
        abort_unless($conversa->envolve(auth()->id()), 403);

        $data = $request->validate(['corpo' => 'required|string|max:5000']);

        $conversa->mensagens()->create([
            'remetente_id' => auth()->id(),
            'corpo'        => $data['corpo'],
        ]);

        $conversa->update(['ultima_mensagem_em' => now()]);

        return redirect()->route('chat.show', $conversa);
    }

    /** Escolhe o layout conforme o tipo de usuário (admin x portal). */
    private function layout(): string
    {
        return auth()->user()->role === 'COLABORADOR' ? 'layouts.portal' : 'layouts.app';
    }
}
