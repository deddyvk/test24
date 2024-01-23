PERHATIAN !!!
* WINDOWS
Berikut beberapa file yang dibutuhkan untuk menjalankan sms terjadwal secara scheduler di Windows, dan konfigurasikan lokasi path file yang ada dengan lokal di komputer anda. 
Silahkan klik kanan file berikut dan buka dengan editor text(Notepad/Notepad++ dll)
1. import_win.bat
Fungsi		:: memanggil/menjalankan aplikasi 
Isi 		:: C:\AppServ\www\sms\tools\curl localhost/sms/index.php/sms/sms_jadwal/
Penjelasan 	:: [C:\AppServ\www\sms\tools\curl] -- lokasi folder tempat file curl.exe berada, default ada dalam folder tools di aplikasi
		   [localhost/sms/index.php/sms/sms_jadwal/] -- alamat url modul sms terjadwal dari aplikasi

2. task_schedule_win.bat
Fungsi		:: Membuat task schedule di windows dengan perintah menjalankan file import_win.bat pada jam tertentu
Isi 		:: SCHTASKS /Create /SC MINUTE /MO 5 /TN SMSJadwal /TR C:\AppServ\www\sms\tools\import_win.bat 
Penjelasan 	:: [SMSJadwal] -- nama dari schedule task yang dibuat
		   [C:\AppServ\www\sms\tools\import_win.bat] -- lokasi file yang akan dijalankan sesuai time schedule
		   [/SC MINUTE /MO 5] -- schedule akan dijalankan tiap 5 menit	
			   
Setelah semua selesai dikonfigurasi, siilahkan jalankan file berikut sesuai dengan urutan
1. import_win.bat
2. task_schedule_win.bat

* LINUX
Berikut beberapa file yang dibutuhkan untuk menjalankan import xml secara scheduler di Linux, dan konfigurasikan lokasi path file yang ada dengan lokal di komputer anda. 
Silahkan klik kanan file berikut dan buka dengan editor text(Notepad/Notepad++ dll)
1. import_linux.sh
Fungsi		:: memanggil/menjalankan aplikasi 
Isi 		:: curl localhost/sms/index.php/sms/sms_jadwal/
Penjelasan 	:: [curl] -- cek dahulu apakah curl sudah diinstall atau belum di Linux, jika belum silahkan install terlebih dahulu
		   [localhost/sms/index.php/sms/sms_jadwal/] -- alamat url modul import scheduler dari aplikasi
			   
2. task_schedule_linux.sh
Fungsi		:: Membuat task schedule di linux dengan perintah menjalankan file import_linux.sh pada jam tertentu
Isi 		:: crontab -l | { cat; echo "*/5 * * * * /home/sms/tools/import_linux.sh"; } | SMSJadwal -
Penjelasan 	:: [*/5 * * * *] - mengatur waktu scheduler akan dijalankan setiap 5 menit 
		   [/home/sms/tools/import_linux.sh] -- lokasi file yang akan dijalankan sesuai time schedule
		   [SMSJadwal] -- nama dari schedule task yang dibuat
			   
Setelah semua selesai dikonfigurasi, siilahkan jalankan file berikut sesuai dengan urutan
1. import_linux.sh
2. task_schedule_linux.sh
