<?php

/***
* SIMRS Khanza Lite from version 0.1 Beta
* About : Porting of SIMRS Khanza by Windiarto a.k.a Mas Elkhanza as web and mobile app.
* Last modified: 02 Pebruari 2018
* Author : drg. Faisol Basoro
* Email : dentix.id@gmail.com
* Licence under GPL
***/

$title = 'Rekam Medik Pasien';
include_once('config.php');
include_once('layout/header.php');
include_once('layout/sidebar.php');

?>

<section class="content">
    <div class="container-fluid">
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                  <div class="header">
                      <h2>
                          <?php echo $title; ?>
                          <?php if (isset($_POST['proses'])) { ?>
                            <small>Periode : <?php echo $_POST['tgl_awal']; ?> s/d <?php echo $_POST['tgl_akhir']; ?></small>
                          <?php } ?>
                        </h2>
                    </div>
                    <div class="body">
                    <?php
                    if (isset($_POST['proses'])) {
                        if (($_POST['tgl_awal'] == "")||($_POST['tgl_akhir'] == "")) {
                            redirect ('rekam-medik.php');
                        } else {
                            $q_pasien = query ("select * from pasien where no_rkm_medis = '$_POST[no_pasien]'");
                            $no_rkm_medis = $_POST['no_pasien'];
                            $data_pasien = fetch_array($q_pasien);
                    ?>
                        <dl class="dl-horizontal">
                            <dt>Nama Lengkap</dt>
                            <dd><?php echo $data_pasien['nm_pasien']; ?></dd>
                            <dt>No. RM</dt>
                            <dd><?php echo $data_pasien['no_rkm_medis']; ?></dd>
                            <dt>Jenis Kelamin</dt>
                            <dd><?php echo $data_pasien['jk']; ?></dd>
                            <dt>Umur</dt>
                            <dd><?php echo $data_pasien['umur']; ?></dd>
                        </dl>
                        <hr>
                               <button class="btn bg-cyan waves-effect m-t-15 m-b-15" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">Berkas RM Lama</button>
                               <button class="btn bg-red waves-effect m-t-15 m-b-15" type="button" data-toggle="modal" data-target="#rmlamaModal">Upload RM Lama</button>
                      
                               <div class="collapse" id="collapseExample">
                                 <div class="well">
                                             <div id="aniimated-thumbnials" class="list-unstyled row clearfix">
                                             <?php
                                             $sql_rmlama = query("SELECT * FROM berkas_digital_perawatan WHERE kode = '003' AND no_rawat IN (SELECT no_rawat FROM reg_periksa WHERE no_rkm_medis = '$no_rkm_medis')");
                                             $no=1;
                                             while ($row_rmlama = fetch_array($sql_rmlama)) {
                                                 echo '<div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">';
                                                 echo '<a href="'.URLSIMRS.'/berkasrawat/'.$row_rmlama[2].'" data-sub-html=""><img class="img-responsive thumbnail"  src="'.URLSIMRS.'/berkasrawat/'.$row_rmlama[2].'"></a>';
                                                 echo '</div>';
                                                 $no++;
                                             }
                                             ?>
                                             <?php
                                             $sql_rmlama = query("SELECT * FROM berkas_digital_perawatan WHERE kode = '006' AND no_rawat IN (SELECT no_rawat FROM reg_periksa WHERE no_rkm_medis = '$no_rkm_medis')");
                                             $no=1;
                                             while ($row_rmlama = fetch_array($sql_rmlama)) {
                                                 echo '<div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">';
                                                 echo '<a href="'.URLSIMRS.'/berkasrawat/'.$row_rmlama[2].'" data-sub-html=""><img class="img-responsive thumbnail"  src="'.URLSIMRS.'/berkasrawat/'.$row_rmlama[2].'"></a>';
                                                 echo '</div>';
                                                 $no++;
                                             }
                                             ?>
                                             </div>
                                 </div>
                               </div>
                               <div class="clearfix"></div>
                      
                        <table id="datatable" class="table table-bordered table-striped table-hover display nowrap" width="100%">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nomor Rawat</th>
                                    <th>Klinik/Ruangan</th>
                                    <th>Keluhan</th>
                                    <th>Pemeriksaan</th>
                                    <th>Diagnosa</th>
                                    <th>Obat</th>
                                    <?php if(FKTL == true) { ?>
                                    <th>Laboratorium</th>
                                    <th>Radiologi</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $q_kunj = query ("SELECT tgl_registrasi, no_rawat, status_lanjut FROM reg_periksa WHERE no_rkm_medis = '$no_rkm_medis' AND tgl_registrasi BETWEEN '$_POST[tgl_awal]' AND '$_POST[tgl_akhir]' ORDER BY tgl_registrasi DESC");
                            if(num_rows($q_kunj) >= 1) {
                            while ($data_kunj = fetch_array($q_kunj)) {
                                $tanggal_kunj   = $data_kunj[0];
                                $no_rawat_kunj = $data_kunj[1];
                                $status_lanjut = $data_kunj[2];
                            ?>
                                <tr>
                                    <td><?php echo $tanggal_kunj; ?></td>
                                    <td><?php echo $no_rawat_kunj; ?></td>
                                    <td>
                                      <?php
                                      if($status_lanjut == 'Ralan') {
                                        $sql_poli = fetch_assoc(query("SELECT a.nm_poli FROM poliklinik a, reg_periksa b WHERE b.no_rawat = '$no_rawat_kunj' AND a.kd_poli = b.kd_poli"));
                                        echo $sql_poli['nm_poli'];
                                      } else {
                                        echo 'Rawat Inap';
                                      }
                                      ?>
                                    </td>
                                      <?php
                                      if($status_lanjut == 'Ralan') {
                                        $sql_riksaralan = fetch_assoc(query("SELECT keluhan, pemeriksaan FROM pemeriksaan_ralan WHERE no_rawat = '$no_rawat_kunj'"));
                                        echo "<td>".$sql_riksaralan['keluhan']."</td>";
                                        echo "<td>".$sql_riksaralan['pemeriksaan']."</td>";
                                      } else {
                                        $sql_riksaranap = fetch_assoc(query("SELECT keluhan, pemeriksaan FROM pemeriksaan_ranap WHERE no_rawat = '$no_rawat_kunj'"));
                                        echo "<td>".$sql_riksaranap['keluhan']."</td>";
                                        echo "<td>".$sql_riksaranap['pemeriksaan']."</td>";
                                      }
                                      ?>
                                    <td>
                                        <ul style="list-style:none;">
                                        <?php
                                        $sql_dx = query("SELECT a.kd_penyakit, a.nm_penyakit FROM penyakit a, diagnosa_pasien b WHERE a.kd_penyakit = b.kd_penyakit AND b.no_rawat = '$no_rawat_kunj'");
                                        $no=1;
                                        while ($row_dx = fetch_array($sql_dx)) {
                                            echo '<li>'.$no.'. '.$row_dx[1].' ('.$row_dx[0].')</li>';
                                            $no++;
                                        }
                                        ?>
                                        </ul>
                                    </td>
                                    <td>
                                        <ul style="list-style:none;">
                                        <?php
                                        $sql_obat = query("select detail_pemberian_obat.jml, databarang.nama_brng from detail_pemberian_obat inner join databarang on detail_pemberian_obat.kode_brng=databarang.kode_brng where detail_pemberian_obat.no_rawat= '$no_rawat_kunj'");
                                        $no=1;
                                        while ($row_obat = fetch_array($sql_obat)) {
                                            echo '<li>'.$no.'. '.$row_obat[1].' ('.$row_obat[0].')</li>';
                                            $no++;
                                        }
                                        ?>
                                        </ul>
                                    </td>
                                    <?php if(FKTL == true) { ?>
                                    <td>
                                        <ul style="list-style:none;">
                                        <?php
                                        $sql_lab = query("select template_laboratorium.Pemeriksaan, detail_periksa_lab.nilai, template_laboratorium.satuan, detail_periksa_lab.nilai_rujukan, detail_periksa_lab.keterangan from detail_periksa_lab inner join  template_laboratorium on detail_periksa_lab.id_template=template_laboratorium.id_template  where detail_periksa_lab.no_rawat= '$no_rawat_kunj'");
                                        $no=1;
                                        while ($row_lab = fetch_array($sql_lab)) {
                                            echo '<li>'.$no.'. '.$row_lab[0].' ('.$row_lab[3].') = '.$row_lab[1].' '.$row_lab[2].'</li>';
                                            $no++;
                                        }
                                        ?>
                                        </ul>
                                    </td>
                                    <td>
                                        <div id="animated-thumbnails" class="list-unstyled row clearfix">
                                        <?php
                                        $sql_rad = query("select * from gambar_radiologi where no_rawat= '$no_rawat_kunj'");
                                        $no=1;
                                        while ($row_rad = fetch_array($sql_rad)) {
                                            echo '<div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">';
                                            echo '<a href="'.$_SERVER['PHP_SELF'].'?action=radiologi&no_rawat='.$no_rawat_kunj.'" class="title"><img class="img-responsive thumbnail"  src="'.SIMRSURL.'/radiologi/'.$row_rad[3].'"></a>';
                                            echo '</div>';
                                            $no++;
                                        }
                                        ?>
                                      </div>
                                    </td>
                                    <?php } ?>
                                </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                    <tr>
                                        <td>Blank..!!</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <?php if(FKTL == true) { ?>
                                        <td></td>
                                        <td></td>
                                        <?php } ?>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                        }
                        echo "<hr>";
                    }
                    ?>
                    <div class="body">
                    <form method="post" action="">
                        <dl class="dl-horizontal">
                            <dt>Pasien</dt>
                            <dd><select name="no_pasien" class="pasien" style="width:100%"></select></dd><br/>
                            <dt>Periode</dt>
                            <dd><input type="text" class="datepicker form-control" name="tgl_awal">
                            <dt></dt><dd>s/d</dd>
                            <dt></dt><dd><input type="text" class="datepicker form-control" name="tgl_akhir"></dd><br/>
                            <dt></dt><dd><input type="submit" class="btn btn-primary waves-effect" name="proses" value="Proses"> <button type="reset" class="btn btn-red waves-effect" name="batal" style="background-color: #f7f7f7 !important; color: #555; border-color: #ccc; text-shadow: none; -webkit-appearance: none;">Batal</button></dd>
                        </dl>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

                          <?php
                            if (isset($_POST['ok_berdig'])) {
                              $get_periksa = fetch_assoc(query("SELECT no_rawat FROM reg_periksa WHERE no_rkm_medis = '{$_POST['__no_rkm_medis']}' ORDER BY no_rawat DESC LIMIT 1"));
                              //$get_periksa = fetch_assoc(query("SELECT no_rawat FROM reg_periksa WHERE no_rkm_medis = '035469' ORDER BY no_rawat DESC LIMIT 1"));
                              if($_FILES['file']['name']!=='') {
                                $tmp_name = $_FILES["file"]["tmp_name"];
                                $namefile = $_FILES["file"]["name"];
                                $explode = explode(".", $namefile);
                                $ext = end($explode);
                                $image_name = "berkasdigital-".time().".".$ext;
                                move_uploaded_file($tmp_name,WEBAPPS."/berkasrawat/pages/upload/".$image_name);
                                $lokasi_berkas = 'pages/upload/'.$image_name;
                                //$insert_berkas = query("INSERT INTO berkas_digital_perawatan VALUES('2019/10/10/000040','003', '$lokasi_berkas')");
                                $insert_berkas = query("INSERT INTO berkas_digital_perawatan VALUES('{$get_periksa['no_rawat']}','003', '$lokasi_berkas')");
                                if($insert_berkas) {
                                  //set_message('Berkas digital perawatan telah ditersimpan.');
                                  //redirect("./?module=RawatJalan&page=index");
                                }
                              }
                            }
                          ?>


    <div class="modal fade" id="rmlamaModal" tabindex="-1" role="dialog" aria-labelledby="rmlamaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="rmlamaModalLabel">Berkas Digital</h4>
                </div>
                <div class="modal-body">
                                <div class="body">
                                  <form id="form_validation" name="berdigi" action="" method="POST"  enctype="multipart/form-data">
                                      <label for="email_address">Unggah Berkas Digital Perawatan</label>
                                      <div class="form-group">
                                          <img id="image_upload_preview" width="200px" src="<?php echo URL; ?>/modules/RawatJalan/images/upload_berkas.png" onclick="upload_berkas()" style="cursor:pointer;" />
                                          <br/>
                                        	<input name="__no_rkm_medis" type="hidden" value="<?php echo $data_pasien['no_rkm_medis']; ?>">
                                          <input name="file" id="inputFile" type="file" style="display:none;"/>
                                      </div>
                                      <button type="submit" name="ok_berdig" value="ok_berdig" class="btn bg-indigo waves-effect" onclick="this.value=\'ok_berdig\'">UPLOAD BERKAS</button>
                                  </form>
                                </div>
                </div>
            </div>
        </div>
    </div>

<?php
include_once('layout/footer.php');
?>
<script>
  $('#rekammedik').dataTable( {
        "bInfo" : true,
      	"scrollX": true,
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "oLanguage": {
            "sProcessing":   "Sedang memproses...",
            "sLengthMenu":   "Tampilkan _MENU_ entri",
            "sZeroRecords":  "Tidak ditemukan data yang sesuai",
            "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
            "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
            "sInfoPostFix":  "",
            "sSearch":       "Cari:",
            "sUrl":          "",
            "oPaginate": {
                "sFirst":    "«",
                "sPrevious": "‹",
                "sNext":     "›",
                "sLast":     "»"
            }
        },
        "order": [[ 0, "asc" ]],
  } );
</script>
<script type="text/javascript">

    function formatData (data) {
        var $data = $(
            '<b>'+ data.id +'</b> - <i>'+ data.text +'</i>'
        );
        return $data;
    };

    function formatDataTEXT (data) {
        var $data = $(
            '<b>'+ data.text +'</b>'
        );
        return $data;
    };

      $('.pasien').select2({
        placeholder: 'Pilih nama/no.RM pasien',
        ajax: {
          url: 'includes/select-pasien.php',
          dataType: 'json',
          delay: 250,
          processResults: function (data) {
            return {
              results: data
            };
          },
          cache: true
        },
        templateResult: formatData,
        minimumInputLength: 3
      });

</script>
