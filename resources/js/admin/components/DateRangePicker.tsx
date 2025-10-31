import React from 'react';
import { useState, useEffect } from 'react';
import { DayPicker, DateRange } from 'react-day-picker';
import 'react-day-picker/dist/style.css';
import { format } from 'date-fns';

export default function DateRangePicker({
                                            from, to, onChange,
                                        }: { from: Date; to: Date; onChange: (r: { from: Date; to: Date }) => void }) {
    const [range, setRange] = useState<DateRange>({ from, to });
    useEffect(() => { if (range.from && range.to) onChange({ from: range.from, to: range.to }); }, [range]);
    return (
        <div className="date-picker">
            <div style={{ fontSize: 12, marginBottom: 8 }}>
                {range.from && range.to ? `${format(range.from, 'yyyy-MM-dd')} → ${format(range.to, 'yyyy-MM-dd')}` : 'Pick range'}
            </div>
            <DayPicker mode="range" selected={range} onSelect={setRange} defaultMonth={to} numberOfMonths={1} />
        </div>
    );
}
