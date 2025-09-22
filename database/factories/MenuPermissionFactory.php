<?php

namespace Database\Factories;

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
            'role_id' => $this->faker->numberBetween(1, 50),
            'can_view' => $this->faker->boolean(90) ? 1 : 0,
        ];
    }

    /**
     * Estado para forzar visibilidad.
     */
    public function viewable(): self
    {
        return $this->state(fn () => ['can_view' => 1]);
    }

    /**
     * Estado para ocultar permiso de vista.
     */
    public function hidden(): self
    {
        return $this->state(fn () => ['can_view' => 0]);
    }
}
