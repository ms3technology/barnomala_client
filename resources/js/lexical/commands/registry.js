/**
 * Command registry.
 *
 * Lexical dispatches commands through `editor.registerCommand(CMD, fn, priority)`.
 * We define the command constants here so every toolbar / plugin module
 * imports the same identifiers. Commands themselves are registered in
 * `editor.js`; this file is purely a vocabulary.
 */
import { createCommand } from 'lexical';

/** @type {import('lexical').LexicalCommand<void>} */
export const INSERT_HORIZONTAL_RULE_COMMAND = createCommand('INSERT_HORIZONTAL_RULE_COMMAND');

/** @type {import('lexical').LexicalCommand<void>} */
export const INSERT_PAGE_BREAK_COMMAND = createCommand('INSERT_PAGE_BREAK_COMMAND');

/**
 * @typedef {Object} InsertImagePayload
 * @property {string} src
 * @property {string} [altText]
 */

/** @type {import('lexical').LexicalCommand<InsertImagePayload>} */
export const INSERT_IMAGE_COMMAND = createCommand('INSERT_IMAGE_COMMAND');

/** @type {import('lexical').LexicalCommand<string>} */
export const INSERT_YOUTUBE_COMMAND = createCommand('INSERT_YOUTUBE_COMMAND');

/** @type {import('lexical').LexicalCommand<void>} */
export const INSERT_COLLAPSIBLE_COMMAND = createCommand('INSERT_COLLAPSIBLE_COMMAND');

/**
 * @typedef {Object} InsertCodeBlockPayload
 * @property {string} [language]
 */

/** @type {import('lexical').LexicalCommand<InsertCodeBlockPayload>} */
export const INSERT_CODE_BLOCK_COMMAND = createCommand('INSERT_CODE_BLOCK_COMMAND');

/** @type {import('lexical').LexicalCommand<void>} */
export const INSERT_DATE_COMMAND = createCommand('INSERT_DATE_COMMAND');
