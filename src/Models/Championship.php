<?php

namespace Src\Models;

use DateTime;
use GTG\MVC\DB\DBModel;
use Src\Models\Clash;
use Src\Models\Competitor;
use Src\Models\Game;
use Src\Models\User;

class Championship extends DBModel 
{
    const S_AWAITING = 1;
    const S_IN_PROGRESS = 2;
    const S_FINISHED = 3;

    public ?array $competitors = null;
    public ?array $clashes = null;
    public ?Game $game = null;
    public ?User $user = null;

    public static function tableName(): string 
    {
        return 'campeonato';
    }

    public static function primaryKey(): string 
    {
        return 'id';
    }

    public static function attributes(): array 
    {
        return [
            'usu_id',
            'jog_id',
            'name',
            'c_status',
            'metadata'
        ];
    }

    public function rules(): array 
    {
        return [
            'usu_id' => [
                [self::RULE_REQUIRED, 'message' => _('O usuário é obrigatório!')]
            ],
            'jog_id' => [
                [self::RULE_REQUIRED, 'message' => _('O jogo é obrigatório!')]
            ],
            'c_status' => [
                [self::RULE_REQUIRED, 'message' => _('O status é obrigatório!')],
                [self::RULE_IN, 'values' => array_keys(self::getStates()), 'message' => _('O status é inválido!')]
            ],
            'name' => [
                [self::RULE_REQUIRED, 'message' => _('O nome é obrigatório!')],
                [self::RULE_MAX, 'max' => 100, 'message' => sprintf(_('O nome deve conter no máximo %s caractéres!'), 100)]
            ]
        ];
    }

    public function save(): bool 
    {
        $this->metadata = $this->metadata ? $this->metadata : null;
        return parent::save();
    }

    public function encode(): static 
    {
        $this->metadata = $this->metadata  
            ? (
                is_array($this->metadata) 
                ? json_encode($this->metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) 
                : $this->metadata
            )
            : null;
        return $this;
    }

