<?php

use App\Models\CategoryGroup;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $techGroup = CategoryGroup::create(['name' => 'Tecnología']);
        $homeGroup = CategoryGroup::create(['name' => 'Hogar y Jardín']);
        $fashionGroup = CategoryGroup::create(['name' => 'Moda']);
        $sportsGroup = CategoryGroup::create(['name' => 'Deportes']);

        $smartphones = Category::create(['name' => 'Smartphones', 'category_group_id' => $techGroup->id]);
        $computers = Category::create(['name' => 'Computadoras', 'category_group_id' => $techGroup->id]);
        $furniture = Category::create(['name' => 'Muebles', 'category_group_id' => $homeGroup->id]);
        $kitchen = Category::create(['name' => 'Cocina', 'category_group_id' => $homeGroup->id]);
        $clothing = Category::create(['name' => 'Ropa', 'category_group_id' => $fashionGroup->id]);
        $shoes = Category::create(['name' => 'Calzado', 'category_group_id' => $fashionGroup->id]);
        $fitness = Category::create(['name' => 'Fitness', 'category_group_id' => $sportsGroup->id]);
        $outdoor = Category::create(['name' => 'Exterior', 'category_group_id' => $sportsGroup->id]);

        $products = [
            [
                'name' => 'iPhone 20 Ultra',
                'description' => 'El mejor iPhone del mundo',
                'image_url' => 'products/1/image_url/iphone.jpg',
                'cta_url' => 'https://www.apple.com/',
                'categories' => [$smartphones->id],
            ],
            [
                'name' => 'Samsung Galaxy Z',
                'description' => 'El mejor Smartphone del mundo',
                'image_url' => null,
                'cta_url' => 'https://www.samsung.com/',
                'categories' => [$smartphones->id, $computers->id],
            ],
            [
                'name' => 'Sofá Moderno',
                'description' => 'Un cómodo sofá para el hogar',
                'image_url' => null,
                'cta_url' => 'https://www.furniturestore.com/',
                'categories' => [$furniture->id],
            ],
            [
                'name' => 'Set de Cuchillos de Cocina',
                'description' => 'Cuchillos de acero inoxidable para chef',
                'image_url' => null,
                'cta_url' => 'https://www.kitchenware.com/',
                'categories' => [$kitchen->id],
            ],
            [
                'name' => 'Zapatillas de Running',
                'description' => 'Zapatillas ligeras y cómodas para correr',
                'image_url' => null,
                'cta_url' => 'https://www.shoes.com/',
                'categories' => [$shoes->id, $fitness->id],
            ],
            [
                'name' => 'Camiseta Deportiva',
                'description' => 'Camiseta transpirable para actividades deportivas',
                'image_url' => null,
                'cta_url' => 'https://www.sportswear.com/',
                'categories' => [$clothing->id, $fitness->id],
            ],
            [
                'name' => 'Tienda de Campaña',
                'description' => 'Ideal para tus aventuras al aire libre',
                'image_url' => null,
                'cta_url' => 'https://www.outdoor.com/',
                'categories' => [$outdoor->id],
            ],
            [
                'name' => 'Laptop Dell XPS',
                'description' => 'Laptop de alta gama para profesionales',
                'image_url' => null,
                'cta_url' => 'https://www.dell.com/',
                'categories' => [$computers->id],
            ],
            [
                'name' => 'Vestido de Verano',
                'description' => 'Vestido ligero perfecto para el verano',
                'image_url' => null,
                'cta_url' => 'https://www.fashionstore.com/',
                'categories' => [$clothing->id],
            ],
            [
                'name' => 'Mancuernas Ajustables',
                'description' => 'Ideales para entrenamientos en casa',
                'image_url' => null,
                'cta_url' => 'https://www.fitnessgear.com/',
                'categories' => [$fitness->id],
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create([
                'name' => $productData['name'],
                'description' => $productData['description'],
                'image_url' => $productData['image_url'],
                'cta_url' => $productData['cta_url'],
            ]);

            $product->categories()->attach($productData['categories']);
        }
    }
}
