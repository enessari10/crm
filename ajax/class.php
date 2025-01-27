<?php

date_default_timezone_set('Europe/Istanbul');

class Process
{


    // SQL FUNC
    public function sqlInsert($db, $tble, $cols, $values)
    {

        $insertquery = "INSERT INTO " . $tble . " (" . $cols . ") VALUES (" . $values . ")";
        echo $insertquery;
        if (mysqli_query($db, $insertquery)) {
            return true;
        } else {
            return false;
        }
    }
    public function getCompanyNameWithId($db, $id)
    {

        $result =  mysqli_query($db, "SELECT * FROM Firmalar WHERE firma_id ='$id'");
        while ($row = mysqli_fetch_assoc($result)) {
            return $row['firma_adi'];
        }
    }

    // CONVERT DATE TYPE

    public function convertDateLocaleTR($date)
    {
        return date("d-m-Y", strtotime($date));
    }

    //HOME SCREEN DATA
    public function getAntivirusDay($db)
    {

        $result =  mysqli_query($db, "SELECT COUNT(*) FROM Firmalar where DATEDIFF(Firmalar.antivirus_tarihi ,NOW()) <= 15;");
        while ($row = mysqli_fetch_assoc($result)) {
            return $row['COUNT(*)'];
        }
    }

    public function getBerqnetDay($db)
    {

        $result =  mysqli_query($db, "SELECT COUNT(*) FROM Firmalar where DATEDIFF(Firmalar.berqnet_tarihi ,NOW()) <= 15;");
        while ($row = mysqli_fetch_assoc($result)) {
            return $row['COUNT(*)'];
        }
    }

    public function getWebDay($db)
    {

        $result =  mysqli_query($db, "SELECT COUNT(*) FROM Firmalar where DATEDIFF(Firmalar.web_tarihi ,NOW()) <= 15;");
        while ($row = mysqli_fetch_assoc($result)) {
            return $row['COUNT(*)'];
        }
    }

    public function getWaitingServiceCount($db)
    {

        $result =  mysqli_query($db, "SELECT COUNT(*) FROM Talepler where talep_durum='Bekliyor'");
        while ($row = mysqli_fetch_assoc($result)) {
            return $row['COUNT(*)'];
        }
    }

    //GLOBAL ALERTS
    public function successAlert($message)
    {
        return '<div class="alert alert-success" role="alert">' . $message . '</div>';
    }

    public function errorAlert($message)
    {
        return '<div class="alert alert-danger" role="alert">' . $message . '</div>';
    }

    //GET DATA
    public function getHomeInfo($db, $search)
    {

        $query = "SELECT * FROM Firmalar where DATEDIFF(Firmalar.$search ,NOW()) <= 15;";
        if ($result = $db->query($query)) {
            while ($row = $result->fetch_assoc()) {
                return $row;
                $result->free();
            }
        }
    }

    public function getCompanies($db)
    {

        $query = "SELECT * FROM Firmalar ORDER BY firma_adi;";
        if ($result = $db->query($query)) {
            while ($row = $result->fetch_assoc()) {
                echo '<option value=' . $row['firma_id'] . '>' . $row['firma_adi'] . '</option>';
            }
        }
    }


    public function getAllReports($db)
    {

        $query = "SELECT * FROM  Raporlar  ORDER BY servis_tarihi DESC";
        if ($result = $db->query($query)) {
            while ($row = $result->fetch_assoc()) {
                $talepTarihi = $this->convertDateLocaleTR($row["talep_tarihi"]);
                $servisTarihi = $this->convertDateLocaleTR($row["servis_tarihi"]);
                echo '
                <tr>
                <td>' . $row["talep_eden"] . '</td>
                <td>' . $talepTarihi . '</td>
                <td>' . $servisTarihi . '</td>
                <td>' . $row["personel"] . '</td>
                <td> <div class="btn-group">
                <button type="button" class="btn btn-gradient-primary btn-sm" data-bs-toggle="dropdown">İşlem Seç</button>
                <div class="dropdown-menu">
                  <a href="report-detail.php?report_id=' . $row['id'] . '" class="dropdown-item">Detayı görüntüle</a>
                </div>
                </td>
                </tr>';
            }
        }
    }

