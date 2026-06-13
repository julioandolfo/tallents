<?php

namespace App\Http\Controllers;

use App\Models\TemplateEmail;
use Illuminate\Http\Request;

class TemplateEmailController extends Controller
{
    public function index()
    {
        $templates = TemplateEmail::orderBy('nome')->get();

        return view('configuracoes.templates.index', compact('templates'));
    }

    public function edit(TemplateEmail $template)
    {
        return view('configuracoes.templates.edit', compact('template'));
    }

    public function update(Request $request, TemplateEmail $template)
    {
        $data = $request->validate([
            'assunto' => 'required|string|max:255',
            'corpo'   => 'required|string',
            'ativo'   => 'boolean',
        ]);

        $template->update([
            'assunto' => $data['assunto'],
            'corpo'   => $data['corpo'],
            'ativo'   => $request->boolean('ativo'),
        ]);

        return redirect()->route('configuracoes.templates.index')->with('success', 'Template atualizado com sucesso!');
    }
}
