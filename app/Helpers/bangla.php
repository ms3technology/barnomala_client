<?php

if (! function_exists('formatDateBN')) {
    /**
     * Format a date for Bangla display.
     *
     * @param  string  $dateString  Any parseable date string.
     * @param  string  $type        'day' returns the day digit translated to Bangla,
     *                              'month' returns the Bangla month name.
     * @return string
     */
    function formatDateBN($dateString, $type = 'day')
    {
        if (empty($dateString)) {
            return $type === 'day' ? '' : '';
        }

        try {
            $date = new \DateTime($dateString);
        } catch (\Exception $e) {
            return $type === 'day' ? '' : '';
        }

        if ($type === 'day') {
            $days = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
            return str_replace(['0','1','2','3','4','5','6','7','8','9'], $days, $date->format('d'));
        }

        $months = [
            'জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন',
            'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর',
        ];

        return $months[intval($date->format('m')) - 1] ?? '';
    }
}
