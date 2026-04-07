<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options = [
            'institute.about.title' => ['value' => 'আমাদের প্রতিষ্ঠান সম্পর্কে', 'type' => 'string'],
            'institute.about.text' => ['value' => 'আমাদের শিক্ষা প্রতিষ্ঠান একটি ঐতিহ্যবাহী বিদ্যাপীঠ। দীর্ঘ পথচলায় আমরা অসংখ্য মেধাবী শিক্ষার্থী উপহার দিয়েছি যারা দেশ ও দশের কল্যাণে নিয়োজিত। আমাদের লক্ষ্য হলো শিক্ষার্থীদের সুপ্ত প্রতিভা বিকাশে সহায়তা করা এবং তাদের সুনাগরিক হিসেবে গড়ে তোলা।', 'type' => 'string'],
            'institute.about.limit' => ['value' => 300, 'type' => 'integer'],
            'institute.about.button_text' => ['value' => 'আরও পড়ুন...', 'type' => 'string'],
            'institute.identity.eiin' => ['value' => '123456', 'type' => 'string'],
            'institute.identity.code' => ['value' => '7890', 'type' => 'string'],
            'institute.identity.center_code' => ['value' => '543', 'type' => 'string'],
            'institute.identity.established_year' => ['value' => '1995', 'type' => 'string'],
            'institute.stats.classes_count' => ['value' => 12, 'type' => 'integer'],
            'institute.stats.students_count' => ['value' => 1200, 'type' => 'integer'],
            'institute.stats.teachers_count' => ['value' => 45, 'type' => 'integer'],
            'institute.stats.staffs_count' => ['value' => 15, 'type' => 'integer'],
            'institute.contact.phone' => ['value' => '01234-567890', 'type' => 'string'],
            'institute.contact.phone_extra' => ['value' => '', 'type' => 'string'],
            'institute.contact.email' => ['value' => 'info@school.edu.bd', 'type' => 'string'],
            'institute.contact.address' => ['value' => 'আপনার প্রতিষ্ঠানের ঠিকান এখানে লিখুন', 'type' => 'string'],
            'institute.branding.name' => ['value' => 'আপনার প্রতিষ্ঠানের নাম', 'type' => 'string'],
            'institute.branding.banner_type' => ['value' => 'info_only', 'type' => 'string'], // banner_only, banner_with_overlay, banner_split, info_only
            'institute.branding.banner_json' => ['value' => json_encode(['url' => asset('images/banner.png')]), 'type' => 'json'],
            'institute.branding.accent_color' => ['value' => '#4F46E5', 'type' => 'string'],
            'institute.hero.type' => ['value' => 'slider_with_notice', 'type' => 'string'], // slider_only, slider_with_notice
            'institute.hero.slider_design' => ['value' => 'slider_1', 'type' => 'string'], // slider_1, slider_2
            'institute.demographics.classes' => ['value' => json_encode(['Six' => 120, 'Seven' => 110, 'Eight' => 105, 'Nine' => 95, 'Ten' => 80]), 'type' => 'json'],
            'institute.demographics.gender' => ['value' => json_encode(['Male' => 260, 'Female' => 250]), 'type' => 'json'],
            'institute.demographics.religion' => ['value' => json_encode(['Islam' => 450, 'Hindu' => 45, 'Christian' => 10, 'Buddhism' => 5]), 'type' => 'json'],
            'institute.homepage.layout' => ['value' => json_encode([
                'hero' => true,
                'latest_news' => true,
                'institute_info' => true,
                'message_section' => true,
                'stats_counter' => true,
                'quick_links' => true,
                'teachers' => true,
                'student_demographics' => true,
            ]), 'type' => 'json'],
        ];

        foreach ($options as $key => $data) {
            \App\Models\Option::updateOrCreate(
                ['option_key' => $key],
                ['option_value' => $data['value'], 'value_type' => $data['type']]
            );
        }
    }
}
