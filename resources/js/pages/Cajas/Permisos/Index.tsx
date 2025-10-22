import AppLayout from '@/layouts/app-layout';

type Props = {
    permisos: {
        data: any[];
        meta: {
            total_permisos: number;
            pagination?: {
                current_page: number;
                last_page: number;
                per_page: number;
                from: number | null;
                to: number | null;
                total: number;
            };
        };
    };
};

export default function Index({ permisos }: Props) {
    const { data, meta } = permisos;
    return (
        <AppLayout title="Menu">
            <div className="bg-white shadow overflow-hidden sm:rounded-md m-2">
                <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Permisos { JSON.stringify(data) } { JSON.stringify(meta) }
                        </h3>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}