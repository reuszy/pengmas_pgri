<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kelas | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 overflow-x-hidden">

    <nav class="w-full bg-white text-black px-8 py-4 shadow flex justify-between items-center">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo_SMK_PGRI_GUMELAR.jpeg') }}" 
                 alt="Logo SMK PGRI Gumelar" 
                 class="w-10 h-10 rounded-full">
            <h1 class="font-semibold text-xl tracking-wide">SMK PGRI GUMELAR</h1>
        </div>
        
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button class="bg-red-600 text-white px-5 py-2 rounded-md hover:bg-red-700">
                Keluar
            </button>
        </form>
    </nav>

    <div class="bg-[#0a1b3d] min-h-screen w-full pt-10 pb-20 flex justify-center">

        <div class="bg-[#d5eaff] rounded-2xl shadow-2xl flex gap-6 p-8 w-11/12 max-w-7xl">

            <div class="w-64 bg-white rounded-xl shadow p-6 flex-shrink-0">

                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 bg-gray-300 rounded-full flex items-center justify-center text-xl font-bold text-gray-700">
                        AA
                    </div>
                    <p class="mt-3 font-semibold text-gray-700 text-center">Hi Admin</p>
                </div>

                <div class="mt-10">
                    <h3 class="text-xs text-gray-500 uppercase mb-2">Menu</h3>

                    <nav class="space-y-2">
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-200">
                            Dashboard
                        </a>

                        <a href="{{ route('admin.dataSiswa') }}" class="block px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-200">
                            Data Siswa
                        </a>

                        <a href="{{ route('admin.dataPembayaran') }}" class="block px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-200">
                            Data Pembayaran
                        </a>

                        <a href="{{ route('admin.dataKelas') }}" class="block px-4 py-2 rounded-md text-sm font-medium bg-gray-300 font-semibold">
                            Data Kelas
                        </a>
                    </nav>
                </div>
            </div>

            <div class="flex-1">

                <h2 class="text-3xl font-bold mb-6 text-[#0a1b3d]">Lihat Data Kelas</h2>

                <div class="flex justify-between items-end mb-4 gap-4">
                    <div class="flex flex-col gap-2">
                        <div>
                            Show
                            <select id="showEntries" class="px-2 py-1 border rounded text-sm">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="100">100</option>
                            </select>
                            entries
                        </div>

                        <input id="customSearch" type="search" placeholder="Cari nama kelas..." class="border px-3 py-1 rounded text-sm shadow-sm" />
                    </div>

                    <button id="btnTambah" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Tambah Kelas</button>
                </div>

                <table id="tabelKelas" class="display w-full text-sm">
                    <thead>
                        <tr class="bg-blue-900 text-white text-left">
                            <th class="px-3 py-2 w-16">No</th>
                            <th class="px-3 py-2">Nama Kelas</th>
                            <th class="px-3 py-2 w-48">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse ($kelas as $key => $k)
                        <tr class="border-b hover:bg-gray-50" id="row-{{ $k->id }}">
                            <td class="px-3 py-2 text-center">{{ $key + 1 }}</td>
                            <td class="px-3 py-2 font-medium">{{ $k->nama_kelas }}</td>
                            <td class="px-3 py-2 flex gap-2">
                                <button onclick="openEditModal(this)" data-url="{{ route('admin.kelas.edit', $k->id) }}" class="px-4 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">Edit</button>
                                <button onclick="hapusKelas('{{ $k->id }}')" class="px-4 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">Hapus</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-gray-500">Tidak ada data kelas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
                <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
                <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

                <div id="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                    <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Tambah Data Kelas</h3>
                            <button id="closeModal" class="text-gray-500 hover:text-gray-700">&times;</button>
                        </div>

                        <form action="{{ route('admin.kelas.store') }}" method="POST" id="formTambah">
                            @csrf
                            <div id="formErrors" class="text-sm text-red-600 mb-3"></div>
                            
                            <div class="mb-4">
                                <label class="text-sm font-medium">Nama Kelas</label>
                                <input name="nama_kelas" required type="text" placeholder="Contoh: X RPL 1" class="w-full border rounded px-3 py-2 mt-1" />
                            </div>

                            <div class="mt-4 flex justify-end gap-2">
                                <button type="button" id="cancelModal" class="px-4 py-2 border rounded hover:bg-gray-100">Batal</button>
                                <button type="button" id="submitTambahBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                    <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Edit Data Kelas</h3>
                            <button id="closeModalEdit" class="text-gray-500 hover:text-gray-700">&times;</button>
                        </div>

                        <form id="formEdit" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" id="edit_id">
                            <div id="formEditErrors" class="text-sm text-red-600 mb-3"></div>
                            
                            <div class="mb-4">
                                <label class="text-sm font-medium">Nama Kelas</label>
                                <input name="nama_kelas" id="edit_nama_kelas" required type="text" class="w-full border rounded px-3 py-2 mt-1" />
                            </div>

                            <div class="mt-4 flex justify-end gap-2">
                                <button type="button" id="cancelModalEdit" class="px-4 py-2 border rounded hover:bg-gray-100">Batal</button>
                                <button type="button" id="submitEditBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    let table = $('#tabelKelas').DataTable({
                        searching: true,
                        lengthChange: false,
                        paging: true,
                        ordering: false,
                        dom: 'rtip'
                    });

                    $('#tabelKelas_filter').remove();
                    let dtWrapper = $('#tabelKelas').closest('.dataTables_wrapper');
                    if (dtWrapper.length) {
                        dtWrapper.find('input[type="search"]').not('#customSearch').closest('label').remove();
                        dtWrapper.find('input[type="search"]').not('#customSearch').remove();
                    }

                    $('#customSearch').on('input', function() {
                        table.search(this.value).draw();
                    });
                    $('#showEntries').on('change', function() {
                        table.page.len($(this).val()).draw();
                    });

                    $('#btnTambah').on('click', function() {
                        $('#formErrors').html('');
                        $('#formTambah')[0].reset();
                        $('#modalTambah').removeClass('hidden').addClass('flex');
                    });
                    $('#closeModal, #cancelModal').on('click', function() {
                        $('#modalTambah').addClass('hidden').removeClass('flex');
                    });

                    function submitTambah($form) {
                        let url = $form.attr('action');
                        let payload = {
                            _token: $form.find('input[name="_token"]').val(),
                            nama_kelas: $form.find('input[name="nama_kelas"]').val()
                        };

                        if (!payload.nama_kelas) {
                            $('#formErrors').html('Nama kelas harus diisi.');
                            return;
                        }

                        $('#formErrors').html('');

                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: payload,
                            success: function(resp) {
                                let newIndex = table.rows().count() + 1;

                                let editUrl = "{{ url('admin/kelas/edit') }}/" + resp.id;

                                table.row.add([
                                    `<div class="text-center">${newIndex}</div>`,
                                    `<div class="font-medium">${resp.nama_kelas}</div>`,
                                    `<div class="flex gap-2">
                                        <button onclick="openEditModal(this)" data-url="${editUrl}" class="px-4 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">Edit</button>
                                        <button onclick="hapusKelas('${resp.id}')" class="px-4 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">Hapus</button>
                                    </div>`
                                ]).node().id = 'row-' + resp.id;
                                table.draw(false);

                                $('#modalTambah').addClass('hidden').removeClass('flex');
                                $form[0].reset();
                                alert('Data kelas berhasil ditambahkan');
                            },
                            error: function(xhr) {
                                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                    let msgs = [];
                                    $.each(xhr.responseJSON.errors, function(k, v) { msgs.push(v.join(', ')); });
                                    $('#formErrors').html(msgs.join('<br>'));
                                } else {
                                    alert('Terjadi kesalahan sistem.');
                                }
                            }
                        });
                    }

                    $('#submitTambahBtn').on('click', function(e) {
                        e.preventDefault();
                        submitTambah($('#formTambah'));
                    });

                    window.openEditModal = function(btn) {
                        let urlEdit = $(btn).attr('data-url');

                        $.ajax({
                            url: urlEdit,
                            method: 'GET',
                            success: function(data) {
                                $('#edit_id').val(data.id);
                                $('#edit_nama_kelas').val(data.nama_kelas);
                                $('#formEditErrors').html('');
                                $('#modalEdit').removeClass('hidden').addClass('flex');

                                let urlUpdate = urlEdit.replace('/edit/', '/update/');
                                $('#formEdit').attr('action', urlUpdate);
                            },
                            error: function(xhr) {
                                console.error('Error Response:', xhr.responseText);
                                alert('Gagal memuat data kelas. Silakan cek console.');
                            }
                        });
                    };

                    $('#closeModalEdit, #cancelModalEdit').on('click', function() {
                        $('#modalEdit').addClass('hidden').removeClass('flex');
                    });

                    function submitEdit($form) {
                        let url = $form.attr('action');
                        let id = $('#edit_id').val();
                        let namaKelas = $('#edit_nama_kelas').val();

                        if (!namaKelas) {
                            $('#formEditErrors').html('Nama kelas harus diisi.');
                            return;
                        }

                        $('#formEditErrors').html('');

                        $.ajax({
                            url: url,
                            method: 'PUT',
                            data: {
                                _token: $form.find('input[name="_token"]').val(),
                                nama_kelas: namaKelas
                            },
                            success: function(resp) {
                                let rowIndex = table.row('#row-' + id).index();
                                let rowData = table.row(rowIndex).data();
                                
                                rowData[1] = `<div class="font-medium">${resp.nama_kelas}</div>`;
                                table.row(rowIndex).data(rowData).draw(false);

                                $('#modalEdit').addClass('hidden').removeClass('flex');
                                alert('Data kelas berhasil diperbarui');
                            },
                            error: function(xhr) {
                                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                    let msgs = [];
                                    $.each(xhr.responseJSON.errors, function(k, v) { msgs.push(v.join(', ')); });
                                    $('#formEditErrors').html(msgs.join('<br>'));
                                } else {
                                    alert('Terjadi kesalahan sistem.');
                                }
                            }
                        });
                    }

                    $('#submitEditBtn').on('click', function(e) {
                        e.preventDefault();
                        submitEdit($('#formEdit'));
                    });

                    window.hapusKelas = function(id) {
                        if (confirm('Yakin ingin menghapus kelas ini?')) {
                            let urlDelete = "{{ url('admin/kelas/delete') }}/" + id;

                            $.ajax({
                                url: urlDelete,
                                method: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(resp) {
                                    table.row('#row-' + id).remove().draw(false);
                                    alert(resp.message);
                                },
                                error: function() {
                                    alert('Gagal menghapus data kelas. Mungkin ada data siswa yang terkait dengan kelas ini.');
                                }
                            });
                        }
                    };
                </script>

            </div>
        </div>
    </div>

</body>
</html>