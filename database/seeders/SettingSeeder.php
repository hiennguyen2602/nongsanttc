<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['group' => 'general', 'key' => 'name', 'value' => 'Nông Sản TTC', 'type' => 'text', 'label' => 'Tên website'],
            ['group' => 'general', 'key' => 'tagline', 'value' => 'Đặc sản nông sản sạch — từ đồng quê đến bàn ăn', 'type' => 'text', 'label' => 'Slogan'],
            ['group' => 'seo', 'key' => 'google_site_verification', 'value' => 'GcT3ffASQndjwwm7rjZZPF8XkKrOQYhQZ4o31URbe-g', 'type' => 'text', 'label' => 'Google Search Console (mã xác minh)'],
            ['group' => 'seo', 'key' => 'about_meta_description', 'value' => 'Nông Sản TTC mang đến nông sản sạch, đặc sản Việt Nam và thực phẩm chất lượng cao. Cam kết nguồn gốc minh bạch, an toàn và uy tín đến từng khách hàng.', 'type' => 'textarea', 'label' => 'Meta mô tả — Về chúng tôi'],
            ['group' => 'seo', 'key' => 'contact_meta_description', 'value' => 'Liên hệ Nông Sản TTC để được tư vấn, báo giá và hỗ trợ đặt hàng nông sản sạch, đặc sản Việt Nam. Phản hồi nhanh chóng và tận tâm.', 'type' => 'textarea', 'label' => 'Meta mô tả — Liên hệ'],
            ['group' => 'contact', 'key' => 'company_name', 'value' => 'Công ty TNHH sản xuất và chế biến nông sản TTC', 'type' => 'text', 'label' => 'Tên công ty'],
            ['group' => 'contact', 'key' => 'phone', 'value' => '0369114096', 'type' => 'text', 'label' => 'Số điện thoại'],
            ['group' => 'contact', 'key' => 'email', 'value' => 'info@nongsanttc.com', 'type' => 'text', 'label' => 'Email'],
            ['group' => 'contact', 'key' => 'address', 'value' => 'Xã Thần Khê, Tỉnh Hưng Yên', 'type' => 'textarea', 'label' => 'Địa chỉ'],
            ['group' => 'contact', 'key' => 'google_maps_url', 'value' => 'https://www.google.com/maps/place/Ch%E1%BB%A3+G%E1%BB%91c+Gi%C6%A1/@20.5797879,106.2689266,17z/data=!3m1!4b1!4m6!3m5!1s0x3135efe80c3a8c37:0x279109c135ee6f0a!8m2!3d20.5797829!4d106.2715015!16s%2Fg%2F11hgh_2sk6?entry=ttu&g_ep=EgoyMDI2MDYxMC4wIKXMDSoASAFQAw%3D%3D', 'type' => 'url', 'label' => 'Google Maps URL'],
            ['group' => 'contact', 'key' => 'google_maps_embed', 'value' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3735.2024618895634!2d106.26892657692629!3d20.57978790304463!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135efe80c3a8c37%3A0x279109c135ee6f0a!2zQ2jhu6MgR-G7kWMgR2nGoQ!5e0!3m2!1svi!2s!4v1781413625703!5m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>', 'type' => 'textarea', 'label' => 'Google Maps Embed iframe'],
            ['group' => 'social', 'key' => 'zalo', 'value' => 'https://zalo.me/0369114096', 'type' => 'url', 'label' => 'Zalo'],
            ['group' => 'social', 'key' => 'facebook', 'value' => 'https://www.facebook.com/profile.php?id=61578921213625', 'type' => 'url', 'label' => 'Facebook'],
            ['group' => 'social', 'key' => 'messenger', 'value' => 'https://m.me/61578921213625', 'type' => 'url', 'label' => 'Messenger'],
            ['group' => 'social', 'key' => 'youtube', 'value' => '', 'type' => 'url', 'label' => 'Youtube'],
            ['group' => 'social', 'key' => 'tiktok', 'value' => '', 'type' => 'url', 'label' => 'TikTok'],
            ['group' => 'banner', 'key' => 'hero_desktop', 'value' => '', 'type' => 'image', 'label' => 'Banner header (Desktop)'],
            ['group' => 'banner', 'key' => 'hero_mobile', 'value' => '', 'type' => 'image', 'label' => 'Banner header (Mobile)'],
            ['group' => 'banner', 'key' => 'about_main', 'value' => '', 'type' => 'image', 'label' => 'Ảnh giới thiệu chính'],
            ['group' => 'banner', 'key' => 'about_small', 'value' => '', 'type' => 'image', 'label' => 'Ảnh giới thiệu phụ'],
        ];

        foreach ($settings as $setting) {
            Setting::query()->updateOrCreate(
                ['key' => $setting['key']],
                $setting,
            );
        }
    }
}
