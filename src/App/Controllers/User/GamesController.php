<?php

namespace Src\App\Controllers\User;

use GTG\MVC\Components\Email;
use Src\App\Controllers\User\TemplateController;
use Src\Models\Game;
use Src\Utils\ErrorMessages;

class GamesController extends TemplateController 
{
    private ?Game $game = null;

    private function game(int $gameId): ?Game 
    {
        if(!$this->game = (new Game())->findById($gameId)) {
            $this->setMessage('error', _('Nenhum jogo foi encontrado!'))->APIResponse([], 404);
            return null;
        }

        return $this->game;
    }

    public function index(array $data): void 
    {
        $this->addData();
        $this->render('user/games/index');
    }

    public function show(array $data): void 
    {
        if(!$this->game(intval($data['game_id']))) return;

        $this->APIResponse([
            'save' => [
                'action' => $this->getRoute('user.games.update', ['game_id' => $this->game->id]),
                'method' => 'put'
            ],
            'content' => $this->game->getData()
        ], 200);
    }

    public function store(array $data): void 
    {
        $this->game = (new Game())->loadData([
            'usu_id' => $this->session->getAuth()->id,
            'name' => $data['name']
        ]);
        if(!$this->game->save()) {
            $this->setMessage('error', ErrorMessages::form())->setErrors(
                $this->game->getFirstErrors()
            )->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success',
            sprintf(_('O jogo "%s" foi cadastrado com sucesso!'), $this->game->name)
        )->APIResponse([], 200);
    }

    public function update(array $data): void 
    {
        if(!$this->game(intval($data['game_id']))) return;
        if(!$this->game->loadData(['name' => $data['name']])->save()) {
            $this->setMessage('error', ErrorMessages::form())->setErrors(
                $this->game->getFirstErrors()
            )->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success', 
            sprintf(_('Os dados do jogo "%s" foram alterados com sucesso!'), $this->game->name)
        )->APIResponse([], 200);
    }

    public function delete(array $data): void 
    {
        if(!$this->game(intval($data['game_id']))) return;
        if(!$this->game->destroy()) {
            $this->setMessage('error', _('Não foi possível excluir o jogo!'))->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success', 
            sprintf(_('O jogo "%s" foi excluído com sucesso.'), $this->game->name)
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

        $games = (new Game())->get($filters)->paginate($limit, $page)->sort([$order => $orderType]);
        $count = $games->count();
        $pages = ceil($count / $limit);
        
        if($objects = $games->fetch(true)) {
            foreach($objects as $game) {
                $params = ['game_id' => $game->id];
                $content[] = [
                    'name' => $game->name,
                    'created_at' => $game->getCreatedAtDateTime()->format('d/m/Y'),
                    'updated_at' => $game->getUpdatedAtDateTime()->format('d/m/Y'),
                    'actions' => "
                        <div class=\"dropup d-inline-block\">
                            <button type=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\" 
                                data-toggle=\"dropdown\" class=\"dropdown-toggle btn btn-sm btn-primary\">
                                " . _('Ações') . "
                            </button>
                            <div tabindex=\"-1\" role=\"menu\" aria-hidden=\"true\" class=\"dropdown-menu\">
                                <h6 tabindex=\"-1\" class=\"dropdown-header\">" . _('Ações') . "</h6>
                                <button type=\"button\" tabindex=\"0\" class=\"dropdown-item\" 
                                    data-act=\"edit\" data-method=\"get\" data-game-name=\"{$game->name}\"
                                    data-action=\"{$this->getRoute('user.games.show', $params)}\">
                                    " . _('Editar Jogo') . "
                                </button>

                                <button type=\"button\" tabindex=\"0\" class=\"dropdown-item\" 
                                    data-act=\"delete\" data-method=\"delete\" 
                                    data-action=\"{$this->getRoute('user.games.delete', $params)}\">
                                    " . _('Excluir Jogo') . "
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
}