<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MenuItem>
 */
class MenuItemFactory extends Factory
{
    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Título y URL opcional
        $title = $this->faker->unique()->words(2, true);
        $defaultUrl = $this->faker->optional()->url();
        $icon = $this->faker->optional()->randomElement(['home', 'user', 'settings', 'file', 'lock', 'mail']);
        $color = 'text-primary';
        $nota = $this->faker->optional()->sentence(8);

        return [
            'title' => ucwords($title),
            'default_url' => $defaultUrl,
            'icon' => $icon,
            'color' => $color,
            'nota' => $nota,
            'position' => $this->faker->numberBetween(1, 5000),
            'parent_id' => $this->faker->optional()->numberBetween(1, 2000),
            'is_visible' => $this->faker->boolean(90) ? 1 : 0,
            'codapl' => 'CA', // por defecto
            'tipo' => 'A', // por defecto
        ];
    }

    /**
     * Estado para ocultar el ítem.
     */
    public function hidden(): self
    {
        return $this->state(fn () => ['is_visible' => 0]);
    }
}
