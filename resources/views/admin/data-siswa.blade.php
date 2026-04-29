<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 overflow-x-hidden">

    <!-- NAVBAR -->
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

            <!-- SIDEBAR -->
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

                        <a href="{{ route('admin.dataSiswa') }}" class="block px-4 py-2 rounded-md text-sm font-medium bg-gray-300 font-semibold">
                            Data Siswa
                        </a>

                        <a href="{{ route('admin.dataPembayaran') }}" class="block px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-200">
                            Data Pembayaran
                        </a>

                        <a href="{{ route('admin.dataKelas') }}"
                           class="block px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-100 text-gray-700">
                            Data Kelas
                        </a>
                    </nav>
                </div>
            </div>

            <!-- MAIN CONTENT -->
            <div class="flex-1">

                <h2 class="text-3xl font-bold mb-6 text-[#0a1b3d]">Lihat Data Siswa</h2>

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

                        <input id="customSearch" type="search" placeholder="Cari nama, NIS, kelas..." class="border px-3 py-1 rounded text-sm shadow-sm" />
                    </div>

                    <button id="btnTambah" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Tambah</button>
                </div>

                <table id="tabelSiswa" class="display w-full text-sm">
                    <thead>
                        <tr class="bg-blue-900 text-white text-left">
                            <th class="px-3 py-2">No</th>
                            <th class="px-3 py-2">Nama</th>
                            <th class="px-3 py-2">Kelas</th>
                            <th class="px-3 py-2">NIS</th>
                            <th class="px-3 py-2">Nomor Telepon</th>
                            <th class="px-3 py-2">E-Mail</th>
                            <th class="px-3 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse ($siswa as $key => $s)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $key + 1 }}</td>
                            <td class="px-3 py-2">{{ $s->nama_pengguna ?? ($s->pengguna->nama_pengguna ?? '-') }}</td>
                            <td class="px-3 py-2">{{ $s->nama_kelas ?? ($s->kelas->nama_kelas ?? '-') }}</td>
                            <td class="px-3 py-2">{{ $s->nis }}</td>
                            <td class="px-3 py-2">{{ $s->nomor_telepon ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $s->email ?? '-' }}</td>
                            <td class="px-3 py-2">
                                <button onclick="openEditModal('{{ $s->nis }}')" class="px-4 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">Edit</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-500">Tidak ada data siswa</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- DataTables -->
                <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
                <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
                <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

                <!-- Modal Tambah Siswa -->
                <div id="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                    <div class="bg-white rounded-lg w-3/4 max-w-3xl p-6 shadow-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Tambah Data Siswa</h3>
                            <button id="closeModal" class="text-gray-500 hover:text-gray-700">&times;</button>
                        </div>

                        <form action="{{ route('admin.siswa.store') }}" method="POST">
                            @csrf
                            <div id="formErrors" class="text-sm text-red-600 mb-3"></div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm">Nama Lengkap</label>
                                    <input name="name" required type="text" class="w-full border rounded px-3 py-2" />
                                </div>

                                <div>
                                    <label class="text-sm">NIS</label>
                                    <input name="nis" required type="text" class="w-full border rounded px-3 py-2" />
                                </div>

                                <div>
                                    <label class="text-sm">Tanggal Lahir</label>
                                    <input name="tanggal_lahir" type="date" class="w-full border rounded px-3 py-2" />
                                </div>

                                <div>
                                    <label class="text-sm">E-mail</label>
                                    <input name="email" type="email" class="w-full border rounded px-3 py-2" />
                                </div>

                                <div>
                                    <label class="text-sm">Kelas</label>
                                    <select name="kelas" required class="w-full border rounded px-3 py-2">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="text-sm">Password</label>
                                    <input name="password" required type="password" class="w-full border rounded px-3 py-2" />
                                </div>

                                <div class="col-span-2">
                                    <label class="text-sm">Nomor Telepon</label>
                                    <input name="telepon" type="text" class="w-full border rounded px-3 py-2" />
                                </div>
                            </div>

                            <div class="mt-4 flex justify-end gap-2">
                                <button type="button" id="cancelModal" class="px-4 py-2 border rounded">Batal</button>
                                <button type="button" id="submitTambahBtn" class="px-4 py-2 bg-blue-600 text-white rounded">Tambah</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal Edit Siswa -->
                <div id="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                    <div class="bg-white rounded-lg w-3/4 max-w-3xl p-6 shadow-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Edit Data Siswa</h3>
                            <button id="closeModalEdit" class="text-gray-500 hover:text-gray-700">&times;</button>
                        </div>

                        <form id="formEdit" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="nis" id="edit_nis">
                            <div id="formEditErrors" class="text-sm text-red-600 mb-3"></div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm">Nama Lengkap</label>
                                    <input name="name" id="edit_name" required type="text" class="w-full border rounded px-3 py-2" />
                                </div>

                                <div>
                                    <label class="text-sm">NIS (tidak dapat diubah)</label>
                                    <input id="edit_nis_display" readonly type="text" class="w-full border rounded px-3 py-2 bg-gray-100" />
                                </div>

                                <div>
                                    <label class="text-sm">Tanggal Lahir</label>
                                    <input name="tanggal_lahir" id="edit_tanggal_lahir" type="date" class="w-full border rounded px-3 py-2" />
                                </div>

                                <div>
                                    <label class="text-sm">E-mail</label>
                                    <input name="email" id="edit_email" type="email" class="w-full border rounded px-3 py-2" />
                                </div>

                                <div>
                                    <label class="text-sm">Kelas</label>
                                    <select name="kelas" id="edit_kelas" required class="w-full border rounded px-3 py-2">
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="text-sm">Password Baru (kosongkan jika tidak diubah)</label>
                                    <input name="password" id="edit_password" type="password" class="w-full border rounded px-3 py-2" />
                                </div>

                                <div class="col-span-2">
                                    <label class="text-sm">Nomor Telepon</label>
                                    <input name="telepon" id="edit_telepon" type="text" class="w-full border rounded px-3 py-2" />
                                </div>
                            </div>

                            <div class="mt-4 flex justify-end gap-2">
                                <button type="button" id="cancelModalEdit" class="px-4 py-2 border rounded">Batal</button>
                                <button type="button" id="submitEditBtn" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    // Aktifkan fitur pencarian (search) bawaan DataTables, tapi kita akan gunakan input kustom
                    let table = $('#tabelSiswa').DataTable({
                        searching: true,
                        lengthChange: false,
                        paging: true,
                        ordering: false,
                        dom: 'rtip' // sembunyikan filter default (f) karena pakai input kustom
                    });

                    // Hapus elemen filter default DataTables (jika ada) supaya hanya input kustom yang terlihat
                    $('#tabelSiswa_filter').remove();
                    let dtWrapper = $('#tabelSiswa').closest('.dataTables_wrapper');
                    if (dtWrapper.length) {
                        dtWrapper.find('input[type="search"]').not('#customSearch').closest('label').remove();
                        dtWrapper.find('input[type="search"]').not('#customSearch').remove();
                    }

                    // Bind input pencarian kustom ke fungsi pencarian DataTables
                    $('#customSearch').on('input', function() {
                        table.search(this.value).draw();
                    });

                    // kontrol show entries kustom
                    $('#showEntries').on('change', function() {
                        let val = $(this).val();
                        table.page.len(val).draw();
                    });
                    
                    // Modal behavior
                    $('#btnTambah').on('click', function() {
                        // auto-select first kelas if user hasn't picked one
                        let $kelasSelect = $('#modalTambah select[name="kelas"]');
                        if ($kelasSelect.length && !$kelasSelect.val()) {
                            let firstOpt = $kelasSelect.find('option[value!=""]').first();
                            if (firstOpt.length) $kelasSelect.val(firstOpt.val());
                        }
                        $('#modalTambah').removeClass('hidden').addClass('flex');
                    });
                    $('#closeModal, #cancelModal').on('click', function() {
                        $('#modalTambah').addClass('hidden').removeClass('flex');
                    });

                    // AJAX submit for tambah siswa form with client-side validation
                    function submitTambah($form) {
                        let url = $form.attr('action');
                        // Build explicit payload to ensure 'kelas' is sent
                        let payload = {
                            _token: $form.find('input[name="_token"]').val(),
                            name: $form.find('input[name="name"]').val(),
                            nis: $form.find('input[name="nis"]').val(),
                            tanggal_lahir: $form.find('input[name="tanggal_lahir"]').val(),
                            kelas: $form.find('select[name="kelas"]').val(),
                            email: $form.find('input[name="email"]').val(),
                            telepon: $form.find('input[name="telepon"]').val(),
                            password: $form.find('input[name="password"]').val()
                        };

                        // client-side validation
                        let nama = $form.find('input[name="name"]').val();
                        let nis = $form.find('input[name="nis"]').val();
                        let kelas = $form.find('select[name="kelas"]').val();
                        let password = $form.find('input[name="password"]').val();
                        let errors = [];
                        if (!nama) errors.push('Nama harus diisi.');
                        if (!nis) errors.push('NIS harus diisi.');
                        if (!kelas) errors.push('Kelas harus dipilih.');
                        if (!password) errors.push('Password harus diisi.');

                        if (errors.length) {
                            $('#formErrors').html(errors.join('<br>'));
                            return;
                        }

                        $('#formErrors').html('');

                        // debug: show payload in console
                        console.log('Submitting form payload:', payload);

                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: payload,
                            success: function(resp) {
                                // Add new row to DataTable
                                let newIndex = table.rows().count() + 1;
                                table.row.add([
                                    newIndex,
                                    resp.nama ?? '-',
                                    resp.nama_kelas ?? '-',
                                    resp.nis ?? '-',
                                    resp.nomor_telepon ?? '-',
                                    resp.email ?? '-',
                                    '<a href="#" class="px-4 py-1 bg-blue-600 text-white text-xs rounded">Edit</a>'
                                ]).draw(false);

                                // close modal, reset form
                                $('#modalTambah').addClass('hidden').removeClass('flex');
                                $form[0].reset();

                                // optional: flash message
                                alert('Siswa berhasil ditambahkan');
                            },
                            error: function(xhr) {
                                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                    let msgs = [];
                                    $.each(xhr.responseJSON.errors, function(k, v) { msgs.push(v.join(', ')); });
                                    $('#formErrors').html(msgs.join('<br>'));
                                } else {
                                    // show raw response for debugging
                                    console.error('Server error response:', xhr.responseText);
                                    alert('Terjadi kesalahan, lihat console untuk detail.');
                                }
                            }
                        });
                    }

                    // bind both form submit and explicit button click to the same handler
                    $('#modalTambah form').on('submit', function(e) {
                        e.preventDefault();
                        submitTambah($(this));
                    });

                    $('#submitTambahBtn').on('click', function(e) {
                        e.preventDefault();
                        submitTambah($('#modalTambah form'));
                    });

                    // Edit Modal Functions
                    window.openEditModal = function(nis) {
                        $.ajax({
                            url: '/admin/siswa/edit/' + nis,
                            method: 'GET',
                            success: function(data) {
                                $('#edit_nis').val(data.nis);
                                $('#edit_nis_display').val(data.nis);
                                $('#edit_name').val(data.nama);
                                $('#edit_tanggal_lahir').val(data.tanggal_lahir);
                                $('#edit_email').val(data.email);
                                $('#edit_telepon').val(data.telepon);
                                $('#edit_kelas').val(data.id_kelas).trigger('change');
                                $('#edit_password').val('');
                                $('#formEditErrors').html('');

                                $('#modalEdit').removeClass('hidden').addClass('flex');
                            },
                            error: function(xhr) {
                                alert('Gagal memuat data siswa');
                            }
                        });
                    };

                    $('#closeModalEdit, #cancelModalEdit').on('click', function() {
                        $('#modalEdit').addClass('hidden').removeClass('flex');
                    });

                    function submitEdit($form) {
                        let nis = $('#edit_nis').val();
                        let url = '/admin/siswa/update/' + nis;

                        let payload = {
                            _token: $form.find('input[name="_token"]').val(),
                            _method: 'PUT',
                            name: $('#edit_name').val(),
                            tanggal_lahir: $('#edit_tanggal_lahir').val(),
                            kelas: $('#edit_kelas').val(),
                            email: $('#edit_email').val(),
                            telepon: $('#edit_telepon').val(),
                            password: $('#edit_password').val()
                        };

                        // client-side validation
                        let nama = $('#edit_name').val();
                        let kelas = $('#edit_kelas').val();
                        let errors = [];
                        if (!nama) errors.push('Nama harus diisi.');
                        // if (!kelas) errors.push('Kelas harus dipilih.');

                        if (errors.length) {
                            $('#formEditErrors').html(errors.join('<br>'));
                            return;
                        }

                        $('#formEditErrors').html('');

                        console.log('Submitting edit payload:', payload);

                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: payload,
                            success: function(resp) {
                                // Update the table row
                                table.rows().every(function() {
                                    let rowData = this.data();
                                    if (rowData[3] === nis) { // Check NIS column
                                        this.data([
                                            rowData[0], // Keep row number
                                            resp.nama ?? '-',
                                            resp.nama_kelas ?? '-',
                                            resp.nis ?? '-',
                                            resp.nomor_telepon ?? '-',
                                            resp.email ?? '-',
                                            '<button onclick=\"openEditModal(\'' + nis + '\')\" class=\"px-4 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700\">Edit</button>'
                                        ]).draw(false);
                                    }
                                });

                                // close modal, reset form
                                $('#modalEdit').addClass('hidden').removeClass('flex');
                                $form[0].reset();

                                alert('Data siswa berhasil diperbarui');
                            },
                            error: function(xhr) {
                                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                    let msgs = [];
                                    $.each(xhr.responseJSON.errors, function(k, v) { msgs.push(v.join(', ')); });
                                    $('#formEditErrors').html(msgs.join('<br>'));
                                } else {
                                    console.error('Server error response:', xhr.responseText);
                                    alert('Terjadi kesalahan, lihat console untuk detail.');
                                }
                            }
                        });
                    }

                    $('#formEdit').on('submit', function(e) {
                        e.preventDefault();
                        submitEdit($(this));
                    });

                    $('#submitEditBtn').on('click', function(e) {
                        e.preventDefault();
                        submitEdit($('#formEdit'));
                    });
                </script>

                </div>

            </div>
        </div>
    </div>

</body>
</html>
