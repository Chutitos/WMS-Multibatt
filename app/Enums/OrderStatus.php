<?php

namespace App\Enums;

enum OrderStatus: string
{
    case CREADO = 'creado';
    case LIBERADO = 'liberado';
    case PREPARANDO = 'preparando';
    case LISTO = 'listo';
    case ENTREGADO = 'entregado';
    case CANCELADO = 'cancelado';

    public function label(): string
    {
        return match ($this) {
            self::CREADO => 'Creado',
            self::LIBERADO => 'Liberado',
            self::PREPARANDO => 'Preparando',
            self::LISTO => 'Listo',
            self::ENTREGADO => 'Entregado',
            self::CANCELADO => 'Cancelado',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::CREADO => 'bg-slate-100 text-slate-700',
            self::LIBERADO => 'bg-blue-100 text-blue-800',
            self::PREPARANDO => 'bg-orange-100 text-orange-800',
            self::LISTO => 'bg-green-100 text-green-800',
            self::ENTREGADO => 'bg-emerald-100 text-emerald-800',
            self::CANCELADO => 'bg-red-100 text-red-800',
        };
    }

    /**
     * Estados válidos a los que se puede transicionar desde este estado.
     *
     * @return list<self>
     */
    public function nextStates(): array
    {
        return match ($this) {
            self::CREADO => [self::LIBERADO, self::CANCELADO],
            self::LIBERADO => [self::PREPARANDO, self::CANCELADO],
            self::PREPARANDO => [self::LISTO, self::CANCELADO],
            self::LISTO => [self::ENTREGADO, self::CANCELADO],
            self::ENTREGADO, self::CANCELADO => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->nextStates(), true);
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
