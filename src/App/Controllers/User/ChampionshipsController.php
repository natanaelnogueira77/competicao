<?php

namespace Src\App\Controllers\User;

use GTG\MVC\Components\Email;
use Src\App\Controllers\User\TemplateController;
use Src\Models\Championship;
use Src\Models\Clash;
use Src\Models\Game;
use Src\Utils\ErrorMessages;

class ChampionshipsController extends TemplateController 
{
    private ?Championship $championship = null;

    private function championship(int $championshipId): ?Championship 
    {
        if(!$this->championship = (new Championship())->findById($championshipId)) {
            $this->setMessage('error', _('Nenhum campeonato foi encontrado!'))->APIResponse([], 404);
            return null;
        }

        return $this->championship;
    }

    public function index(array $data): void 
    {
        $this->addData();
        $this->render('user/championships/index', [
            'dbGames' => (new Game())->get()->fetch(true)
        ]);
    }

    public function single(array $data): void 
    {
        $this->addData();

        if(!$this->championship(intval($data['championship_id']))) {
            $this->session->setFlash('error', _('Nenhum campeonato foi encontrado!'));
            $this->redirect('user.championships.index');
        }

        if($this->championship->isInProgress()) {
            if(!$dbClash = $this->championship->getCurrentClash()) {
                $this->championship->setAsFinished()->save();
                $this->redirect('user.championships.single', [
                    'championship_id' => $this->championship->id
                ]);
            }

            $dbClash->competitor1();
            $dbClash->competitor2();
        } elseif($this->championship->isFinished()) {
            $dbCompetitor = $this->championship->getWinner();
        }

        $bracketsData = $this->championship->getBracketsData();

        $this->render('user/championships/single', [
            'dbChampionship' => $this->championship,
            'dbClash' => $dbClash,
            'dbCompetitor' => $dbCompetitor,
            'bracketsData' => $bracketsData['bracketsData'],
            'roundLabels' => $bracketsData['roundLabels']
        ]);
    }

    public function brackets(array $data): void 
    {
        $this->addData();

        if(!$this->championship(intval($data['championship_id']))) {
            $this->session->setFlash('error', _('Nenhum campeonato foi encontrado!'));
            $this->redirect('user.championships.index');
        }

        $bracketsData = $this->championship->getBracketsData();

        $this->render('user/championships/brackets', [
            'dbChampionship' => $this->championship,
            'bracketsData' => $bracketsData['bracketsData'],
            'roundLabels' => $bracketsData['roundLabels']
        ]);
    }

    public function show(array $data): void 
    {
        if(!$this->championship(intval($data['championship_id']))) return;

        $this->APIResponse([
            'save' => [
                'action' => $this->getRoute('user.championships.update', ['championship_id' => $this->championship->id]),
                'method' => 'put'
            ],
            'content' => $this->championship->getData()
        ], 200);
    }

    public function store(array $data): void 
    {
        $this->championship = (new Championship())->loadData([
            'usu_id' => $this->session->getAuth()->id,
            'jog_id' => $data['jog_id'],
            'name' => $data['name'],
            'c_status' => Championship::S_AWAITING,
            'metadata' => $data['metadata']
        ]);
        if(!$this->championship->save()) {
            $this->setMessage('error', ErrorMessages::form())->setErrors(
                $this->championship->getFirstErrors()
            )->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success',
            sprintf(_('O campeonato "%s" foi cadastrado com sucesso!'), $this->championship->name)
        )->APIResponse([], 200);
    }

    public function update(array $data): void 
    {
        if(!$this->championship(intval($data['championship_id']))) return;
        $this->championship->loadData([
            'jog_id' => $data['jog_id'],
            'name' => $data['name'],
            'metadata' => $data['metadata']
        ]);
        if(!$this->championship->save()) {
            $this->setMessage('error', ErrorMessages::form())->setErrors(
                $this->championship->getFirstErrors()
            )->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success', 
            sprintf(_('Os dados do campeonato "%s" foram alterados com sucesso!'), $this->championship->name)
        )->APIResponse([], 200);
    }

