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
            ['group' => 'contact', 'key' => 'company_name', 'value' => 'Công ty TNHH sản xuất và chế biến nông sản TTC', 'type' => 'text', 'label' => 'Tên công ty'],
            ['group' => 'contact', 'key' => 'phone', 'value' => '0901 234 567', 'type' => 'text', 'label' => 'Số điện thoại'],
            ['group' => 'contact', 'key' => 'email', 'value' => 'info@nongsanttc.com', 'type' => 'text', 'label' => 'Email'],
            ['group' => 'contact', 'key' => 'address', 'value' => 'Xã Ninh Phú, Huyện Hoa Lư, Tỉnh Ninh Bình', 'type' => 'textarea', 'label' => 'Địa chỉ'],
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