    public function decode(): static 
    {
        $this->metadata = $this->metadata  
            ? (
                is_string($this->metadata) 
                ? json_decode($this->metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : $this->metadata
            ) 
            : null;
        return $this;
    }

    public function competitors(array $filters = [], string $columns = '*'): ?array 
    {
        $this->competitors = $this->hasMany(Competitor::class, 'cam_id', 'id', $filters, $columns)->fetch(true);
        return $this->competitors;
    }

    public function clashes(array $filters = [], string $columns = '*'): ?array 
    {
        $this->clashes = $this->hasMany(Clash::class, 'cam_id', 'id', $filters, $columns)->fetch(true);
        return $this->clashes;
    }

    public function game(string $columns = '*'): ?Game 
    {
        $this->game = $this->belongsTo(Game::class, 'jog_id', 'id', $columns)->fetch(false);
        return $this->game;
    }

    public function user(string $columns = '*'): ?User 
    {
        $this->user = $this->belongsTo(User::class, 'usu_id', 'id', $columns)->fetch(false);
        return $this->user;
    }

    public static function withCompetitors(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withHasMany(
            $objects, 
            Competition::class, 
            'cam_id', 
            'competitors', 
            'id', 
            $filters, 
            $columns
        );
    }

    public static function withClashes(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withHasMany(
            $objects, 
            Clash::class, 
            'cam_id', 
            'clashes', 
            'id', 
            $filters, 
            $columns
        );
    }

    public static function withGame(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withBelongsTo(
            $objects, 
            Game::class, 
            'jog_id', 
            'game', 
            'id', 
            $filters, 
            $columns
        );
    }

    public static function withUser(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withBelongsTo(
            $objects, 
            User::class, 
            'usu_id', 
            'user', 
            'id', 
            $filters, 
            $columns
        );
    }

    public function destroy(): bool 
    {
        if((new Competitor())->get(['cam_id' => $this->id])->count()) {
            $this->addError('destroy', _('Você não pode excluir um campeonato vinculado à um competidor!'));
            return false;
        } elseif((new Clash())->get(['cam_id' => $this->id])->count()) {
            $this->addError('destroy', _('Você não pode excluir um campeonato vinculado à um confronto!'));
            return false;
        }
        return parent::destroy();
    }

    public function getCreatedAtDateTime(): DateTime 
    {
        return new DateTime($this->created_at);
    }

    public function getUpdatedAtDateTime(): DateTime 
    {
        return new DateTime($this->updated_at);
    }

    public static function getStates(): array 
    {
        return [
            self::S_AWAITING => _('Aguardando'),
            self::S_IN_PROGRESS => _('Em Andamento'),
            self::S_FINISHED => _('Finalizado')
        ];
    }

    public function getStatus(): ?string 
    {
        return isset(self::getStates()[$this->c_status]) ? self::getStates()[$this->c_status] : null;
    }

    public static function getStatesColors(): array 
    {
        return [
            self::S_AWAITING => 'warning',
            self::S_IN_PROGRESS => 'primary',
            self::S_FINISHED => 'success'
        ];
    }

    public function getStatusColor(): ?string 
    {
        return isset(self::getStatesColors()[$this->c_status]) ? self::getStatesColors()[$this->c_status] : null;
    }

    public function createClashes(User $user): bool 
    {
        if(!$this->isAwaiting()) {
            $this->addError('create_clashes', _('Não é possível gerar confrontos para um campeonato que não está em espera!'));
            return false;
        } elseif(!$dbCompetitors = $this->competitors()) {
            $this->addError('create_clashes', _('Não é possível gerar confrontos para um campeonato sem competidores!'));
            return false;
        }

        shuffle($dbCompetitors);
        $chunks = array_chunk($dbCompetitors, 2);
        $log = log(count($chunks), 2);
        if(filter_var($log, FILTER_VALIDATE_INT) === false) {
            $this->addError('create_clashes', _('O número de competidores precisa ser potência de 2!'));
            return false;
        }

        $level = $log + 1;

        $dbClashes = [];
        foreach($chunks as $index => $chunk) {
            $dbClashes[] = (new Clash())->loadData([
                'usu_id' => $user->id,
                'cam_id' => $this->id,
                'level' => $level,
                'position' => $index + 1,
                'com1_id' => $chunk[0]->id,
                'com2_id' => $chunk[1]->id,
                'c_status' => Clash::S_READY
            ]);
        }

        $level--;
        while($level >= 1) {
            for($i = 1; $i <= 2 ** ($level - 1); $i++) {
                $dbClashes[] = (new Clash())->loadData([
                    'usu_id' => $user->id,
                    'cam_id' => $this->id,
                    'level' => $level,
                    'position' => $i,
                    'c_status' => Clash::S_UNDEFINED
                ]);
            }
            $level--;
        }

        if($this->clashes()) {
            Clash::deleteByObjects($this->clashes);
        }

        if(!$objects = Clash::insertMany($dbClashes)) {
            return false;
        }

        return true;
    }

    public function getCurrentClash(): ?Clash 
    {
        return (new Clash())->get([
            'cam_id' => $this->id,
            'c_status' => Clash::S_READY
        ])->order('level DESC, position ASC')->fetch(false);
    }

    public function getOrderedClashes(): ?array
    {
        return (new Clash())->get([
            'cam_id' => $this->id
        ])->order('level DESC, position ASC')->fetch(true);
    }

    public function getWinner(): ?Competitor 
    {
        $finalClash = (new Clash())->get([
            'cam_id' => $this->id,
            'level' => 1,
            'position' => 1
        ])->fetch(false);
        if(!$finalClash) {
            return null;
        } elseif(!$finalClash->isFinished()) {
            return null;
        }

        return $finalClash->clashWinner();
    }

    public function getBracketsData(): array
    {
        $bracketsData = [];
        $roundLabels = [];
        if($dbClashes = $this->getOrderedClashes()) {
            $dbClashes = Clash::withCompetitor1($dbClashes);
            $dbClashes = Clash::withCompetitor2($dbClashes);
            $dbCompetitor = $this->getWinner();
            foreach($dbClashes as $clash) {
                $groupedClashes[$clash->level][] = [
                    [
                        'id' => $clash->com1_id ?? ($clash->id . '-player-1'),
                        'name' => $clash->competitor1?->name ?? _('A Definir')
                    ],
                    [
                        'id' => $clash->com2_id ?? ($clash->id . '-player-2'),
                        'name' => $clash->competitor2?->name ?? _('A Definir')
                    ]
                ];
            }
            
            $groupedClashes[0][] = [
                [
                    'id' => $dbCompetitor ? $dbCompetitor->id : 'winner',
                    'name' => $dbCompetitor?->name ?? _('A Definir')
                ]
            ];

            foreach($groupedClashes as $level => $content) {
                if($level == 0) {

                } elseif($level == 1) {
                    $roundLabels[] = _('Grande Final');
                } elseif($level == 2) {
                    $roundLabels[] = _('Semifinais');
                } elseif($level == 3) {
                    $roundLabels[] = _('Quartas de Final');
                } elseif($level == 4) {
                    $roundLabels[] = _('Oitavas de Final');
                } else {
                    $roundLabels[] = sprintf(_('%sª de Final'), 2 ** ($level - 1));
                }

                $bracketsData[] = $content;
            }

            $roundLabels[] = _('Vencedor');
        }

        return [
            'bracketsData' => $bracketsData,
            'roundLabels' => $roundLabels
        ];
    }

    public function setAsAwaiting(): self 
    {
        $this->c_status = self::S_AWAITING;
        return $this;
    }

    public function setAsInProgress(): self 
    {
        $this->c_status = self::S_IN_PROGRESS;
        return $this;
    }

    public function setAsFinished(): self 
    {
        $this->c_status = self::S_FINISHED;
        return $this;
    }

    public function isAwaiting(): bool 
    {
        return $this->c_status == self::S_AWAITING;
    }

    public function isInProgress(): bool 
    {
        return $this->c_status == self::S_IN_PROGRESS;
    }

    public function isFinished(): bool 
    {
        return $this->c_status == self::S_FINISHED;
    }
}