    public function delete(array $data): void 
    {
        if(!$this->championship(intval($data['championship_id']))) return;
        if(!$this->championship->destroy()) {
            $this->setMessage('error', _('Não foi possível excluir o campeonato!'))->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success', 
            sprintf(_('O campeonato "%s" foi excluído com sucesso.'), $this->championship->name)
        )->APIResponse([], 200);
    }

    public function list(array $data): void 
    {
        $data = array_merge($data, filter_input_array(INPUT_GET, FILTER_DEFAULT));

        $content = [];
        $filters = [];

        $limit = $data['limit'] ? intval($data['limit']) : 10;
        $page = $data['page'] ? intval($data['page']) : 1;
        $order = $data['order'] ? $data['order'] : 'id';
        $orderType = $data['orderType'] ? $data['orderType'] : 'ASC';

        if($data['search']) {
            $filters['search'] = [
                'term' => $data['search'],
                'columns' => ['name']
            ];
        }

        $championships = (new Championship())->get($filters)->paginate($limit, $page)->sort([$order => $orderType]);
        $count = $championships->count();
        $pages = ceil($count / $limit);
        
        if($objects = $championships->fetch(true)) {
            $objects = Championship::withGame($objects);
            foreach($objects as $championship) {
                $params = ['championship_id' => $championship->id];
                $content[] = [
                    'name' => $championship->name,
                    'jog_id' => $championship->game->name,
                    'c_status' => "<div class=\"badge badge-{$championship->getStatusColor()}\">
                        {$championship->getStatus()}</div>",
                    'created_at' => $championship->getCreatedAtDateTime()->format('d/m/Y'),
                    'updated_at' => $championship->getUpdatedAtDateTime()->format('d/m/Y'),
                    'actions' => "
                        <div class=\"dropup d-inline-block\">
                            <button type=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\" 
                                data-toggle=\"dropdown\" class=\"dropdown-toggle btn btn-sm btn-primary\">
                                " . _('Ações') . "
                            </button>
                            <div tabindex=\"-1\" role=\"menu\" aria-hidden=\"true\" class=\"dropdown-menu\">
                                <h6 tabindex=\"-1\" class=\"dropdown-header\">" . _('Ações') . "</h6>
                                <a href=\"{$this->getRoute('user.competitors.index', $params)}\" 
                                    type=\"button\" tabindex=\"0\" class=\"dropdown-item\">
                                    " . _('Ver Competidores') . "
                                </a>

                                <button type=\"button\" tabindex=\"0\" class=\"dropdown-item\" 
                                    data-act=\"create-clashes\" data-method=\"post\" data-championship-name=\"{$championship->name}\"
                                    data-action=\"{$this->getRoute('user.championships.createClashes', $params)}\">
                                    " . _('Gerar Confrontos') . "
                                </button>

                                <a href=\"{$this->getRoute('user.championships.brackets', $params)}\" 
                                    type=\"button\" tabindex=\"0\" class=\"dropdown-item\">
                                    " . _('Ver Chaveamentos') . "
                                </a>

                                <a href=\"{$this->getRoute('user.championships.single', $params)}\" 
                                    type=\"button\" tabindex=\"0\" class=\"dropdown-item\">
                                    " . _('Área do Campeonato') . "
                                </a>

                                <div class=\"dropdown-divider\"></div>
                                <button type=\"button\" tabindex=\"0\" class=\"dropdown-item\" 
                                    data-act=\"edit\" data-method=\"get\" data-championship-name=\"{$championship->name}\"
                                    data-action=\"{$this->getRoute('user.championships.show', $params)}\">
                                    " . _('Editar Campeonato') . "
                                </button>

                                <button type=\"button\" tabindex=\"0\" class=\"dropdown-item\" 
                                    data-act=\"delete\" data-method=\"delete\" 
                                    data-action=\"{$this->getRoute('user.championships.delete', $params)}\">
                                    " . _('Excluir Campeonato') . "
                                </button>
                            </div>
                        </div>
                    "
                ];
            }
        }

        $this->APIResponse([
            'content' => [
                'table' => $this->getView('_components/data-table', [
                    'headers' => [
                        'actions' => ['text' => _('Ações')],
                        'name' => ['text' => _('Nome'), 'sort' => true],
                        'jog_id' => ['text' => _('Jogo'), 'sort' => true],
                        'c_status' => ['text' => _('Status'), 'sort' => true],
                        'created_at' => ['text' => _('Criado em'), 'sort' => true],
                        'updated_at' => ['text' => _('Modificado em'), 'sort' => true]
                    ],
                    'order' => [
                        'selected' => $order,
                        'type' => $orderType
                    ],
                    'data' => $content
                ]),
                'pagination' => $this->getView('_components/pagination', [
                    'pages' => $pages,
                    'currPage' => $page,
                    'results' => $count,
                    'limit' => $limit
                ])
            ]
        ], 200);
    }

