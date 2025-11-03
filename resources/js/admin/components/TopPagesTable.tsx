import React from 'react';

export default function TopPagesTable({ rows }: { rows: { canonical_url: string; date: string; uniques: number }[] }) {

    const totals = new Map<string, number>();

    rows.forEach(r => totals.set(r.canonical_url, (totals.get(r.canonical_url) || 0) + r.uniques));

    const sorted = Array.from(totals.entries()).sort((a,b) => b[1]-a[1]).slice(0, 20);

    return (
        <div style={{ background:'white', border:'1px solid #eee', borderRadius:8, padding:12 }}>
            <h3 style={{ marginTop:0 }}>Top pages (by uniques)</h3>
            <table style={{ width:'100%', borderCollapse:'collapse' }}>
                <thead>
                <tr>
                    <th style={{ textAlign:'left', borderBottom:'1px solid #eee', padding:8 }}>Page</th>
                    <th style={{ textAlign:'right', borderBottom:'1px solid #eee', padding:8 }}>Uniques</th>
                </tr>
                </thead>
                <tbody>
                {sorted.map(([url, uniq]) => (
                    <tr key={url}>
                        <td style={{ padding:8, borderBottom:'1px solid #f5f5f5', fontFamily:'monospace' }}>{url}</td>
                        <td style={{ padding:8, borderBottom:'1px solid #f5f5f5', textAlign:'right' }}>{uniq}</td>
                    </tr>
                ))}
                </tbody>
            </table>
        </div>
    );
}
