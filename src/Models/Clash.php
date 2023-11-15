<?php

namespace Src\Models;

use DateTime;
use GTG\MVC\DB\DBModel;
use Src\Models\Championship;
use Src\Models\Competitor;
use Src\Models\User;

class Clash extends DBModel 
{
    const S_UNDEFINED = 1;
    const S_READY = 2;
    const S_FINISHED = 3;

    public ?Championship $championship = null;
    public ?Competitor $clashWinner = null;
    public ?Competitor $competitor1 = null;
    public ?Competitor $competitor2 = null;
    public ?User $user = null;

    public static function tableName(): string 
    {
        return 'confronto';
    }

    public static function primaryKey(): string 
    {
        return 'id';
    }

    public static function attributes(): array 
    {
        return [
            'usu_id',
            'cam_id',
            'level',
            'position',
            'com1_id',
            'com2_id',
            'c_status',
            'winner'
        ];
    }

    public function rules(): array 
    {
        return array_merge([
            'usu_id' => [
                [self::RULE_REQUIRED, 'message' => _('O usuário é obrigatório!')]
            ],
            'cam_id' => [
                [self::RULE_REQUIRED, 'message' => _('O campeonato é obrigatório!')]
            ],
            'level' => [
                [self::RULE_REQUIRED, 'message' => _('O nível do confronto é obrigatório!')]
            ],
            'position' => [
                [self::RULE_REQUIRED, 'message' => _('A posição do confronto é obrigatória!')]
            ],
            'c_status' => [
                [self::RULE_REQUIRED, 'message' => _('O status é obrigatório!')],
                [self::RULE_IN, 'values' => array_keys(self::getStates()), 'message' => _('O status é inválido!')]
            ]
        ], $this->isReady() || $this->isFinished() ? [
            'com1_id' => [
                [self::RULE_REQUIRED, 'message' => _('O primeiro competidor é obrigatório!')]
            ],
            'com2_id' => [
                [self::RULE_REQUIRED, 'message' => _('O segundo competidor é obrigatório!')]
            ]
        ] : [], $this->isFinished() ? [
            'winner' => [
                [self::RULE_REQUIRED, 'message' => _('O vencedor é obrigatório!')]
            ]
        ] : []);
    }

    public function save(): bool 
    {
        $this->com1_id = $this->com1_id ? $this->com1_id : null;
        $this->com2_id = $this->com2_id ? $this->com2_id : null;
        $this->winner = $this->isFinished() ? $this->winner : null;
        return parent::save();
    }

    public function destroy(): bool 
    {
        return parent::destroy();
    }

    public function championship(string $columns = '*'): ?Championship 
    {
        $this->championship = $this->belongsTo(Championship::class, 'cam_id', 'id', $columns)->fetch(false);
        return $this->championship;
    }

    public function competitor1(string $columns = '*'): ?Competitor 
    {
        $this->competitor1 = $this->belongsTo(Competitor::class, 'com1_id', 'id', $columns)->fetch(false);
        return $this->competitor1;
    }

    public function competitor2(string $columns = '*'): ?Competitor 
    {
        $this->competitor2 = $this->belongsTo(Competitor::class, 'com2_id', 'id', $columns)->fetch(false);
        return $this->competitor2;
    }

    public function user(string $columns = '*'): ?User 
    {
        $this->user = $this->belongsTo(User::class, 'usu_id', 'id', $columns)->fetch(false);
        return $this->user;
    }

    public function clashWinner(string $columns = '*'): ?Competitor 
    {
        $this->clashWinner = $this->belongsTo(Competitor::class, 'winner', 'id', $columns)->fetch(false);
        return $this->clashWinner;
    }

    public static function withChampionship(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withBelongsTo(
            $objects, 
            Championship::class, 
            'cam_id', 
            'championship', 
            'id', 
            $filters, 
            $columns
        );
    }

    public static function withCompetitor1(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withBelongsTo(
            $objects, 
            Competitor::class, 
            'com1_id', 
            'competitor1', 
            'id', 
            $filters, 
            $columns
        );
    }

    public static function withCompetitor2(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withBelongsTo(
            $objects, 
            Competitor::class, 
            'com2_id', 
            'competitor2', 
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

    public static function withClashWinner(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withBelongsTo(
            $objects, 
            Competitor::class, 
            'winner', 
            'clashWinner', 
            'id', 
            $filters, 
            $columns
        );
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
            self::S_UNDEFINED => _('Indefinido'),
            self::S_READY => _('Pronto'),
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
            self::S_UNDEFINED => 'secondary',
            self::S_READY => 'primary',
            self::S_FINISHED => 'success'
        ];
    }

    public function getStatusColor(): ?string 
    {
        return isset(self::getStatesColors()[$this->c_status]) ? self::getStatesColors()[$this->c_status] : null;
    }

    public function getClashName(): string
    {
        $text = '';
        if($this->level == 1) {
            $text .= _('Grande Final');
        } elseif($this->level == 2) {
            $text .= _('Semifinais');
        } elseif($this->level == 3) {
            $text .= _('Quartas de final');
        } elseif($this->level == 4) {
            $text .= _('Oitavas de final');
        } else {
            $text .= sprintf(_('%sª de final'), 2 ** ($this->level - 1));
        }

        if($this->level > 1) {
            $text .= ' - ' . sprintf(_('%s° Confronto'), $this->position);
        }

        return $text;
    }

    public function getNextClash(): ?Clash
    {
        return (new self())->get([
            'cam_id' => $this->cam_id,
            'level' => $this->level - 1,
            'position' => ceil($this->position / 2)
        ])->fetch(false);
    }

    public function setAsUndefined(): self 
    {
        $this->c_status = self::S_UNDEFINED;
        return $this;
    }

    public function setAsReady(): self 
    {
        $this->c_status = self::S_READY;
        return $this;
    }

    public function setAsFinished(): self 
    {
        $this->c_status = self::S_FINISHED;
        return $this;
    }

    public function isUndefined(): bool 
    {
        return $this->c_status == self::S_UNDEFINED;
    }

    public function isReady(): bool 
    {
        return $this->c_status == self::S_READY;
    }

    public function isFinished(): bool 
    {
        return $this->c_status == self::S_FINISHED;
    }
}