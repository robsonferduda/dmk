<?php

namespace App\Http\Controllers;

use Auth;
use App\Processo;
use App\ProcessoComprovacao;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * [TIMEMARK - EM TESTES]
 * Controller das comprovações Timemark (fotos com data/hora, GPS e logo)
 * enviadas pelo correspondente para um processo.
 *
 * Mantido isolado do ProcessoController para que falhas/iterações nesta
 * feature em testes não comprometam o fluxo principal de processos.
 */
class ProcessoComprovacaoController extends Controller
{
    private $cdContaCon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->cdContaCon = \Session::get('SESSION_CD_CONTA');
    }

    /**
     * Lista (JSON) de comprovações de um processo.
     */
    public function index($id)
    {
        $processo = $this->processoAcessivel($id);
        if (!$processo) {
            return response()->json(['success' => false, 'message' => 'Processo não acessível'], 403);
        }

        $itens = ProcessoComprovacao::where('cd_processo_pro', $processo->cd_processo_pro)
                                    ->orderBy('dt_captura_pcm', 'DESC')
                                    ->get();

        return response()->json($itens);
    }

    /**
     * Servir imagem (original ou marcada) com checagem de acesso.
     * Tipo: "marcada" (default) ou "original".
     */
    public function imagem($id, $tipo = 'marcada')
    {
        $cmp = ProcessoComprovacao::where('cd_processo_comprovacao_pcm', $id)->first();
        if (!$cmp) {
            abort(404);
        }
        if (!$this->processoAcessivel($cmp->cd_processo_pro)) {
            abort(403);
        }

        $caminho = ($tipo === 'original')
            ? storage_path($cmp->arquivo_original_pcm)
            : storage_path($cmp->arquivo_marcado_pcm ?: $cmp->arquivo_original_pcm);

        if (!is_file($caminho)) {
            abort(404);
        }

        return response()->file($caminho, [
            'Content-Type' => $cmp->mime_pcm ?: 'image/jpeg',
        ]);
    }

    /**
     * Recebe o upload da foto + metadados (lat, lng, captura, observação),
     * gera versão estampada (Timemark) e persiste.
     *
     * Espera multipart/form-data:
     *   - foto (file, obrigatório, image/*)
     *   - latitude, longitude, precisao (opcionais)
     *   - dt_captura (ISO 8601, opcional - default: now)
     *   - endereco (opcional)
     *   - observacao (opcional)
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'foto'      => 'required|file|image|max:20480', // 20 MB
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'precisao'  => 'nullable|numeric',
        ]);

        $processo = $this->processoAcessivel($id);
        if (!$processo) {
            return response()->json(['success' => false, 'message' => 'Processo não acessível'], 403);
        }

        $file       = $request->file('foto');
        $latitude   = $request->input('latitude');
        $longitude  = $request->input('longitude');
        $precisao   = $request->input('precisao');
        $endereco   = $request->input('endereco');
        $observacao = $request->input('observacao');
        $dtCaptura  = $request->input('dt_captura')
            ? date('Y-m-d H:i:s', strtotime($request->input('dt_captura')))
            : date('Y-m-d H:i:s');

        // Pasta de destino (segue convenção do FilepickerController):
        // storage/arquivos/{conta}/processos/{processo}/comprovacoes
        $relDir = "arquivos/{$processo->cd_conta_con}/processos/{$processo->cd_processo_pro}/comprovacoes";
        $absDir = storage_path($relDir);
        if (!is_dir($absDir)) {
            @mkdir($absDir, 0775, true);
        }

        $base       = uniqid('pcm_') . '_' . date('Ymd_His');
        $extOrig    = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $nomeOrig   = $base . '_orig.' . $extOrig;
        $nomeMark   = $base . '_mark.jpg';
        $relOrig    = $relDir . '/' . $nomeOrig;
        $relMark    = $relDir . '/' . $nomeMark;

        // Hash do original (auditoria/integridade)
        $hash = hash_file('sha256', $file->getRealPath());

        // Salva original
        $file->move($absDir, $nomeOrig);

        // Gera versão marcada (Timemark)
        try {
            $this->estampar(
                $absDir . '/' . $nomeOrig,
                $absDir . '/' . $nomeMark,
                [
                    'data_hora'  => date('d/m/Y H:i', strtotime($dtCaptura)),
                    'latitude'   => $latitude,
                    'longitude'  => $longitude,
                    'precisao'   => $precisao,
                    'endereco'   => $endereco,
                    'processo'   => $processo->nu_processo_pro,
                    'autor'      => optional(Auth::user())->name,
                ]
            );
            $arquivoMarcado = $relMark;
        } catch (\Exception $e) {
            // Se a estampa falhar, segue só com o original (não derruba upload).
            \Log::warning('[TIMEMARK] Falha ao estampar imagem: ' . $e->getMessage());
            $arquivoMarcado = null;
        }

        $cmp = ProcessoComprovacao::create([
            'cd_processo_pro'         => $processo->cd_processo_pro,
            'cd_conta_con'            => $processo->cd_conta_con,
            'cd_correspondente_cor'   => $processo->cd_correspondente_cor,
            'cd_entidade_ete'         => Auth::user()->cd_entidade_ete,
            'cd_user_upload'          => Auth::user()->id,
            'arquivo_original_pcm'    => $relOrig,
            'arquivo_marcado_pcm'     => $arquivoMarcado,
            'mime_pcm'                => $file->getClientMimeType() ?: 'image/jpeg',
            'tamanho_bytes_pcm'       => is_file($absDir . '/' . $nomeOrig) ? filesize($absDir . '/' . $nomeOrig) : null,
            'dt_captura_pcm'          => $dtCaptura,
            'nu_latitude_pcm'         => $latitude,
            'nu_longitude_pcm'        => $longitude,
            'nu_precisao_metros_pcm'  => $precisao,
            'ds_endereco_pcm'         => $endereco,
            'ds_observacao_pcm'       => $observacao,
            'hash_sha256_pcm'         => $hash,
        ]);

        return response()->json([
            'success'      => true,
            'comprovacao'  => $cmp,
            'url_marcada'  => url('processos/comprovacao/' . $cmp->cd_processo_comprovacao_pcm . '/imagem'),
            'url_original' => url('processos/comprovacao/' . $cmp->cd_processo_comprovacao_pcm . '/imagem/original'),
        ]);
    }

    /**
     * Remove (soft-delete) uma comprovação.
     */
    public function destroy($id)
    {
        $cmp = ProcessoComprovacao::where('cd_processo_comprovacao_pcm', $id)->first();
        if (!$cmp) {
            return response()->json(['success' => false, 'message' => 'Não encontrado'], 404);
        }
        if (!$this->processoAcessivel($cmp->cd_processo_pro)) {
            return response()->json(['success' => false, 'message' => 'Sem permissão'], 403);
        }

        $cmp->delete();

        return response()->json(['success' => true]);
    }

    // -----------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------

    /**
     * Carrega o processo respeitando a regra de visibilidade do usuário.
     * Retorna null caso o usuário não tenha acesso.
     */
    private function processoAcessivel($id)
    {
        $slug = optional(Auth::user()->role()->first())->slug;

        $q = Processo::where('cd_processo_pro', $id);

        if ($slug === 'correspondente') {
            $q->where('cd_correspondente_cor', $this->cdContaCon);
        } else {
            $q->where('cd_conta_con', $this->cdContaCon);
        }

        return $q->first();
    }

    /**
     * Estampa a imagem com a faixa "Timemark" (data/hora, GPS, processo).
     */
    private function estampar($origem, $destino, array $meta)
    {
        $img    = Image::make($origem);
        $width  = $img->width();
        $height = $img->height();

        // Faixa preta translúcida no rodapé (~22% da altura)
        $faixaH = max(140, (int) ($height * 0.22));
        $img->rectangle(0, $height - $faixaH, $width, $height, function ($draw) {
            $draw->background('rgba(0, 0, 0, 0.55)');
        });

        // Tamanho da fonte proporcional à largura
        $fonteG = max(22, (int) ($width / 38));
        $fonteP = max(16, (int) ($width / 55));

        $padX  = 24;
        $linha = $height - $faixaH + 24;

        $linhas = [];
        $linhas[] = ['t' => $meta['data_hora'] ?? '',                                          's' => $fonteG];
        if (!empty($meta['endereco'])) {
            $linhas[] = ['t' => $meta['endereco'],                                              's' => $fonteP];
        }
        if (isset($meta['latitude'], $meta['longitude']) && $meta['latitude'] !== null && $meta['longitude'] !== null) {
            $coord = sprintf('Lat %.6f  Lng %.6f', $meta['latitude'], $meta['longitude']);
            if (!empty($meta['precisao'])) {
                $coord .= sprintf('  (±%dm)', (int) $meta['precisao']);
            }
            $linhas[] = ['t' => $coord,                                                          's' => $fonteP];
        }
        if (!empty($meta['processo'])) {
            $linhas[] = ['t' => 'Processo: ' . $meta['processo'],                                's' => $fonteP];
        }
        if (!empty($meta['autor'])) {
            $linhas[] = ['t' => 'Por: ' . $meta['autor'],                                        's' => $fonteP];
        }

        foreach ($linhas as $i => $ln) {
            $img->text($ln['t'], $padX, $linha, function ($font) use ($ln) {
                $font->size($ln['s']);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });
            $linha += $ln['s'] + 8;
        }

        // Marca "TIMEMARK" no canto direito inferior
        $img->text('TIMEMARK', $width - $padX, $height - $padX, function ($font) use ($fonteP) {
            $font->size($fonteP);
            $font->color('rgba(255,255,255,0.85)');
            $font->align('right');
            $font->valign('bottom');
        });

        // Logotipo opcional (config/timemark.php => 'logo_path')
        $logoPath = config('timemark.logo_path');
        if ($logoPath && is_file($logoPath)) {
            try {
                $logo = Image::make($logoPath);
                $logoW = (int) ($width * 0.18);
                $logo->resize($logoW, null, function ($c) { $c->aspectRatio(); $c->upsize(); });
                $img->insert($logo, 'top-right', $padX, $padX);
            } catch (\Exception $e) {
                // ignora falha do logo
            }
        }

        $img->save($destino, 85, 'jpg');
    }
}
