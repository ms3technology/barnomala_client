/**
 * URL validation helpers.
 *
 * Lexical's link plugin accepts arbitrary strings — we never want to persist
 * `javascript:` or `data:` payloads. Validation happens client-side for UX
 * (so we can warn the user before saving) and again server-side at submit
 * time, which is the authoritative check.
 */

const SAFE_PROTOCOLS = new Set(['http:', 'https:', 'mailto:', 'tel:']);

/**
 * Returns a normalized URL string when the input is acceptable, or `null`
 * otherwise. Mailto / tel addresses are also accepted.
 *
 * @param {string} raw
 * @param {boolean} [allowMailto]
 * @returns {string|null}
 */
export function sanitizeUrl(raw, allowMailto = true) {
    if (typeof raw !== 'string') return null;

    const trimmed = raw.trim();
    if (!trimmed) return null;

    // Allow bare host names like "example.com"
    if (/^[\w.-]+\.[a-z]{2,}/i.test(trimmed) && !trimmed.includes(' ')) {
        return 'https://' + trimmed;
    }

    let url;
    try {
        url = new URL(trimmed);
    } catch (_) {
        return null;
    }

    if (!SAFE_PROTOCOLS.has(url.protocol)) return null;
    if (url.protocol === 'mailto:' && !allowMailto) return null;

    return url.toString();
}

/**
 * Extract a YouTube video ID from common URL shapes.
 *
 * @param {string} url
 * @returns {string|null}
 */
export function extractYouTubeId(url) {
    if (!url) return null;
    try {
        const parsed = new URL(url);
        const host = parsed.hostname.replace(/^www\./, '');
        if (host === 'youtu.be') {
            const id = parsed.pathname.split('/').filter(Boolean)[0];
            return id || null;
        }
        if (host.endsWith('youtube.com')) {
            if (parsed.pathname === '/watch') {
                return parsed.searchParams.get('v');
            }
            const parts = parsed.pathname.split('/').filter(Boolean);
            if (parts[0] === 'embed' || parts[0] === 'shorts' || parts[0] === 'live') {
                return parts[1] || null;
            }
        }
    } catch (_) {
        return null;
    }
    return null;
}

/**
 * Build the iframe `src` for an embedded YouTube video using the privacy-
 * enhanced `youtube-nocookie.com` domain.
 *
 * @param {string} id
 * @returns {string}
 */
export function youtubeEmbedUrl(id) {
    return `https://www.youtube-nocookie.com/embed/${encodeURIComponent(id)}`;
}
