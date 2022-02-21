<?php
class Dijkstra
{
    function jalurTerpendek($arg_graph, $simpulAwal, $simpulTujuan)
    {
        // Simpul_awal == simpul_tujuan, maka die()
        if ($simpulAwal == $simpulTujuan) {
            return json_encode(['status' => 'error', 'error' => 'lokasi_anda_sudah_dekat', 'teks' => 'Lokasi Anda Sudah Dekat', 'content' => '']);
        }

        // Simpul_Awal OR Simpul_Tujuan Not Found
        if (!array_key_exists($simpulAwal, $arg_graph) || !array_key_exists($simpulTujuan, $arg_graph)) {
            return print_r(json_encode(['status' => 'error', 'error' => 'simpul_input_tidak_ditemukan', 'teks' => "could not find the input : $simpulAwal or $simpulTujuan", 'content' => '']));
        }

        $graph = $arg_graph;
        $simpul_awal = $simpulAwal;
        $simpul_maju = $simpulAwal;
        $simpul_tujuan = $simpulTujuan;
        $jml_simpul = count($arg_graph);

        $simpulYangDikerjakan = array();

        // Untuk menyimpan nilai-nilai yang ditandai
        $simpulYangSudahDikerjakan_bawah = array();

        $nilaiSimpulYangDitandai = 0;
        $nilaiSimpulFixYangDitandai = 0;

        // Handle Perulangan
        for ($perulangan = 0; $perulangan < 1; $perulangan++) {
            $perbandinganSemuaBobot = array();

            if (!in_array($simpul_maju, $simpulYangDikerjakan)) {
                array_push($simpulYangDikerjakan, $simpul_maju);
            }
            for ($perulanganSimpul = 0; $perulanganSimpul < count($simpulYangDikerjakan); $perulanganSimpul++) {
                $jumlah_baris = count($graph[$simpulYangDikerjakan[$perulanganSimpul]]);

                $bobot_baris = array();

                $baris_belum_dikerjakan = 0;

                for ($start_baris = 0; $start_baris < $jumlah_baris; $start_baris++) {
                    $ruas_dan_bobot = $graph[$simpulYangDikerjakan[$perulanganSimpul]][$start_baris];

                    $explode = explode('->', $ruas_dan_bobot);

                    if (count($explode) == 2) {
                        $baris_belum_dikerjakan += 1;

                        if (!empty($simpulYangSudahDikerjakan_bawah)) {
                            if (in_array($simpulYangDikerjakan[$perulanganSimpul], $simpulYangSudahDikerjakan_bawah)) {
                                $nilaiSimpulFixYangDitandai = 0;
                            } else {
                                $nilaiSimpulYangDitandai = $nilaiSimpulFixYangDitandai;
                            }
                        }

                        array_push($bobot_baris, ($explode[1]+$nilaiSimpulYangDitandai));

                        $graph[$simpulYangDikerjakan[$perulanganSimpul]][$start_baris] = $explode[0] . "->" . $explode[1] . $nilaiSimpulYangDitandai;
                    }
                }
                if ($baris_belum_dikerjakan > 0) {
                    for ($index_bobot = 0; $index_bobot < count($bobot_baris); $index_bobot++) {
                        if ($bobot_baris[$index_bobot] <= $bobot_baris[0]) {
                            $bobot_baris[0] = $bobot_baris[$index_bobot];
                        }
                    }

                    array_push($perbandinganSemuaBobot, $bobot_baris[0]);
                } else {
                }

                if (!in_array($simpulYangDikerjakan[$perulanganSimpul], $simpulYangSudahDikerjakan_bawah)) {
                    array_push($simpulYangSudahDikerjakan_bawah, $simpulYangDikerjakan[$perulanganSimpul]);
                }
            }

            for ($min_indexAntarBobotYangDitandai = 0; $min_indexAntarBobotYangDitandai < count($perbandinganSemuaBobot); $min_indexAntarBobotYangDitandai++) {
                if ($perbandinganSemuaBobot[$min_indexAntarBobotYangDitandai] <= $perbandinganSemuaBobot[0]) {
                    $perbandinganSemuaBobot[0] = $perbandinganSemuaBobot[$min_indexAntarBobotYangDitandai];
                }
            }

            $indexAwalAsli = 0;
            $baris_belum_dikerjakan1 = 0;
            $dapat_indexAsliBobot = 0;
            $simpul_lama = 0;

            foreach ($simpulYangDikerjakan as $idx => $v) {
                $length_baris = $graph[$simpulYangDikerjakan[$idx]];

                for ($baris1 = 0; $baris1 < $length_baris; $baris1++) {
                    if (isset($graph[$simpulYangDikerjakan[$indexAwalAsli]][$baris1])) {
                        $bobot_baris_dan_ruas1 = $graph[$simpulYangDikerjakan[$indexAwalAsli]][$baris1];
                        $explode1 = array();
                        $explode1 = explode('->', $bobot_baris_dan_ruas1);
                        if (count($explode1) == 2) {
                            if ($perbandinganSemuaBobot[0] == $explode1[1]) {
                                $dapat_indexAsliBobot = $baris1;
                                $simpul_lama = $simpulYangDikerjakan[$indexAwalAsli];
                                $simpul_maju = $explode1[0];
                                $baris_belum_dikerjakan1 += 1;
                            }
                        }
                    } 
                    else {
                        break;
                    }
                    
                }

                $indexAwalAsli++;
            }

            if ($baris_belum_dikerjakan1 > 0) {
                $graph[$simpul_lama][$dapat_indexAsliBobot] = $graph[$simpul_lama][$dapat_indexAsliBobot] . "->y";

                for ($min_kolom = 0; $min_kolom < $jml_simpul; $min_kolom++) {
                    $length_baris1 = count($graph[$min_kolom]);

                    for ($min_baris = 0; $min_baris < $length_baris1; $min_baris++) {
                        if (isset($graph[$min_kolom][$min_baris])) {
                            $ruasYangAkanDihapus = $graph[$min_kolom][$min_baris];
                            $explode3 = explode('->', $ruasYangAkanDihapus);
                            if (count($explode3) == 2) {
                                if ($explode3[0] == $simpul_maju) {
                                    $graph[$min_kolom][$min_baris] = $graph[$min_kolom][$min_baris] + "->t";
                                }
                            }
                        }
                    }
                }
            }
            if (!isset($perbandinganSemuaBobot[0]))
                return json_encode(['status' => 'error', 'error' => 'alur_graph_anda_salah', 'teks' => 'alur graph Anda Salah', 'content' => '']);

            $nilaiSimpulFixYangDitandai = $perbandinganSemuaBobot[0];

            if ($simpul_maju != $simpul_tujuan) {
                --$perulangan;
            } else {
                break;
            }
        }

        $gabungSimpulPilihan = array();
        for ($h = 0; $h < $jml_simpul; $h++) {
            $length_baris2 = count($graph[$h]);

            for($n = 0; $n < $length_baris2; $n++){
                if(isset($graph[$h][$n])){
                    $str_graph = $graph[$h][$n];
                    if(substr($str_graph, (strlen($str_graph)-1), strlen($str_graph)) == "y"){
                        $explode4 = explode('->', $graph[$h][$n]);
                        $simpulGabung = $h . "-" . $explode4[0];

                        array_push($gabungSimpulPilihan, $simpulGabung);
                    }
                }
            }
        }

        $simpulFix_finish = array();

        array_push($simpulFix_finish, $simpul_tujuan);

        $simpul_explode = $simpul_tujuan;
        for($v = 0; $v < 1; $v++){
            for($w = 0; $w < count($gabungSimpulPilihan); $w++){
                $explode_simpul = $gabungSimpulPilihan[$w];
                $explode5 = explode('-', $explode_simpul);
                if($simpul_explode == $explode5[1]{
                    array_push($simpulFix_finish, $explode5[0]);
                    $simpul_explode = $explode5[0];
                }
                if($simpul_explode == $simpul_awal){
                    break;
                }
            }

            if($simpul_awal != $simpul_explode){
                --$v;
            }
            else{
                break;
            }
        }

        $simpulFix_finish_reverse = array_reverse($simpulFix_finish);

        $jalur_terpendek = "";
        for($x = 0; $x < count($simpulFix_finish_reverse); $x++){
            if($x == (count($simpulFix_finish_reverse)-1)){
                $jalur_terpendek .= $simpulFix_finish_reverse[$x];
            }
            else{
                $jalur_terpendek .= $simpulFix_finish_reverse[$x] . "->";

            }

            $json['status'] = 'success';
            $json['success'] = 'generate_jalur_terpendek';
            $json['teks'] = 'Jalur berhasil dibuat';
            $json['content'] = $jalur_terpendek;

            return json_encode($json);
        }
?>