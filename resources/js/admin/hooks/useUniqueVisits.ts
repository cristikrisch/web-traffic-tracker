import { useQuery } from '@tanstack/react-query';
import { fetchUniqueVisits } from '../lib/api';

export function useUniqueVisits(params: { from: string; to: string; page?: string }) {
    return useQuery({
        queryKey: ['unique-visits', params],
        queryFn: () => fetchUniqueVisits(params),
        keepPreviousData: true,
    });
}