    public function getAllInvoices($db)
    {

        $query = "SELECT * FROM Fatura_Edilecekler INNER JOIN Firmalar ON Fatura_Edilecekler.firma_id = Firmalar.firma_id ORDER BY Fatura_Edilecekler.tarih DESC";
        if ($result = $db->query($query)) {
            while ($row = $result->fetch_assoc()) {
                $tarih = $this->convertDateLocaleTR($row["tarih"]);
                echo '
                <tr>
                <td>' . $row["aciklama"] . '</td>
                <td>' . $row["firma_adi"] . '</td>
                <td>' . $tarih . '</td>
                <td> <div class="btn-group">
                <button type="button" class="btn btn-gradient-primary btn-sm" data-bs-toggle="dropdown">İşlem Seç</button>
                <div class="dropdown-menu">
                  <a href="" class="dropdown-item">Sil</a>
                </div>
                </td>
                </tr>';
            }
        }
    }

    public function getAllUsers($db)
    {

        $query = "SELECT * FROM Users INNER JOIN Firmalar ON Users.company_id = Firmalar.firma_id ORDER BY firma_adi DESC;";
        if ($result = $db->query($query)) {
            while ($row = $result->fetch_assoc()) {

                $tarih = $this->convertDateLocaleTR($row["created_date"]);
                if ($row["role"] == "customer") {
                    $role = "Müşteri";
                } else if ($row["role"] == "mikroes_worker") {
                    $role = "Mikroes Çalışanı";
                } else {
                    $role = "Admin";
                }
                echo '
                <tr>
                <td>' . $row["firma_adi"] . '</td>
                <td>' . $row["email"] . '</td>
                <td>' . $role . '</td>
                <td>' . $row["password"] . '</td>
                <td>' . $tarih . '</td>
                <td> <div class="btn-group">
                <button type="button" class="btn btn-gradient-primary btn-sm" data-bs-toggle="dropdown">İşlem Seç</button>
                <div class="dropdown-menu">
                  <a href="user-detail.php?user_id=' . $row['user_id'] . '" class="dropdown-item">Güncelle</a>
                  <a href="" class="dropdown-item">Sil</a>
                </div>
                </td>
                </tr>';
            }
        }
    }

    public function getAllCompanies($db)
    {

        $query = "SELECT * FROM Firmalar ORDER BY firma_adi DESC;";
        if ($result = $db->query($query)) {
            while ($row = $result->fetch_assoc()) {

                $berqnet = $this->convertDateLocaleTR($row["berqnet_tarihi"]);
                $web = $this->convertDateLocaleTR($row["web_tarihi"]);
                $antivirus = $this->convertDateLocaleTR($row["antivirus_tarihi"]);

                echo '
                <tr>
                <td>' . $row["firma_adi"] . '</td>
                <td>' . $berqnet . '</td>
                <td>' . $antivirus . '</td>
                <td>' . $web . '</td>
                <td> <div class="btn-group">
                <button type="button" class="btn btn-gradient-primary btn-sm" data-bs-toggle="dropdown">İşlem Seç</button>
                <div class="dropdown-menu">
                  <a href="company-detail.php?company_id=' . $row['firma_id'] . '" class="dropdown-item">Güncelle</a>
                  <a href="" class="dropdown-item">Sil</a>
                </div>
                </td>
                </tr>';
            }
        }
    }

    public function getAllSatisFactions($db)
    {

        $query = "SELECT * FROM Memnuniyetler INNER JOIN Raporlar ON Memnuniyetler.rapor_id = Raporlar.id";
        if ($result = $db->query($query)) {
            while ($row = $result->fetch_assoc()) {

                $reportDate = $this->convertDateLocaleTR($row["servis_tarihi"]);

                echo '
                <tr>
                <td>' . $row['talep_eden'] . '</td>
                <td>' . $row['personel'] . '</td>
                <td>' . $reportDate . '</td>
                <td>' . $row['puan'] . '</td>
                <td> <div class="btn-group">
                <button type="button" class="btn btn-gradient-primary btn-sm" data-bs-toggle="dropdown">İşlem Seç</button>
                <div class="dropdown-menu">
                  <a href="satisfaction-detail.php?satisfaction_id=' . $row['id'] . '" class="dropdown-item">Detay</a>
                  <a href="" class="dropdown-item">Sil</a>
                </div>
                </td>
                </tr>';
            }
        }
    }
    public function getAllAntivirus($db)
    {

        $query = "SELECT * FROM Antivirusler ORDER BY firma_adi ASC";
        if ($result = $db->query($query)) {
            while ($row = $result->fetch_assoc()) {

                echo '
                <tr>
                <td>' . $row['firma_adi'] . '</td>
                <td><a href="' . $row['url'] . '">İndirme Linki</a></td>
                </tr>';
            }
        }
    }

