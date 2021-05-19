<?php

namespace App\Http\Controllers;

use Auth;
use App\Conta;
use App\Ticket;
use App\TicketComentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;
use Redmine\Client;
use League\HTMLToMarkdown\HtmlConverter;
use Michelf\Markdown;

class TicketController extends Controller
{
    private $cdContaCon;
    private $service;

    public function __construct()
    {
        $this->middleware('auth');
        $this->cdContaCon = \Session::get('SESSION_CD_CONTA');
        $this->service = $this->redmineService();
    }

    public function index()
    {
        $tickets = [];

        $tickets = Ticket::where('cd_conta_con', $this->cdContaCon)->where('user_id', Auth::id())->get();

        $idsRedmine = $tickets->pluck('cd_redmine_task_tic')->toArray();
        if ($idsRedmine) {
            $idsRedmine = implode(',', $idsRedmine);
            $tickets = $this->service->issue->all(['issue_id' => $idsRedmine, 'status_id' => '*']);
        } else {
            $tickets['issues'] = [];
        }

        return view('ticket/tickets', ['tickets' => $tickets, '']);
    }

    public function create()
    {
        $trackers = $this->service->tracker->all();
        $trackers = $trackers['trackers'];
        return view('ticket/novo', ['trackers' => $trackers]);
    }

    public function store(Request $request)
    {
        $converter = new HtmlConverter(array('strip_tags' => true));
        $descricao = $converter->convert($request->descricao);

        $task = $this->service->issue->create([
            'project_id'  => env('REDMINE_PROJETO'),
            'subject'     => $request->titulo,
            'description' => $descricao,
            'tracker_id'  => $request->tipo,
            'status_id'   => 1, //Novo
            'priority_id' => 2,  //Normal
            'assigned_to_id' => 1
        ]);

        if ($this->service->getResponseCode() === 201) {
            Ticket::create([
                'cd_redmine_task_tic' => (int)$task->id,
                'cd_conta_con' => $this->cdContaCon,
                'user_id' => Auth::id()

            ]);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileContent = file_get_contents($file->getRealPath());
            $fileUploaded = $this->service->attachment->upload($fileContent);
            $fileUploaded = json_decode($fileUploaded);
            $this->service->issue->attach($task->id, ['token' => $fileUploaded->upload->token,
                                                     'filename' => $file->getClientOriginalName(),
                                                     'content_type' => $file->getClientMimeType()
                                                    ]);
        }

        $conta = Conta::where('cd_conta_con', $this->cdContaCon)->first();

        $note = "Conta (".$this->cdContaCon.") :".$conta->nm_razao_social_con;
        $note .= "<br /> Usuario (".Auth::id().") :".Auth::user()->name;

        $this->service->issue->addNoteToIssue($task->id, $converter->convert($note), $privateNote = true);

        return redirect('/suporte/tickets');
    }

    public function show($idIssue)
    {
        $ticket = $this->service->issue->show($idIssue, ['status_id' => '*', 'include' => 'journals,attachments']);
        $idEasy =  Ticket::where('cd_conta_con', $this->cdContaCon)
                           ->where('user_id', Auth::id())
                           ->where('cd_redmine_task_tic', $idIssue)
                           ->first()->cd_ticket_tic;

        $ticket['issue']['description'] = Markdown::defaultTransform($ticket['issue']['description']);

        foreach ($ticket['issue']['journals'] as $key => $comentario) {
            if (empty($comentario['details'])) {
                $ticket['issue']['journals'][$key]['notes'] = Markdown::defaultTransform($ticket['issue']['journals'][$key]['notes']);
            }
        }

        $idComentarios = TicketComentario::where('cd_conta_con', $this->cdContaCon)
                                       ->where('user_id', Auth::id())
                                       ->where('cd_ticket_tic', $idEasy)
                                       ->pluck('cd_issue_note_tco')->toArray();

        return view('ticket/detalhes', ['ticket' => $ticket, 'id' => $idEasy, 'idComentarios' => $idComentarios]);
    }

    public function comment(Request $request, $idIssue)
    {
        if (!empty($request->descricao)) {
            $converter = new HtmlConverter(array('strip_tags' => true));
            $descricao = $converter->convert($request->descricao);
            $this->service->issue->addNoteToIssue($idIssue, $descricao);

            if ($this->service->getResponseCode() === 200) {
                $ticket = $this->service->issue->show($idIssue, ['status_id' => '*', 'include' => 'journals']);
                
                $note = end($ticket['issue']['journals']);

                $idEasy =  Ticket::where('cd_conta_con', $this->cdContaCon)
                           ->where('user_id', Auth::id())
                           ->where('cd_redmine_task_tic', $idIssue)
                           ->first()->cd_ticket_tic;
            
                $comentario = TicketComentario::create([
                    'cd_issue_note_tco' => $note['id'],
                    'cd_conta_con' => $this->cdContaCon,
                    'user_id' => Auth::id(),
                    'cd_ticket_tic' => $idEasy
                ]);
            }
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileContent = file_get_contents($file->getRealPath());
            $fileUploaded = $this->service->attachment->upload($fileContent);
            $fileUploaded = json_decode($fileUploaded);
            $this->service->issue->attach($idIssue, ['token' => $fileUploaded->upload->token,
                                                     'filename' => $file->getClientOriginalName(),
                                                     'content_type' => $file->getClientMimeType()
                                                    ]);
        }

        return redirect('/suporte/ticket/'.$idIssue);
    }

    public function anexo($id)
    {
        $fileName =  $this->service->attachment->show($id)['attachment']['filename'];
        $fileContents = $this->service->attachment->download($id);
        return response($fileContents)
                        ->header('Cache-Control', 'no-cache private')
                        ->header('Content-Description', 'File Transfer')
                        ->header('Content-Type', 'application/octet-stream')
                        ->header('Content-length', strlen($fileContents))
                        ->header('Content-Disposition', 'attachment; filename=' . $fileName)
                        ->header('Content-Transfer-Encoding', 'binary');
    }

    private function redmineService()
    {
        return new Client(env('REDMINE_HOST'), env('REDMINE_KEY_API'));
    }
}