    public function createClashes(array $data): void 
    {
        if(!$this->championship(intval($data['championship_id']))) return;
        if(!$this->championship->createClashes($this->session->getAuth())) {
            if($this->championship->hasError('create_clashes')) {
                $message = $this->championship->getFirstError('create_clashes');
                $error = 403;
            } else {
                $message = ErrorMessages::requisition();
                $error = 500;
            }

            $this->setMessage('error', $message)->APIResponse([], $error);
            return;
        }

        $this->setMessage(
            'success', 
            sprintf(_('Os confrontos do campeonato "%s" foram gerados com sucesso!'), $this->championship->name)
        )->APIResponse([], 200);
    }

    public function setAsInProgress(array $data): void 
    {
        if(!$this->championship(intval($data['championship_id']))) return;
        if(!$this->championship->clashes()) {
            $this->setMessage(
                'error', 
                _('Não é possível gerar confrontos para um campeonato sem competidores!')
            )->APIResponse([], 500);
            return;
        } elseif(!$this->championship->setAsInProgress()->save()) {
            $this->setMessage('error', ErrorMessages::requisition())->APIResponse([], 500);
            return;
        }

        $this->setMessage(
            'success', 
            sprintf(_('O campeonato "%s" foi iniciado com sucesso!'), $this->championship->name)
        )->APIResponse([], 200);
    }

    public function setClashWinner(array $data): void 
    {
        if(!$this->championship(intval($data['championship_id']))) return;
        if(!$dbClash = (new Clash())->findById(intval($data['clash_id']))) {
            $this->setMessage('error', _('Nenhum confornto foi encontrado!'))->APIResponse([], 404);
            return;
        } elseif(!$dbClash->isReady()) {
            $this->setMessage('error', _('Este confronto já foi encerrado ou ainda não foi definido!'))->APIResponse([], 403);
            return;
        }

        $dbClash->winner = $data['winner_id'];
        if(!$dbClash->winner) {
            $this->setMessage('error', _('Um vencedor precisa ser declarado!'))->APIResponse([], 422);
            return;
        }
        
        $clashes = [];
        $dbClash->setAsFinished();
        $clashes[] = $dbClash;

        if($nextClash = $dbClash->getNextClash()) {
            if(!$nextClash->com1_id) {
                $nextClash->com1_id = $dbClash->winner;
            } else {
                $nextClash->com2_id = $dbClash->winner;
                $nextClash->setAsReady();
            }
            $clashes[] = $nextClash;
        } else {
            if(!$this->championship->setAsFinished()->save()) {
                $this->setMessage('error', ErrorMessages::requisition())->APIResponse([], 500);
                return;
            }
        }

        if(!Clash::saveMany($clashes)) {
            $this->setMessage('error', ErrorMessages::requisition())->APIResponse([], 500);
            return;
        }

        $message = sprintf(
            _('O vencedor do confronto "%s" é "%s"!'), 
            $dbClash->getClashName(),
            $dbClash->clashWinner()->name
        );

        $this->session->setFlash('success', $message);
        $this->setMessage('success', $message)->APIResponse([], 200);
    }
}