# Web-Poliklinik
# Database
Database poliklinik
Table structure for table daftar_poli
Column	Type	Null	Default	Links to	Comments	Media type
id	int(11)	No			
id_pasien	int(11)	No		pasien (id)		
id_jadwal	int(11)	No		jadwal_periksa (id)		
keluhan	text	No			
no_antrian	int(11)	Yes	NULL		

Table structure for table detail_periksa
Column	Type	Null	Default	Links to	Comments	Media type
id	int(11)	No			
id_periksa	int(11)	No		periksa (id)		
id_obat	int(11)	No		obat (id)		

Table structure for table dokter
Column	Type	Null	Default	Links to	Comments	Media type
id	int(11)	No			
nama	varchar(150)	No			
alamat	varchar(255)	Yes	NULL		
no_hp	int(15)	No			
id_poli	int(11)	No		poli (id_poli)		

Table structure for table jadwal_periksa
Column	Type	Null	Default	Links to	Comments	Media type
id	int(11)	No			
id_dokter	int(11)	No		dokter (id)		
hari	varchar(10)	No			
jam_mulai	time	No			
jam_selesai	time	No			

Table structure for table obat
Column	Type	Null	Default	Links to	Comments	Media type
id	int(11)	No			
nama_obat	varchar(50)	No			
kemasan	varchar(35)	Yes	NULL		
harga	int(10)	Yes	0		

Table structure for table pasien
Column	Type	Null	Default	Links to	Comments	Media type
id	int(11)	No			
nama	varchar(150)	No			
alamat	varchar(255)	No			
no_ktp	int(10)	No			
no_hp	int(10)	No			
no_rm	char(10)	Yes	NULL		

Table structure for table periksa
Column	Type	Null	Default	Links to	Comments	Media type
id	int(11)	No			
id_daftar_poli	int(11)	No		daftar_poli (id)		
tgl_periksa	date	No			
catatan	text	No			
biaya_periksa	int(11)	Yes	NULL		

Table structure for table poli
Column	Type	Null	Default	Links to	Comments	Media type
id_poli	int(11)	No			
nama_poli	varchar(25)	No			
keterangan	text	Yes	NULL		


Table structure for table users
Column	Type	Null	Default	Links to	Comments	Media type
id	int(11)	No			
username	varchar(50)	No			
password	varchar(55)	No			
role	enum('admin', 'dokter', 'pasien')	No			
created_at	timestamp	No	current_timestamp()		

