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
            ['group' => 'seo', 'key' => 'google_site_verification', 'value' => '', 'type' => 'text', 'label' => 'Google Search Console (mã xác minh)'],
            ['group' => 'seo', 'key' => 'about_meta_description', 'value' => 'Tìm hiểu câu chuyện, sứ mệnh và giá trị của Nông Sản TTC — nông sản sạch từ đồng quê đến bàn ăn.', 'type' => 'textarea', 'label' => 'Meta mô tả — Về chúng tôi'],
            ['group' => 'seo', 'key' => 'contact_meta_description', 'value' => 'Liên hệ Nông Sản TTC — địa chỉ, điện thoại, email và bản đồ. Chúng tôi sẵn sàng hỗ trợ bạn.', 'type' => 'textarea', 'label' => 'Meta mô tả — Liên hệ'],
            ['group' => 'contact', 'key' => 'company_name', 'value' => 'Công ty TNHH sản xuất và chế biến nông sản TTC', 'type' => 'text', 'label' => 'Tên công ty'],
            ['group' => 'contact', 'key' => 'phone', 'value' => '0901 234 567', 'type' => 'text', 'label' => 'Số điện thoại'],
            ['group' => 'contact', 'key' => 'email', 'value' => 'info@nongsanttc.com', 'type' => 'text', 'label' => 'Email'],
            ['group' => 'contact', 'key' => 'address', 'value' => 'Xã Thần Khê, Tỉnh Hưng Yên', 'type' => 'textarea', 'label' => 'Địa chỉ'],
            ['group' => 'contact', 'key' => 'google_maps_url', 'value' => 'https://maps.google.com', 'type' => 'url', 'label' => 'Google Maps URL'],
            ['group' => 'contact', 'key' => 'google_maps_embed', 'value' => '', 'type' => 'textarea', 'label' => 'Google Maps Embed iframe'],
            ['group' => 'social', 'key' => 'zalo', 'value' => 'https://zalo.me/0901234567', 'type' => 'url', 'label' => 'Zalo'],
            ['group' => 'social', 'key' => 'facebook', 'value' => 'https://facebook.com/nongsanttc', 'type' => 'url', 'label' => 'Facebook'],
            ['group' => 'social', 'key' => 'messenger', 'value' => 'https://m.me/nongsanttc', 'type' => 'url', 'label' => 'Messenger'],
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
