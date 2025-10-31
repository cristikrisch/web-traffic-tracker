import { useQuery } from '@tanstack/react-query';
import { fetchPages } from '../lib/api';

export function usePages() {
    return useQuery({ queryKey: ['pages'], queryFn: fetchPages });
}
