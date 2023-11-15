<?php

namespace Src\App\Controllers\User;

use GTG\MVC\Components\Email;
use Src\App\Controllers\User\TemplateController;
use Src\Models\Championship;
use Src\Models\Competitor;
use Src\Models\Game;
use Src\Utils\ErrorMessages;

class CompetitorsController extends TemplateController 
{
    private ?Championship $championship = null;
    private ?Competitor $competitor = null;

    private function championship(int $championshipId): ?Championship 
    {
        if(!$this->championship = (new Championship())->findById($championshipId)) {
            $this->setMessage('error', _('Nenhum campeonato foi encontrado!'))->APIResponse([], 404);
            return null;
        }

        return $this->championship;
    }
    
    private function competitor(int $competitorId): ?Competitor 
    {
        if(!$this->competitor = (new Competitor())->findById($competitorId)) {
            $this->setMessage('error', _('Nenhum competidor foi encontrado!'))->APIResponse([], 404);
            return null;
        } elseif($this->competitor->cam_id != $this->championship->id) {
            $this->setMessage('error', _('Este competidor não pertence à este campeonato!'))->APIResponse([], 403);
            return null;
        }

        return $this->competitor;
    }

    public function index(array $data): void 
    {
        $this->addData();

        if(!$this->championship(intval($data['championship_id']))) {
            $this->session->setFlash('error', _('Nenhum competidor foi encontrado!'));
            $this->redirect('user.championships.index', ['championship_id' => $this->championship->id]);
        }

        $this->render('user/competitors/index', [
            'dbChampionship' => $this->championship
        ]);
    }

    public function show(array $data): void 
    {
        if(!$this->championship(intval($data['championship_id']))) return;
        if(!$this->competitor(intval($data['competitor_id']))) return;

        $this->APIResponse([
            'save' => [
                'action' => $this->getRoute('user.competitors.update', [
                    'championship_id' => $this->championship->id,
                    'competitor_id' => $this->competitor->id,
                ]),
                'method' => 'put'
            ],
            'content' => $this->competitor->getData()
        ], 200);
    }

    public function store(array $data): void 
    {
        if(!$this->championship(intval($data['championship_id']))) return;
        $this->competitor = (new Competitor())->loadData([
            'usu_id' => $this->session->getAuth()->id,
            'cam_id' => $this->championship->id,
            'name' => $data['name'],
            'img' => $data['img']
        ]);
        if(!$this->competitor->save()) {
            $this->setMessage('error', ErrorMessages::form())->setErrors(
                $this->competitor->getFirstErrors()
            )->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success',
            sprintf(_('O competidor "%s" foi cadastrado com sucesso!'), $this->competitor->name)
        )->APIResponse([], 200);
    }

    public function update(array $data): void 
    {
        if(!$this->championship(intval($data['championship_id']))) return;
        if(!$this->competitor(intval($data['competitor_id']))) return;
        $this->competitor->loadData([
            'name' => $data['name'],
            'img' => $data['img']
        ]);
        if(!$this->competitor->save()) {
            $this->setMessage('error', ErrorMessages::form())->setErrors(
                $this->competitor->getFirstErrors()
            )->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success', 
            sprintf(_('Os dados do competidor "%s" foram alterados com sucesso!'), $this->competitor->name)
        )->APIResponse([], 200);
    }

    public function delete(array $data): void 
    {
        if(!$this->championship(intval($data['championship_id']))) return;
        if(!$this->competitor(intval($data['competitor_id']))) return;
        if(!$this->competitor->destroy()) {
            $this->setMessage('error', _('Não foi possível excluir o competidor!'))->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success', 
            sprintf(_('O competidor "%s" foi excluído com sucesso.'), $this->competitor->name)
        )->APIResponse([], 200);
    }

    public function list(array $data): void 
    {
        $data = array_merge($data, filter_input_array(INPUT_GET, FILTER_DEFAULT));
        if(!$this->championship(intval($data['championship_id']))) return;

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

        $filters['cam_id'] = $this->championship->id;

        $competitors = (new Competitor())->get($filters)->paginate($limit, $page)->sort([$order => $orderType]);
        $count = $competitors->count();
        $pages = ceil($count / $limit);
        
        if($objects = $competitors->fetch(true)) {
            foreach($objects as $competitor) {
                $params = [
                    'championship_id' => $this->championship->id,
                    'competitor_id' => $competitor->id
                ];
                $content[] = [
                    'name' => "
                        <div class=\"widget-content p-0\">
                            <div class=\"widget-content-wrapper\">
                                <div class=\"widget-content-left mr-3\">
                                    <div class=\"widget-content-left\">
                                        <img width=\"40\" height=\"40\" class=\"rounded-circle\" 
                                            src=\"" . (
                                                $competitor->getImageURL() 
                                                ? $competitor->getImageURL() 
                                                : "https://www.gravatar.com/avatar/"
                                            ) . "\">
                                    </div>
                                </div>
                                <div class=\"widget-content-left\">
                                    <div class=\"widget-heading\">{$competitor->name}</div>
                                </div>
                            </div>
                        </div>
                    ",
                    'created_at' => $competitor->getCreatedAtDateTime()->format('d/m/Y'),
                    'updated_at' => $competitor->getUpdatedAtDateTime()->format('d/m/Y'),
                    'actions' => "
                        <div class=\"dropup d-inline-block\">
                            <button type=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\" 
                                data-toggle=\"dropdown\" class=\"dropdown-toggle btn btn-sm btn-primary\">
                                " . _('Ações') . "
                            </button>
                            <div tabindex=\"-1\" role=\"menu\" aria-hidden=\"true\" class=\"dropdown-menu\">
                                <h6 tabindex=\"-1\" class=\"dropdown-header\">" . _('Ações') . "</h6>
                                <button type=\"button\" tabindex=\"0\" class=\"dropdown-item\" 
                                    data-act=\"edit\" data-method=\"get\" data-competitor-name=\"{$competitor->name}\"
                                    data-action=\"{$this->getRoute('user.competitors.show', $params)}\">
                                    " . _('Editar Competidor') . "
                                </button>

                                <button type=\"button\" tabindex=\"0\" class=\"dropdown-item\" 
                                    data-act=\"delete\" data-method=\"delete\" 
                                    data-action=\"{$this->getRoute('user.competitors.delete', $params)}\">
                                    " . _('Excluir Competidor') . "
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