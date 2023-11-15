<?php

namespace Src\Models;

use DateTime;
use GTG\MVC\DB\DBModel;
use Src\Models\Championship;
use Src\Models\Clash;
use Src\Models\User;

class Competitor extends DBModel 
{
    public ?Championship $championship = null;
    public ?User $user = null;

    public static function tableName(): string 
    {
        return 'competidor';
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
            'name',
            'img'
        ];
    }

    public function rules(): array 
    {
        return [
            'usu_id' => [
                [self::RULE_REQUIRED, 'message' => _('O usuário é obrigatório!')]
            ],
            'cam_id' => [
                [self::RULE_REQUIRED, 'message' => _('O campeonato é obrigatório!')]
            ],
            'name' => [
                [self::RULE_REQUIRED, 'message' => _('O nome é obrigatório!')],
                [self::RULE_MAX, 'max' => 100, 'message' => sprintf(_('O nome deve conter no máximo %s caractéres!'), 100)]
            ],
            self::RULE_RAW => [
                function ($model) {
                    if($model->img) {
                        if(!in_array(pathinfo($model->img, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png'])) {
                            $model->addError(self::KEY_LOGIN_IMG, _('O arquivo escolhido não é uma imagem válida!'));
                        }
                    }
                }
            ]
        ];
    }

    public function save(): bool 
    {
        $this->img = $this->img ? $this->img : null;
        return parent::save();
    }

    public function destroy(): bool 
    {
        if((new Clash())->get(['com1_id' => $this->id])->count()) {
            $this->addError('destroy', _('Você não pode excluir um competidor vinculado à um confronto!'));
            return false;
        } elseif((new Clash())->get(['com2_id' => $this->id])->count()) {
            $this->addError('destroy', _('Você não pode excluir um competidor vinculado à um confronto!'));
            return false;
        }
        return parent::destroy();
    }

    public function championship(string $columns = '*'): ?Championship 
    {
        $this->championship = $this->belongsTo(Championship::class, 'cam_id', 'id', $columns)->fetch(false);
        return $this->championship;
    }

    public function user(string $columns = '*'): ?User 
    {
        $this->user = $this->belongsTo(User::class, 'usu_id', 'id', $columns)->fetch(false);
        return $this->user;
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

    public function getCreatedAtDateTime(): DateTime 
    {
        return new DateTime($this->created_at);
    }

    public function getUpdatedAtDateTime(): DateTime 
    {
        return new DateTime($this->updated_at);
    }

    public function getImageURL(): string 
    {
        return $this->img ? url($this->img) : 'https://www.gravatar.com/avatar/';
    }
}