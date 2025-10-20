<?php

namespace Database\Factories;

use App\Models\Gener21;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MenuPermission>
 */
class MenuPermissionFactory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'menu_item_id' => $this->faker->numberBetween(1, 2000),
            'tipfun' => Gener21::all()->random()->tipfun,
            'can_view' => $this->faker->boolean(90) ? 1 : 0,
        ];
    }

    /**
     * Estado para forzar visibilidad.
     */
    public function viewable(): self
    {
        return $this->state(fn() => ['can_view' => 1]);
    }

    /**
     * Estado para ocultar permiso de vista.
     */
    public function hidden(): self
    {
        return $this->state(fn() => ['can_view' => 0]);
    }
}
