import { format } from 'date-fns';
import { es } from 'date-fns/locale';

const ISO_DATE_PREFIX = /^(\d{4})-(\d{2})-(\d{2})/;

/**
 * Financial records store a calendar date (no time), so timestamps coming from
 * the API must not be translated to the browser timezone or the day may shift.
 */
export function parseCalendarDate(value) {
    if (!value) return null;

    if (value instanceof Date) return value;

    const match = String(value).match(ISO_DATE_PREFIX);

    if (match) {
        return new Date(Number(match[1]), Number(match[2]) - 1, Number(match[3]));
    }

    return new Date(value);
}

/**
 * Value ready to be bound to an Element Plus date picker (yyyy-MM-dd).
 */
export function toDateInput(value, fallback = '') {
    const date = parseCalendarDate(value);

    return date ? format(date, 'yyyy-MM-dd') : fallback;
}

/**
 * Human readable calendar date, e.g. "09 nov 2026".
 */
export function formatCalendarDate(value, pattern = 'dd MMM yyyy') {
    const date = parseCalendarDate(value);

    return date ? format(date, pattern, { locale: es }) : '';
}
