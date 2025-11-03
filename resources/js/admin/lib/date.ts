import { format, subDays } from 'date-fns';
export const fmt = (d: Date) => format(d, 'yyyy-MM-dd');
export const today = new Date();
export const twoDaysAgo = subDays(today, 2);
