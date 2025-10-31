import React from 'react';
import type { PageRow } from '../lib/api';

export default function PageSelect({
                                       pages, value, onChange,
                                   }: { pages?: PageRow[]; value?: string; onChange: (v?: string) => void }) {
    return (
        <select value={value || ''} onChange={(e) => onChange(e.target.value || undefined)} style={{ padding: 8, minWidth: 360 }}>
            <option value="">All pages</option>
            {pages?.map(p => <option key={p.id} value={p.canonical_url}>{p.canonical_url}</option>)}
        </select>
    );
}
