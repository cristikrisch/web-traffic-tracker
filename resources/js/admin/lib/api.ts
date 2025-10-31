const API = '/api';

export type PageRow = { id: number; canonical_url: string };

export type UniqueRow =
    | { date: string; uniques: number }
    | { canonical_url: string; date: string; uniques: number };

export async function fetchPages(): Promise<PageRow[]> {
    const res = await fetch(`${API}/pages`, { cache: 'no-store' });
    if (!res.ok) throw new Error('Failed to fetch pages');
    return res.json();
}

export async function fetchUniqueVisits(params: { from: string; to: string; page?: string }): Promise<UniqueRow[]> {
    const u = new URL(`${API}/metrics/unique-visits`, window.location.origin);
    u.searchParams.set('from', params.from);
    u.searchParams.set('to', params.to);
    if (params.page) u.searchParams.set('page', params.page);
    const res = await fetch(u.toString(), { cache: 'no-store' });
    if (!res.ok) throw new Error('Failed to fetch metrics');
    return res.json();
}
