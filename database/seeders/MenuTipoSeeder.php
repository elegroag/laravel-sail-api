<?php

namespace Database\Seeders;

use App\Models\MenuTipo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuTipoSeeder extends Seeder
{
    use WithoutModelEvents;

    private const TABLE = 'menu_tipos';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->limpiarTabla();
            $menu_caja = [
                [
                    'id' => 1,
                    'position' => 1,
                    'is_visible' => 1,
                    'tipo' => 'A',
                    'menu_item' => 1
                ],
                [
                    'id' => 2,
                    'is_visible' => 1,
                    'position' => 7,
                    'tipo' => 'A',
                    'menu_item' => 2
                ],
                [
                    'id' => 3,
                    'position' => 3,
                    'is_visible' => 1,
                    'tipo' => 'A',
                    'menu_item' => 3
                ],
                [
                    'id' => 4,
                    'position' => 4,
                    'is_visible' => 1,
                    'tipo' => 'A',
                    'menu_item' => 4
                ],
                [
                    'id' => 5,
                    'position' => 5,
                    'is_visible' => 1,
                    'tipo' => 'A',
                    'menu_item' => 5
                ],
                [
                    'id' => 6,
                    'position' => 6,
                    'is_visible' => 1,
                    'tipo' => 'A',
                    'menu_item' => 6
                ],
                [
                    'id' => 7,
                    'position' => 7,
                    'is_visible' => 1,
                    'tipo' => 'A',
                    'menu_item' => 7
                ],
                [
                    'id' => 8,
                    'position' => 8,
                    'is_visible' => 1,
                    'tipo' => 'A',
                    'menu_item' => 8
                ],
                [
                    'id' => 9,
                    'position' => 9,
                    'is_visible' => 1,
                    'tipo' => 'A',
                    'menu_item' => 9
                ],
                [
                    'id' => 10,
                    'position' => 10,
                    'is_visible' => 1,
                    'tipo' => 'A',
                    'menu_item' => 10
                ],
                [
                    'id' => 11,
                    'position' => 11,
                    'is_visible' => 1,
                    'tipo' => 'A',
                    'menu_item' => 11
                ],
                [
                    'id' => 12,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 12
                ],
                [
                    'id' => 13,
                    'position' => '2',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 13
                ],
                [
                    'id' => 14,
                    'position' => '3',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 14
                ],
                [
                    'id' => 15,
                    'position' => '4',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 15
                ],
                [
                    'id' => 16,
                    'position' => '5',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 16
                ],
                [
                    'id' => 17,
                    'position' => '6',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 17
                ],
                [
                    'id' => 18,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 18
                ],
                [
                    'id' => 19,
                    'position' => '2',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 19
                ],
                [
                    'id' => 20,
                    'position' => '3',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 20
                ],
                [
                    'id' => 21,
                    'position' => '4',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 21
                ],
                [
                    'id' => 22,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 22
                ],
                [
                    'id' => 23,
                    'position' => '2',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 23
                ],
                [
                    'id' => 24,
                    'position' => '3',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 24
                ],
                [
                    'id' => 25,
                    'position' => '4',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 25
                ],
                [
                    'id' => 26,
                    'position' => '5',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 26
                ],
                [
                    'id' => 28,
                    'position' => '7',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 28
                ],
                [
                    'id' => 29,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 29
                ],
                [
                    'id' => 30,
                    'position' => '2',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 30
                ],
                [
                    'id' => 31,
                    'position' => '3',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 31
                ],
                [
                    'id' => 32,
                    'position' => '4',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 32
                ],
                [
                    'id' => 33,
                    'position' => '5',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 33
                ],
                [
                    'id' => 34,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 34
                ],
                [
                    'id' => 35,
                    'position' => '2',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 35
                ],
                [
                    'id' => 36,
                    'position' => '3',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 36
                ],
                [
                    'id' => 37,
                    'position' => '4',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 37
                ],
                [
                    'id' => 38,
                    'position' => '5',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 38
                ],
                [
                    'id' => 39,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 39
                ],
                [
                    'id' => 40,
                    'position' => '2',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 40
                ],
                [
                    'id' => 41,
                    'position' => '3',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 41
                ],
                [
                    'id' => 42,
                    'position' => '4',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 42
                ],
                [
                    'id' => 43,
                    'position' => '5',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 43
                ],
                [
                    'id' => 44,
                    'position' => '6',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 44
                ],
                [
                    'id' => 45,
                    'position' => '7',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 45
                ],
                [
                    'id' => 46,
                    'position' => '8',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 46
                ],
                [
                    'id' => 47,
                    'position' => '9',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 47
                ],
                [
                    'id' => 48,
                    'position' => '10',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 48
                ],
                [
                    'id' => 49,
                    'position' => '11',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 49
                ],
                [
                    'id' => 50,
                    'position' => '12',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 50
                ],
                [
                    'id' => 51,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 51
                ],
                [
                    'id' => 52,
                    'position' => '2',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 52
                ],
                [
                    'id' => 53,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 53
                ],
                [
                    'id' => 54,
                    'position' => '6',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 54
                ],
                [
                    'id' => 55,
                    'position' => '6',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 55
                ],
                [
                    'id' => 56,
                    'position' => '10',
                    'is_visible' => '1',
                    'tipo' => 'A',
                    'menu_item' => 56
                ],
            ];

            $menu_mercurio = [
                [
                    'id' => 178,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 178
                ],
                [
                    'id' => 179,
                    'position' => '10',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 179
                ],
                [
                    'id' => 180,
                    'position' => '9',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 180
                ],
                [
                    'id' => 181,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 181
                ],
                [
                    'id' => 182,
                    'position' => '4',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 182
                ],
                [
                    'id' => 183,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 183
                ],
                [
                    'id' => 184,
                    'position' => '2',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 184
                ],
                [
                    'id' => 185,
                    'position' => '3',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 185
                ],
                [
                    'id' => 186,
                    'position' => '4',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 186
                ],
                [
                    'id' => 187,
                    'position' => '5',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 187
                ],
                [
                    'id' => 188,
                    'position' => '5',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 188
                ],
                [
                    'id' => 189,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 189
                ],
                [
                    'id' => 190,
                    'position' => '3',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 190
                ],
                [
                    'id' => 191,
                    'position' => '4',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 191
                ],
                [
                    'id' => 192,
                    'position' => '5',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 192
                ],
                [
                    'id' => 193,
                    'position' => '6',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 193
                ],
                [
                    'id' => 194,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 194
                ],
                [
                    'id' => 195,
                    'position' => '7',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 195
                ],
                [
                    'id' => 196,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 196
                ],
                [
                    'id' => 197,
                    'position' => '2',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 197
                ],
                [
                    'id' => 198,
                    'position' => '3',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 198
                ],
                [
                    'id' => 199,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 178
                ],
                [
                    'id' => 200,
                    'position' => '6',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 200
                ],
                [
                    'id' => 201,
                    'position' => '10',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 179
                ],
                [
                    'id' => 202,
                    'position' => '4',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 202
                ],
                [
                    'id' => 203,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 203
                ],
                [
                    'id' => 204,
                    'position' => '2',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 204
                ],
                [
                    'id' => 205,
                    'position' => '3',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 205
                ],
                [
                    'id' => 206,
                    'position' => '4',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 206
                ],
                [
                    'id' => 207,
                    'position' => '5',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 207
                ],
                [
                    'id' => 208,
                    'position' => '5',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 208
                ],
                [
                    'id' => 209,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 209
                ],
                [
                    'id' => 210,
                    'position' => '2',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 210
                ],
                [
                    'id' => 211,
                    'position' => '3',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 190
                ],
                [
                    'id' => 212,
                    'position' => '4',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 191
                ],
                [
                    'id' => 213,
                    'position' => '6',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 213
                ],
                [
                    'id' => 214,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 214
                ],
                [
                    'id' => 215,
                    'position' => '2',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 215
                ],
                [
                    'id' => 216,
                    'position' => '7',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 216
                ],
                [
                    'id' => 217,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 196
                ],
                [
                    'id' => 218,
                    'position' => '2',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 218
                ],
                [
                    'id' => 219,
                    'position' => '3',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 198
                ],
                [
                    'id' => 220,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'P',
                    'menu_item' => 178
                ],
                [
                    'id' => 221,
                    'position' => '10',
                    'is_visible' => '1',
                    'tipo' => 'P',
                    'menu_item' => 179
                ],
                [
                    'id' => 222,
                    'position' => '3',
                    'is_visible' => '1',
                    'tipo' => 'P',
                    'menu_item' => 208
                ],
                [
                    'id' => 223,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'P',
                    'menu_item' => 223
                ],
                [
                    'id' => 224,
                    'position' => '2',
                    'is_visible' => '1',
                    'tipo' => 'P',
                    'menu_item' => 224
                ],
                [
                    'id' => 225,
                    'position' => '3',
                    'is_visible' => '1',
                    'tipo' => 'P',
                    'menu_item' => 225
                ],
                [
                    'id' => 226,
                    'position' => '4',
                    'is_visible' => '1',
                    'tipo' => 'P',
                    'menu_item' => 226
                ],
                [
                    'id' => 227,
                    'position' => '4',
                    'is_visible' => '1',
                    'tipo' => 'P',
                    'menu_item' => 227
                ],
                [
                    'id' => 228,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'P',
                    'menu_item' => 196
                ],
                [
                    'id' => 229,
                    'position' => '2',
                    'is_visible' => '1',
                    'tipo' => 'P',
                    'menu_item' => 229
                ],
                [
                    'id' => 230,
                    'position' => '3',
                    'is_visible' => '1',
                    'tipo' => 'P',
                    'menu_item' => 198
                ],
                [
                    'id' => 231,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'F',
                    'menu_item' => 178
                ],
                [
                    'id' => 232,
                    'position' => '10',
                    'is_visible' => '1',
                    'tipo' => 'F',
                    'menu_item' => 179
                ],
                [
                    'id' => 233,
                    'position' => '3',
                    'is_visible' => '1',
                    'tipo' => 'F',
                    'menu_item' => 233
                ],
                [
                    'id' => 234,
                    'position' => '3',
                    'is_visible' => '1',
                    'tipo' => 'F',
                    'menu_item' => 234
                ],
                [
                    'id' => 235,
                    'position' => '4',
                    'is_visible' => '1',
                    'tipo' => 'F',
                    'menu_item' => 235
                ],
                [
                    'id' => 236,
                    'position' => '4',
                    'is_visible' => '1',
                    'tipo' => 'F',
                    'menu_item' => 236
                ],
                [
                    'id' => 237,
                    'position' => '1',
                    'is_visible' => '1',
                    'tipo' => 'F',
                    'menu_item' => 196
                ],
                [
                    'id' => 238,
                    'position' => '2',
                    'is_visible' => '1',
                    'tipo' => 'F',
                    'menu_item' => 229
                ],
                [
                    'id' => 239,
                    'position' => '3',
                    'is_visible' => '1',
                    'tipo' => 'F',
                    'menu_item' => 198
                ],
                [
                    'id' => 240,
                    'position' => '2',
                    'is_visible' => '1',
                    'tipo' => 'E',
                    'menu_item' => 240
                ],
                [
                    'id' => 241,
                    'position' => '2',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 210
                ],
                [
                    'id' => 242,
                    'position' => '3',
                    'is_visible' => '1',
                    'tipo' => 'T',
                    'menu_item' => 208
                ]
            ];

            foreach ($menu_mercurio as $item1) {
                MenuTipo::create($item1);
            }

            foreach ($menu_caja as $item2) {
                MenuTipo::create($item2);
            }
        });
    }

    /**
     * Elimina los registros existentes para permitir re-ejecuciones idempotentes.
     */
    protected function limpiarTabla(): void
    {
        DB::statement(sprintf('DELETE FROM %s', self::TABLE));
    }
}
