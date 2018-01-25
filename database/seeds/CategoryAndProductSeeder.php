<?php

use Illuminate\Database\Seeder;

class CategoryAndProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // make 3 top categories
        $topCategories = factory(\App\Models\Category::class, 3)->create()
            ->each(function ($category) {
                $category->saveAsRoot();
            });

        // make sub top categories
        foreach ($topCategories as $topCategory) {
            factory(\App\Models\Category::class, 5)->create()
                ->each(function ($category) use ($topCategory) {
                    $category->appendToNode($topCategory)->save();
                    // insert products into category
                    $category->products()->saveMany(factory(\App\Models\Product::class, 5)->make());
                });
        }
    }
}
