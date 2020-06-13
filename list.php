<?php
require_once 'functions.php';

$pdo = connectDB();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    // 画像を取得
    $sql = 'SELECT * FROM images ORDER BY created_at DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $images = $stmt->fetchAll();

} else {
    // 画像を保存
    if (!empty($_FILES['image']['name'])) {
        $name = $_FILES['image']['name'];
        $type = $_FILES['image']['type'];
        $content = file_get_contents($_FILES['image']['tmp_name']);
        $size = $_FILES['image']['size'];
        $coment = $_POST['coment'];
        $toukou = $_POST['toukou'];
        $house = $_POST['house'];

        $sql = 'INSERT INTO images(image_name, image_type, image_content, image_size, image_coment, image_toukou, image_house, created_at)
                VALUES (:image_name, :image_type, :image_content, :image_size, :coment, :toukou, :house, now())';

        // $sql = 'INSERT INTO images(image_name, image_type, image_content, image_size, created_at)
        // VALUES (:image_name, :image_type, :image_content, :image_size, now())';


$stmt = $pdo->prepare($sql);
        $stmt->bindValue(':image_name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':image_type', $type, PDO::PARAM_STR);
        $stmt->bindValue(':image_content', $content, PDO::PARAM_STR);
        $stmt->bindValue(':image_size', $size, PDO::PARAM_INT);
        $stmt->bindValue(':coment', $coment, PDO::PARAM_STR);
        $stmt->bindValue(':toukou', $toukou, PDO::PARAM_STR);
        $stmt->bindValue(':house', $house, PDO::PARAM_STR);
        $stmt->execute();
    }
    unset($pdo);
    header('Location:list.php');
    exit();
}

unset($pdo);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>Image Test</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <style>
      .text{
        margin-top: 20px;
      }
      /* .onamae{
      padding-top: 20px;
      } */
      
      .livehouse{
        width:250px;
      }

      .jumbotron {
        background-image: url("img/top00.jpg");
        background-size: cover;
        background-position: center 60%;
      }


    </style>
</head>
<body>

<div class="container text-center mt-5">
  <div class="text-center bg-dark h-100 pt-4 pb-4 jumbotron">
      <p class="display-3 center-block text-white">ライブハウス大作戦</p>
      <p class="display-4 text-white">with コロナ</p>
  </div>
</div>


<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 border-right">
            <ul class="list-unstyled">
                <?php for($i = 0; $i < count($images); $i++): ?>
                    <li class="media mt-5">
                        <a href="#lightbox" data-toggle="modal" data-slide-to="<?= $i; ?>">
                          <img src="image.php?id=<?= $images[$i]['image_id']; ?>" width="200px" height="auto" class="mr-3">
                        </a>
                        <div class="media-body">
                            <h6 class="onamae">名前：<?= $images[$i]['image_toukou']; ?></h6>
                            <h5 class="live"><?= $images[$i]['image_house']; ?></h5>
                            <!-- <h5><?= $images[$i]['image_name']; ?> (<?= number_format($images[$i]['image_size']/1000, 2); ?> KB)</h5> -->
                            <h6><?= $images[$i]['image_coment']; ?></h6>
                            <a href="javascript:void(0);" 
                                onclick="var ok = confirm('削除しますか？'); if (ok) location.href='delete.php?id=<?= $images[$i]['image_id']; ?>'">
                            <i class="far fa-trash-alt"></i> 削除</a>
                        </div>
                    </li>
                <?php endfor; ?>
            </ul>
        </div>
        <div class="col-md-4 pt-4 pl-4">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">

                    <label class="text">名前　</label>
                    <input name="toukou" type="text" class="livehouse" required></input>
                    <br>
                    <label class="text">ライブハウス名</label>
                    <input name="house" type="text" class="livehouse" required></input>
                    <br>                    
                    <label class="text">思い出画像</label>
                    <input type="file" name="image" required>

                    <label class="text">応援コメント入力</label>
                    <textArea name="coment" rows="4" cols="40"></textArea>
                </div>
                <button type="submit" class="btn btn-primary">保存</button>
            </form>

                    <div>
                    <h6 class="text-center mt-5 font-weight-bold">\\ お金で応援(500円) //</h6>

                        <div id="paypal-button-container"></div>
                        <script src="https://www.paypal.com/sdk/js?client-id=sb&currency=JPY" data-sdk-integration-source="button-factory"></script>
                        <script>
                        paypal.Buttons({
                            style: {
                                shape: 'pill',
                                color: 'blue',
                                layout: 'horizontal',
                                label: 'paypal',
                                
                            },
                            createOrder: function(data, actions) {
                                return actions.order.create({
                                    purchase_units: [{
                                        amount: {
                                            value: '1'
                                        }
                                    }]
                                });
                            },
                            onApprove: function(data, actions) {
                                return actions.order.capture().then(function(details) {
                                    alert('Transaction completed by ' + details.payer.name.given_name + '!');
                                });
                            }
                        }).render('#paypal-button-container');
                        </script>

                    </div>

        </div>
    </div>
</div>

<div class="modal carousel slide" id="lightbox" tabindex="-1" role="dialog" data-ride="carousel" style="position: fixed;">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
    <div class="modal-body">
        <ol class="carousel-indicators">
            <?php for ($i = 0; $i < count($images); $i++): ?>
                <li data-target="#lightbox" data-slide-to="<?= $i; ?>" <?php if ($i==0) echo 'class="active"'; ?>></li>
            <?php endfor; ?>
        </ol>
        <div class="carousel-inner">
            <?php for ($i = 0; $i < count($images); $i++): ?>
                <div class="carousel-item <?php if ($i==0) echo 'active'; ?>">
                  <img src="image.php?id=<?= $images[$i]['image_id']; ?>" class="d-block w-100">
                </div>
            <?php endfor; ?>
        </div>

        <a class="carousel-control-prev" href="#lightbox" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#lightbox" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>