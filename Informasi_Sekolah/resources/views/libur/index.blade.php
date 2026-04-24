<!DOCTYPE html>
<html>
<head>
    <title>Info Libur Sekolah</title>
</head>
<body style="font-family: sans-serif; background-color: #f9f9f9; padding: 20px;">
    <div style="max-width: 800px; margin: 0 auto; background: white; border-radius: 8px; padding: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <h1 style="border-bottom: 2px solid #eee; padding-bottom: 10px;">Info Libur Sekolah</h1>
        <h3 style="color: #666;">Deskripsi informasi</h3>

        <div style="margin-top: 20px;">
            @forelse($semuaLibur as $libur)
            <div style="background-color: #8fa0a8; color: white; padding: 15px; margin-bottom: 15px; border-radius: 4px;">
                <p style="margin: 0; font-weight: bold; font-size: 14px; opacity: 0.8;">{{ \Carbon\Carbon::parse($libur->tanggal)->format('d F Y') }}</p>
                <h3 style="margin: 10px 0;">{{ $libur->judul }}</h3>
                <p style="margin: 0;">{{ $libur->deskripsi }}</p>
            </div>
            @empty
            <p>Belum ada data libur.</p>
            @endforelse
        </div>

        <div style="background-color: #ffcccc; padding: 20px; margin-top: 40px; border-radius: 8px;">
            <h3>Komen/pertanyaan</h3>

            @if(session('sukses'))
                <p style="color: green; font-weight: bold;">{{ session('sukses') }}</p>
            @endif

            <form action="/libur/komentar" method="POST" style="margin-bottom: 20px;">
                @csrf
                <div style="margin-bottom: 10px;">
                    <input type="text" name="nama" placeholder="Nama..." required style="padding: 8px; width: 300px;">
                </div>
                <div style="margin-bottom: 10px;">
                    <textarea name="isi" placeholder="Tulis komentar..." required style="padding: 8px; width: 300px; min-height: 80px;"></textarea>
                </div>
                <button type="submit" style="padding: 8px 15px; background: #666; color: white; border: none; cursor: pointer; border-radius: 4px;">Kirim</button>
            </form>

            <div style="border-top: 1px solid #ff9999; padding-top: 15px;">
                @foreach($semuaKomentar as $komen)
                <div style="margin-bottom: 15px;">
                    <strong style="display: block; color: #333;">{{ $komen->nama }} <span style="font-size: 12px; color: #777; font-weight: normal;">• {{ $komen->created_at->diffForHumans() }}</span></strong>
                    <p style="margin: 5px 0 0 0; color: #555;">{{ $komen->isi }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>