    public function getAllServiceRequests($db)
    {

        $query = "SELECT * FROM Talepler WHERE talep_durum != 'Rapor Oluşturuldu'ORDER BY talep_tarihi DESC";
        if ($result = $db->query($query)) {
            while ($row = $result->fetch_assoc()) {

                $serviceDate = $this->convertDateLocaleTR($row["talep_tarihi"]);
                $description = substr($row['talep_aciklamasi'], 0, 65);
                echo '
                <tr>
                <td>' . $row['talep_eden'] . '</td>
                <td>' . $description . '</td>
                <td>' . $serviceDate . '</td>
                <td>' . $row['talep_durum'] . '</td>
                <td> <div class="btn-group">
                <button type="button" class="btn btn-gradient-primary btn-sm" data-bs-toggle="dropdown">İşlem Seç</button>
                <div class="dropdown-menu">
                  <a href="report-service.php?service_id=' . $row['id'] . '" class="dropdown-item">Durum Değiştir</a>
                  <a href="" class="dropdown-item">Sil</a>
                </div>
                </td>
                </tr>';
            }
        }
    }


    // CANLI DESTEK AREA

    public function getAllChats($db)
    {
        $query = "SELECT * FROM Gorusmeler INNER JOIN Users ON Gorusmeler.g_id = Users.user_id ORDER BY tarih_saat ASC";
        if ($result = $db->query($query)) {
            while ($row = $result->fetch_assoc()) {
                $date = $this->convertDateLocaleTR($row["tarih_saat"]);
                echo '
                <tr>
                <td>' . $row['email'] . '</td>
                <td>' . $$date . '</td>
                <td> <div class="btn-group">
                <button type="button" class="btn btn-gradient-primary btn-sm" data-bs-toggle="dropdown">İşlem Seç</button>
                <div class="dropdown-menu">
                  <a href="chat-detail.php?chat_id=' . $row['gorusme_id'] . '&alici_id=' . $row['a_id'] . '&gonderen_id=' . $row['g_id'] . '" class="dropdown-item">Görüntüle</a>
                  <a href="" class="dropdown-item">Sil</a>
                </div>
                </td>
                </tr>';
            }
        }
    }

    public function getAdminChatDetailWithId($db, $gorusme_id, $id)
    {
        $query = "SELECT * FROM Mesajlar INNER JOIN Users ON Mesajlar.gonderen_id = Users.user_id WHERE Mesajlar.gorusme_id = $gorusme_id AND Users.user_id= $id ORDER BY tarih_saat asc";
        if ($result = $db->query($query)) {
            while ($row = $result->fetch_assoc()) {


                echo '<div class="media media-chat media-chat-reverse">
                    <div class="media-body">
                      <p>' . $row['mesaj'] . '</p>
                      <p class="meta">10:00</p>
                    </div>
                  </div>';
            }
        }
    }
    public function getUserChatDetailWithId($db, $gorusme_id, $id)
    {
        $query = "SELECT * FROM Mesajlar INNER JOIN Users ON Mesajlar.gonderen_id = Users.user_id WHERE Mesajlar.gorusme_id = $gorusme_id AND Users.user_id= $id ORDER BY tarih_saat ASC";
        if ($result = $db->query($query)) {
            while ($row = $result->fetch_assoc()) {


                echo ' <div class="media media-chat">
                  <img class="avatar" src="https://img.icons8.com/color/36/000000/administrator-male.png" alt="...">
                  <div class="media-body">
                  <p>' . $row['mesaj'] . '</p>
                    <p class="meta">23:58</p>
                  </div>
                </div>
                    ';
            }
        }
    }



    public function getCustomerAllServiceRequests($db, $user_email)
    {

        $query = "SELECT * FROM Talepler INNER JOIN Firmalar ON Talepler.talep_eden_firma_id = Firmalar.firma_id WHERE firma_adi = '$user_email' ORDER BY talep_tarihi DESC ";
        if ($result = $db->query($query)) {
            while ($row = $result->fetch_assoc()) {

                $serviceDate = $this->convertDateLocaleTR($row["talep_tarihi"]);
                $description = substr($row['talep_aciklamasi'], 0, 65);
                echo '
                <tr>
                <td>' . $row['firma_adi'] . '</td>
                <td>' . $description . '</td>
                <td>' . $serviceDate . '</td>
                <td>' . $row['talep_durum'] . '</td>
                
                </tr>';
            }
        }
    }

    public function getCompanyIdWithEmail($db, $email)
    {
        $query = "SELECT * FROM Firmalar INNER JOIN Users ON Firmalar.firma_id = Users.company_id WHERE Users.email = '$email' ";
        if ($result = $db->query($query)) {
            while ($row = $result->fetch_assoc()) {
                return $row['firma_id'];
            }
        }
    }





    // CANLI DESTEK AREA FINAL
}
