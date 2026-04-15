<?php

namespace App\Registries;

class OptionRegistry
{
    /**
     * Define the structure of options and their categories.
     * 
     * @return array
     */
    public static function getRegistration(): array
    {
        return [
            'about' => [
                'label' => 'About Us',
                'description' => 'Institution summary and about section image.',
                'icon' => 'fas fa-info-circle',
                'options' => [
                    'institute.about.side_panel_type' => [
                        'label' => 'About Side Panel Type',
                        'type' => 'select',
                        'options' => [
                            'image' => 'Image',
                            'notice' => 'Notice Side Panel',
                        ],
                        'default' => 'image',
                    ],
                    'institute.about.image_json' => [
                        'label' => 'About Image',
                        'type' => 'image',
                    ],
                    'institute.about.title' => [
                        'label' => 'About Title',
                        'type' => 'text',
                    ],
                    'institute.about.text' => [
                        'label' => 'About Text',
                        'type' => 'textarea',
                    ],
                    'institute.about.button_text' => [
                        'label' => 'About Button Text',
                        'type' => 'text',
                    ],
                    'institute.footer.text' => [
                        'label' => 'Footer Text',
                        'type' => 'textarea',
                        'placeholder' => 'Enter text for the footer section',
                    ],
                ]
            ],
            'identity' => [
                'label' => 'Identity',
                'description' => 'Institution basic information and identification.',
                'icon' => 'fas fa-id-card',
                'options' => [
                    'institute.branding.name' => [
                        'label' => 'Institution Name',
                        'type' => 'text',
                        'placeholder' => 'Enter institution name',
                    ],
                    'institute.identity.established_year' => [
                        'label' => 'Established Year',
                        'type' => 'number',
                        'placeholder' => 'e.g. 1995',
                    ],
                    'institute.identity.eiin' => [
                        'label' => 'EIIN Number',
                        'type' => 'text',
                        'placeholder' => 'Enter EIIN number',
                    ],
                    'institute.identity.code' => [
                        'label' => 'Institution Code',
                        'type' => 'text',
                        'placeholder' => 'Enter institute code',
                    ],
                    'site.visitor_count' => [
                        'label' => 'Visitor Counter (Auto)',
                        'type' => 'number',
                        'placeholder' => 'Enter visitor count',
                    ],
                ]
            ],
            'contact' => [
                'label' => 'Contact Info',
                'description' => 'Physical address and communication details.',
                'icon' => 'fas fa-address-book',
                'options' => [
                    'institute.contact.address' => [
                        'label' => 'Full Address',
                        'type' => 'textarea',
                    ],
                    'institute.contact.phone' => [
                        'label' => 'Phone Number',
                        'type' => 'text',
                    ],
                    'institute.contact.email' => [
                        'label' => 'Email Address',
                        'type' => 'email',
                    ],
                    'institute.contact.map_link' => [
                        'label' => 'Google Map Link',
                        'type' => 'textarea',
                    ],
                ]
            ],
            'social' => [
                'label' => 'Social Links',
                'description' => 'Social media profiles and links.',
                'icon' => 'fas fa-share-alt',
                'options' => [
                    'institute.social.facebook' => [
                        'label' => 'Facebook Page',
                        'type' => 'url',
                    ],
                    'institute.social.youtube' => [
                        'label' => 'YouTube Channel',
                        'type' => 'url',
                    ],
                ]
            ]
        ];
    }

    /**
     * Get flat list of all registered option keys.
     * 
     * @return array
     */
    public static function getAllKeys(): array
    {
        $keys = [];
        foreach (self::getRegistration() as $category) {
            $keys = array_merge($keys, array_keys($category['options']));
        }
        return $keys;
    }
}
