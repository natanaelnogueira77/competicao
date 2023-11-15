<?php

namespace Src\Models;

use DateTime;
use GTG\MVC\DB\DBModel;
use Src\Models\Championship;
use Src\Models\User;

class Game extends DBModel 
{
    public ?array $championships = null;
    public ?User $user = null;

    public static function tableName(): string 
    {
        return 'jogo';
    }

    public static function primaryKey(): string 
    {
        return 'id';
    }

    public static function attributes(): array 
    {
        return [
            'usu_id',
            'name'
        ];
    }

    public function rules(): array 
    {
        return [
            'usu_id' => [
                [self::RULE_REQUIRED, 'message' => _('O usuário é obrigatório!')]
            ],
            'name' => [
                [self::RULE_REQUIRED, 'message' => _('O nome é obrigatório!')],
                [self::RULE_MAX, 'max' => 100, 'message' => sprintf(_('O nome deve conter no máximo %s caractéres!'), 100)]
            ]
        ];
    }

    public function championships(array $filters = [], string $columns = '*'): ?array 
    {
        $this->championships = $this->hasMany(Championship::class, 'jog_id', 'id', $filters, $columns)->fetch(true);
        return $this->championships;
    }

    public function user(string $columns = '*'): ?User 
    {
        $this->user = $this->belongsTo(User::class, 'usu_id', 'id', $columns)->fetch(false);
        return $this->user;
    }

    public static function withChampionships(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withHasMany(
            $objects, 
            Championship::class, 
            'jog_id', 
            'championships', 
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
        if((new Championship())->get(['jog_id' => $this->id])->count()) {
            $this->addError('destroy', _('Você não pode excluir um jogo vinculado à um campeonato!'));
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
}