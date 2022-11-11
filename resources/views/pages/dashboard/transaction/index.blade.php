<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaction') }}
        </h2>
    </x-slot>

    <x-slot name="script">
        <script>
            // AJAX DataTable
            var datatable = $('#crudTable').DataTable({
                ajax: {
                    url: '{!! url()->current() !!}',
                    order: [[0, 'desc']]
                },
                columns: [
                    { data: 'id', name: 'id', width: '5%'},
                    { data: 'user.name', name: 'user.name' },
                    {data: 'user_location.address', name: 'user_location.address'},
                    { data: 'total_price', name: 'total_price' },
                    { data: 'status', name: 'status' },
                    { data: 'kurir.nama', name: 'kurir.nama' },
                    { data: 'kurir.phone_number', name: 'kurir.phone_number' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: true,
                        width: '25%'
                    },
                ],
            });
        </script>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 bg-white sm:p-6">
                    <table id="crudTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>Total Harga</th>
                                <th>Status</th>
                                <th>Nama Kurir</th>
                                <th>No Wa Kurir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>