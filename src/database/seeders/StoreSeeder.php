<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Gạo & Ngũ cốc', 'slug' => 'gao-ngu-coc'],
            ['name' => 'Trái cây & Rau củ', 'slug' => 'trai-cay-rau-cu'],
            ['name' => 'Đặc sản vùng miền', 'slug' => 'dac-san-vung-mien'],
            ['name' => 'Quà tặng', 'slug' => 'qua-tang'],
        ];

        foreach ($categories as $i => $cat) {
            Category::query()->updateOrCreate(
                ['slug' => $cat['slug']],
                ['name' => $cat['name'], 'sort_order' => $i + 1],
            );
        }

        $products = [
            [
                'name' => 'Gạo ST25 túi 5kg',
                'slug' => 'gao-st25-tui-5kg',
                'sku' => 'GAO-ST25-5',
                'category' => 'gao-ngu-coc',
                'price' => 185000,
                'sale_price' => 175000,
                'image' => 'images/store/product-rice.jpg',
                'featured' => true,
                'variants' => [
                    ['flavor' => null, 'size' => '5kg', 'price' => 175000],
                    ['flavor' => null, 'size' => '10kg', 'price' => 340000],
                ],
            ],
            [
                'name' => 'Mật ong rừng nguyên chất 500ml',
                'slug' => 'mat-ong-rung-500ml',
                'sku' => 'MO-500',
                'category' => 'dac-san-vung-mien',
                'price' => 220000,
                'image' => 'images/store/product-honey.jpg',
                'featured' => true,
                'variants' => [
                    ['flavor' => 'Nguyên chất', 'size' => '500ml', 'price' => 220000],
                    ['flavor' => 'Nguyên chất', 'size' => '1 lít', 'price' => 400000],
                ],
            ],
            [
                'name' => 'Chè lam Thạch Thất',
                'slug' => 'che-lam-thach-that',
                'sku' => 'CL-TT',
                'category' => 'dac-san-vung-mien',
                'price' => 85000,
                'image' => 'images/store/product-snack.jpg',
                'featured' => true,
                'variants' => [
                    ['flavor' => 'Vừng', 'size' => 'Hộp 300g', 'price' => 85000],
                    ['flavor' => 'Lạc', 'size' => 'Hộp 300g', 'price' => 85000],
                ],
            ],
            [
                'name' => 'Bưởi da xanh Hương Lai',
                'slug' => 'buoi-da-xanh-huong-lai',
                'sku' => 'BXL-HL',
                'category' => 'trai-cay-rau-cu',
                'price' => 65000,
                'image' => 'images/store/product-fruit.jpg',
                'featured' => true,
            ],
            [
                'name' => 'Rau sạch combo tuần',
                'slug' => 'rau-sach-combo-tuan',
                'sku' => 'RAU-CB',
                'category' => 'trai-cay-rau-cu',
                'price' => 120000,
                'image' => 'images/store/product-vegetables.jpg',
                'featured' => true,
            ],
            [
                'name' => 'Set quà tết Nông Sản TTC',
                'slug' => 'set-qua-tet-nong-san-ttc',
                'sku' => 'QT-TET',
                'category' => 'qua-tang',
                'price' => 450000,
                'sale_price' => 399000,
                'image' => 'images/store/product-gift.jpg',
                'featured' => true,
            ],
        ];

        foreach ($products as $data) {
            $category = Category::where('slug', $data['category'])->first();

            $product = Product::query()->updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'category_id' => $category?->id,
                    'name' => $data['name'],
                    'sku' => $data['sku'],
                    'short_description' => 'Sản phẩm nông sản sạch, nguồn gốc rõ ràng, đóng gói cẩn thận.',
                    'description' => '<p>Sản phẩm được tuyển chọn từ vùng nguyên liệu đạt chuẩn, quy trình sản xuất khép kín, đảm bảo chất lượng và an toàn thực phẩm.</p><p>Phù hợp làm quà biếu hoặc sử dụng hàng ngày cho gia đình.</p>',
                    'price' => $data['price'],
                    'sale_price' => $data['sale_price'] ?? null,
                    'image' => $data['image'],
                    'gallery' => [$data['image']],
                    'is_featured' => $data['featured'] ?? false,
                    'is_active' => true,
                    'stock' => 100,
                ],
            );

            if (! empty($data['variants'])) {
                $product->variants()->delete();
                foreach ($data['variants'] as $v) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'flavor' => $v['flavor'],
                        'size' => $v['size'],
                        'price' => $v['price'],
                        'stock' => 50,
                    ]);
                }
            }
        }

        $posts = [
            [
                'title' => 'Bí quyết chọn gạo ST25 chuẩn vị Sóc Trăng',
                'slug' => 'bi-quyet-chon-gao-st25',
                'excerpt' => 'Gạo ST25 nổi tiếng với hương thơm lá dứa đặc trưng. Cùng TTC tìm hiểu cách chọn gạo ngon, tránh hàng kém chất lượng.',
                'image' => 'images/store/product-rice.jpg',
            ],
            [
                'title' => '5 món quà nông sản ý nghĩa dịp Tết',
                'slug' => '5-mon-qua-nong-san-tet',
                'excerpt' => 'Set quà nông sản sạch vừa thể hiện tấm lòng, vừa góp phần lan tỏa đặc sản Việt đến người thân và đối tác.',
                'image' => 'images/store/product-gift.jpg',
            ],
            [
                'title' => 'Hành trình nông sản sạch từ nông trại đến bàn ăn',
                'slug' => 'hanh-trinh-nong-san-sach',
                'excerpt' => 'TTC hợp tác trực tiếp với hộ nông dân, kiểm soát chất lượng từ khâu thu hoạch đến đóng gói và vận chuyển.',
                'image' => 'images/store/post-farm.jpg',
            ],
        ];

        foreach ($posts as $i => $post) {
            Post::query()->updateOrCreate(
                ['slug' => $post['slug']],
                [
                    'title' => $post['title'],
                    'excerpt' => $post['excerpt'],
                    'content' => '<p>' . $post['excerpt'] . '</p>',
                    'image' => $post['image'],
                    'is_published' => true,
                    'published_at' => now()->subDays($i + 1),
                ],
            );
        }

        Banner::query()->delete();

        Banner::create([
            'title' => 'Món quà vàng từ đồng quê',
            'subtitle' => 'Nông sản sạch — gửi trọn tình thân',
            'image' => 'images/store/banner-farm.jpg',
            'link' => '/san-pham',
            'position' => 'home_cta',
            'sort_order' => 1,
        ]);

        Banner::create([
            'title' => 'Trở thành đại lý',
            'subtitle' => 'Lan tỏa đặc sản nông sản đến mọi miền',
            'image' => 'images/store/banner-partner.jpg',
            'link' => '#',
            'position' => 'home_cta',
            'sort_order' => 2,
        ]);
    }
}
