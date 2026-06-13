<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Contrato;
use App\Services\AutentiqueService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ContratoController extends Controller
{
    public function index(Request $request)
    {
        $contratos = Contrato::with('colaborador')
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->colaborador_id, fn($q, $v) => $q->where('colaborador_id', $v))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('contratos.index', compact('contratos'));
    }

    public function create(Request $request)
    {
        $colaboradores = Colaborador::orderBy('nome')->get();
        $colaboradorId = $request->colaborador_id;

        return view('contratos.create', compact('colaboradores', 'colaboradorId'));
    }

    public function store(Request $request, AutentiqueService $autentique)
    {
        $data = $request->validate([
            'colaborador_id'    => 'required|exists:colaboradores,id',
            'titulo'            => 'required|string|max:255',
            'tipo'              => 'required|in:CONTRATO,ESTAGIO,ADITIVO,TERMO',
            'conteudo'          => 'nullable|string',
            'arquivo'           => 'nullable|file|max:10240',
            'metodo_assinatura' => 'nullable|in:INTERNO,AUTENTIQUE',
        ]);

        if ($request->hasFile('arquivo')) {
            $data['arquivo'] = $request->file('arquivo')->store('contratos', 'public');
        }

        $data['criado_por']        = auth()->id();
        $data['status']            = 'PENDENTE';
        $data['metodo_assinatura'] = $data['metodo_assinatura'] ?? 'INTERNO';

        $contrato = Contrato::create($data);

        // Envio automático para a Autentique quando escolhido e configurado.
        if ($contrato->metodo_assinatura === 'AUTENTIQUE') {
            [$tipo, $msg] = $this->enviarParaAutentique($contrato, $autentique);
            return redirect()->route('contratos.show', $contrato)->with($tipo, $msg);
        }

        return redirect()->route('contratos.show', $contrato)->with('success', 'Contrato criado!');
    }

    public function show(Contrato $contrato)
    {
        $contrato->load(['colaborador.cargo', 'colaborador.setor', 'colaborador.empresa', 'criadoPor']);

        return view('contratos.show', compact('contrato'));
    }

    public function destroy(Contrato $contrato)
    {
        $contrato->delete();

        return redirect()->route('contratos.index')->with('success', 'Contrato removido.');
    }

    /** Cancela ou reabre um contrato (uso administrativo). */
    public function status(Request $request, Contrato $contrato)
    {
        $data = $request->validate(['status' => 'required|in:PENDENTE,CANCELADO']);

        $contrato->update(['status' => $data['status']]);

        return back()->with('success', 'Status atualizado.');
    }

    /** Pré-visualização do contrato com as variáveis já substituídas. */
    public function preview(Contrato $contrato)
    {
        $contrato->load(['colaborador.cargo', 'colaborador.setor', 'colaborador.empresa']);

        return view('contratos.preview', compact('contrato'));
    }

    /** Download do contrato em PDF (conteúdo renderizado). */
    public function pdf(Contrato $contrato)
    {
        $contrato->load(['colaborador.cargo', 'colaborador.setor', 'colaborador.empresa']);

        return Pdf::loadView('contratos.pdf', compact('contrato'))
            ->download('contrato-' . $contrato->id . '.pdf');
    }

    /** Envia (ou reenvia) o contrato para assinatura via Autentique. */
    public function enviarAutentique(Contrato $contrato, AutentiqueService $autentique)
    {
        [$tipo, $msg] = $this->enviarParaAutentique($contrato, $autentique);

        return back()->with($tipo, $msg);
    }

    /** Consulta a Autentique e atualiza o status do contrato, se assinado. */
    public function sincronizar(Contrato $contrato, AutentiqueService $autentique)
    {
        if (! $contrato->autentique_document_id) {
            return back()->with('error', 'Contrato não foi enviado para a Autentique.');
        }

        $info = $autentique->consultarDocumento($contrato->autentique_document_id);

        if ($info === null) {
            return back()->with('error', 'Não foi possível consultar a Autentique agora.');
        }

        if ($info['assinado'] && $contrato->status !== 'ASSINADO') {
            $contrato->update(['status' => 'ASSINADO', 'assinado_em' => now()]);
            return back()->with('success', 'Contrato assinado na Autentique — status atualizado.');
        }

        return back()->with('success', $info['assinado'] ? 'Já estava assinado.' : 'Ainda pendente de assinatura.');
    }

    /** Lógica compartilhada de envio à Autentique. Retorna [tipoFlash, mensagem]. */
    private function enviarParaAutentique(Contrato $contrato, AutentiqueService $autentique): array
    {
        if (! $autentique->habilitado()) {
            return ['error', 'Integração Autentique não configurada (defina AUTENTIQUE_TOKEN).'];
        }

        $contrato->load(['colaborador.cargo', 'colaborador.setor', 'colaborador.empresa']);
        $email = $contrato->colaborador?->emailContato();

        if (blank($email)) {
            return ['error', 'O colaborador não possui e-mail para receber o documento.'];
        }

        // Gera o PDF do contrato em arquivo temporário para upload.
        $tmp = tempnam(sys_get_temp_dir(), 'contrato_') . '.pdf';
        Pdf::loadView('contratos.pdf', compact('contrato'))->save($tmp);

        $resultado = $autentique->enviarDocumento($contrato->titulo, $tmp, $contrato->colaborador->nome, $email);
        @unlink($tmp);

        if (! $resultado || empty($resultado['id'])) {
            return ['error', 'Falha ao enviar para a Autentique. Verifique o token e tente novamente.'];
        }

        $contrato->update([
            'metodo_assinatura'        => 'AUTENTIQUE',
            'status'                   => 'PENDENTE',
            'autentique_document_id'   => $resultado['id'],
            'autentique_signature_url' => $resultado['url'],
            'enviado_assinatura_em'    => now(),
        ]);

        return ['success', 'Contrato enviado para assinatura via Autentique.'];
    }
}
