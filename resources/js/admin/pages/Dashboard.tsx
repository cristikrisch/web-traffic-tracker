import React from 'react';
import { useMemo, useState } from 'react';
import { format } from 'date-fns';
import { usePages } from '../hooks/usePages';
import { useUniqueVisits } from '../hooks/useUniqueVisits';
import { fmt, today, sevenDaysAgo } from '../lib/date';
import DateRangePicker from '../components/DateRangePicker';
import PageSelect from '../components/PageSelect';
import UniqueVisitsChart from '../components/UniqueVisitsChart';
import TopPagesTable from '../components/TopPagesTable';

export default function Dashboard() {
    const [from, setFrom] = useState<Date>(sevenDaysAgo);
    const [to, setTo] = useState<Date>(today);
    const [page, setPage] = useState<string | undefined>(undefined);

    const { data: pages } = usePages();
    const { data, isLoading, error } = useUniqueVisits({ from: fmt(from), to: fmt(to), page });

    const chartData = useMemo(() => {
        if (!data) return [];
        if (page) return (data as any[]).map(d => ({ date: d.date, uniques: d.uniques }));
        const map = new Map<string, number>();
        (data as any[]).forEach(d => map.set(d.date, (map.get(d.date) || 0) + d.uniques));
        return Array.from(map.entries()).sort((a,b)=>a[0].localeCompare(b[0])).map(([date, uniques]) => ({ date, uniques }));
    }, [data, page]);

    return (
        <div style={{ padding: 24, background: '#f7f7f9', minHeight: '100vh' }}>
            <h1 style={{ marginTop: 0 }}>Website Traffic — Uniques</h1>

            <div style={{ display:'flex', gap:24, alignItems:'flex-start', marginBottom:24 }}>
                <div>
                    <label style={{ display:'block', fontSize:12, color:'#666', marginBottom:6 }}>Date range</label>
                    <DateRangePicker from={from} to={to} onChange={({ from, to }) => { setFrom(from); setTo(to); }} />
                </div>

                <div style={{ marginTop:28 }}>
                    <label style={{ display:'block', fontSize:12, color:'#666', marginBottom:6 }}>Page</label>
                    <PageSelect pages={pages} value={page} onChange={setPage} />
                </div>
            </div>

            {isLoading && <div>Loading metrics…</div>}
            {error && <div style={{ color:'crimson' }}>Failed to load metrics.</div>}
            {!isLoading && !error && (
                <>
                    <UniqueVisitsChart data={chartData} />
                    {!page && <div style={{ height:16 }} />}
                    {!page && <TopPagesTable rows={data as any[]} />}
                </>
            )}
        </div>
    );
}
