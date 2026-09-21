/**
 * Default editor configuration.
 *
 * The toolbar reads from here to populate font families / sizes and to know
 * which insert items to expose. Keeping this in a single module means the
 * Blade component can hand the entire config object to the JS entry point
 * via a `data-lex-config` attribute.
 */

/** @typedef {{label:string, value:string}} SelectOption */

/** Web-safe font families Lexical supports out of the box. */
export const FONT_FAMILY_OPTIONS = /** @type {SelectOption[]} */ ([
    { label: 'Inter', value: 'Inter, system-ui, sans-serif' },
    { label: 'Arial', value: 'Arial, Helvetica, sans-serif' },
    { label: 'Georgia', value: 'Georgia, "Times New Roman", serif' },
    { label: 'Times New Roman', value: '"Times New Roman", Times, serif' },
    { label: 'Courier New', value: '"Courier New", Courier, monospace' },
    { label: 'Verdana', value: 'Verdana, Geneva, sans-serif' },
    { label: 'Trebuchet MS', value: '"Trebuchet MS", sans-serif' },
    { label: 'Comic Sans', value: '"Comic Sans MS", cursive' },
    { label: 'Kalpurush (Bangla)', value: 'Kalpurush, "SolaimanLipi", sans-serif' },
]);

/** Ordered list of selectable sizes; also used to clamp font-size step buttons. */
export const FONT_SIZE_OPTIONS = /** @type {SelectOption[]} */ ([
    { label: '10', value: '10px' },
    { label: '11', value: '11px' },
    { label: '12', value: '12px' },
    { label: '13', value: '13px' },
    { label: '14', value: '14px' },
    { label: '15', value: '15px' },
    { label: '16', value: '16px' },
    { label: '17', value: '17px' },
    { label: '18', value: '18px' },
    { label: '19', value: '19px' },
    { label: '20', value: '20px' },
    { label: '22', value: '22px' },
    { label: '24', value: '24px' },
    { label: '26', value: '26px' },
    { label: '28', value: '28px' },
    { label: '32', value: '32px' },
    { label: '36', value: '36px' },
    { label: '48', value: '48px' },
]);

/** Block types surfaced in the block-type dropdown. */
export const BLOCK_TYPE_OPTIONS = /** @type {SelectOption[]} */ ([
    { label: 'Paragraph', value: 'paragraph' },
    { label: 'Heading 1', value: 'h1' },
    { label: 'Heading 2', value: 'h2' },
    { label: 'Heading 3', value: 'h3' },
    { label: 'Heading 4', value: 'h4' },
    { label: 'Heading 5', value: 'h5' },
    { label: 'Heading 6', value: 'h6' },
    { label: 'Quote', value: 'quote' },
]);

/** Items surfaced in the Insert dropdown. Keep in sync with the command registry. */
export const INSERT_MENU_ITEMS = /** @type {{label:string, value:string, icon:string}[]} */ ([
    { label: 'Horizontal Rule', value: 'horizontal-rule', icon: 'minus' },
    { label: 'Page Break', value: 'page-break', icon: 'page-break' },
    { label: 'Image', value: 'image', icon: 'image' },
    { label: 'Table', value: 'table', icon: 'table' },
    { label: 'Code Block', value: 'code-block', icon: 'code' },
    { label: 'Link', value: 'link', icon: 'link' },
    { label: 'YouTube Video', value: 'youtube', icon: 'video' },
    { label: 'Date', value: 'date', icon: 'calendar' },
    { label: 'Collapsible Container', value: 'collapsible', icon: 'chevron-down' },
]);

/** Color palette used by the text-color and highlight pickers. */
export const TEXT_COLORS = [
    '#000000', '#434343', '#666666', '#999999', '#b7b7b7', '#cccccc', '#d9d9d9', '#efefef', '#f3f3f3', '#ffffff',
    '#980000', '#ff0000', '#ff9900', '#ffff00', '#00ff00', '#00ffff', '#4a86e8', '#0000ff', '#9900ff', '#ff00ff',
    '#e6b8af', '#f4cccc', '#fce5cd', '#fff2cc', '#d9ead3', '#d0e0e3', '#c9daf8', '#cfe2f3', '#d9d2e9', '#ead1dc',
];

export const HIGHLIGHT_COLORS = [
    'transparent',
    '#feffe2', '#fff2cc', '#fce5cd', '#f4cccc',
    '#ead1dc', '#d9d2e9', '#cfe2f3', '#c9daf8',
    '#d0e0e3', '#d9ead3', '#b6d7a8',
];

/**
 * @typedef {Object} EditorConfig
 * @property {string} uploadUrl          POST endpoint accepting a multipart image.
 * @property {string} csrf               CSRF token (already quoted-safe via Blade).
 * @property {SelectOption[]} fontFamilies
 * @property {SelectOption[]} fontSizes
 * @property {SelectOption[]} blockTypes
 * @property {string[]} textColors
 * @property {string[]} highlightColors
 * @property {Array} insertMenu
 */

/** Build the config object the editor entry-point expects. */
export function buildConfig(dataset) {
    return {
        uploadUrl: dataset.uploadUrl || '',
        csrf: dataset.csrf || '',
        fontFamilies: FONT_FAMILY_OPTIONS,
        fontSizes: FONT_SIZE_OPTIONS,
        blockTypes: BLOCK_TYPE_OPTIONS,
        textColors: TEXT_COLORS,
        highlightColors: HIGHLIGHT_COLORS,
        insertMenu: INSERT_MENU_ITEMS,
    };
}

export default buildConfig;
