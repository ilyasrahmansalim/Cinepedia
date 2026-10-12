-- Kosongkan tabel movies agar ID ter-reset dari 1
TRUNCATE TABLE `movies`;

-- Insert Data Asli Film Indonesia & Internasional Populer
INSERT INTO `movies` (`id`, `title`, `genre`, `release_year`, `duration`, `synopsis`, `poster_url`, `trailer_url`) VALUES
(1, 'Agak Laen', 'Comedy', 2024, '1 Jam 59 Menit', 'Empat sekawan pengelola wahana rumah hantu di pasar malam berusaha menyembunyikan mayat seorang politisi yang meninggal karena serangan jantung di dalam wahana mereka.', 'https://upload.wikimedia.org/wikipedia/id/3/30/Poster_Agak_Laen_2024.jpeg', 'https://www.youtube.com/watch?v=3R-iYF3k65w'),

(2, 'Siksa Kubur', 'Horror', 2024, '1 Jam 57 Menit', 'Setelah orang tuanya menjadi korban bom bunuh diri, Sita kehilangan kepercayaannya pada agama dan bertekad membuktikan bahwa siksa kubur itu tidak ada dengan masuk ke liang kubur.', 'https://image.tmdb.org/t/p/w780/dZRERqbTTZMm3P7QlXI4G2QSgFn.jpg', 'https://www.youtube.com/watch?v=G2XvCqSlsG0'),

(3, 'Oppenheimer', 'Biography', 2023, '3 Jam 0 Menit', 'Kisah perjalanan fisikawan J. Robert Oppenheimer dalam memimpin Proyek Manhattan untuk menciptakan bom atom pertama di dunia selama Perang Dunia II.', 'https://image.tmdb.org/t/p/w500/8Gxv8gSFCU0XGDykEGv3C21fKWf.jpg', 'https://www.youtube.com/watch?v=uYPbbksJxIg'),

(4, 'KKN di Desa Penari', 'Horror', 2022, '2 Jam 10 Menit', 'Enam mahasiswa yang sedang menjalankan program KKN di desa terpencil mulai mengalami kejadian mistis setelah melanggar aturan ghaib setempat.', 'https://upload.wikimedia.org/wikipedia/id/7/77/KKN_di_Desa_Penari_poster.jpg', 'https://www.youtube.com/watch?v=d_kS_s-m7-U'),

(5, 'Mencuri Raden Saleh', 'Action', 2022, '2 Jam 34 Menit', 'Sekelompok mahasiswa dengan keahlian berbeda merencanakan pencurian lukisan bersejarah mahakarya Raden Saleh dari Istana Negara demi menyelamatkan ayah salah satu anggota.', 'https://upload.wikimedia.org/wikipedia/id/f/f6/Mencuri_Raden_Saleh_poster.jpg', 'https://www.youtube.com/watch?v=DN2mO12p2rU'),

(6, 'Pengabdi Setan 2: Communion', 'Horror', 2022, '1 Jam 59 Menit', 'Rini dan keluarganya mencoba memulai hidup baru di sebuah rumah susun murah, namun teror supranatural yang lebih menakutkan kembali menghantui mereka saat badai menerjang.', 'https://image.tmdb.org/t/p/w500/b8c61WQx9RVF9hDk8Pq63kinbHu.jpg', 'https://www.youtube.com/watch?v=N_8IThL43iE'),

(7, 'Bumi Manusia', 'Drama', 2019, '3 Jam 1 Menit', 'Minke, seorang pemuda Jawa berpendidikan Eropa pada era kolonial, jatuh cinta pada Annelies, putri blasteran dari Nyai Ontosoroh yang pemberani.', 'https://upload.wikimedia.org/wikipedia/id/d/dc/Poster_film_Bumi_Manusia.jpg', 'https://www.youtube.com/watch?v=wXmIq3d4U2w'),

(8, 'Keluarga Cemara', 'Drama', 2018, '1 Jam 50 Menit', 'Sebuah keluarga kaya mendadak bangkrut akibat ditipu, memaksa sang ayah membawa istri dan anak-anaknya pindah ke desa terpencil untuk memulai hidup sederhana dari nol.', 'https://upload.wikimedia.org/wikipedia/id/0/05/Poster_film_Keluarga_Cemara_%282019%29.png', 'https://www.youtube.com/watch?v=7b7pS65b2qQ'),

(9, 'Cek Toko Sebelah', 'Comedy', 2016, '1 Jam 44 Menit', 'Erwin, seorang pemuda berpendidikan tinggi dengan karir sukses, dihadapkan pada dilema saat ayahnya yang sakit ingin mewariskan toko kelontong keluarga kepadanya.', 'https://upload.wikimedia.org/wikipedia/id/4/4e/Cek_Toko_Sebelah.jpg', 'https://www.youtube.com/watch?v=13Yv50SnoIs'),

(10, 'Interstellar', 'Sci-Fi', 2014, '2 Jam 49 Menit', 'Di balik konsep sains tingkat tinggi tentang fisika kuantum dan lubang cacing, sebuah tim penjelajah melintasi alam semesta demi mencari planet baru bagi umat manusia.', 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg', 'https://www.youtube.com/watch?v=zSWdZVtXT7E'),

(11, 'The Raid: Redemption', 'Action', 2011, '1 Jam 41 Menit', 'Tim pasukan khusus SWAT terjebak di dalam gedung apartment kumuh bertingkat tinggi yang dikuasai oleh gembong narkoba kejam dan ratusan pembunuh bayaran.', 'https://image.tmdb.org/t/p/w500/reQdvNCh49PuXqFSYaCMGk3bvdR.jpg', 'https://www.youtube.com/watch?v=PkULMOFsev4'),

(12, 'Laskar Pelangi', 'Drama', 2008, '2 Jam 5 Menit', 'Kisah perjuangan sepuluh anak dan dua guru pengabdi di sekolah miskin Belitung yang berjuang melawan kemiskinan dan keterbatasan demi meraih cita-cita.', 'https://upload.wikimedia.org/wikipedia/id/8/8e/Laskar_Pelangi_film.jpg', 'https://www.youtube.com/watch?v=f93N1ZhhI24');