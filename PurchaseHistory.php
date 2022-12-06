<?php
$dsn = "mysql:host=localhost;dbname=ideal;charset=utf8";
$username = "root";
$password = "";

// 小計
$sumVal = 0;


$a = 0;
try {
  $db = new PDO($dsn, $username, $password);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

  // カートから支払い済みの商品を取得
  $sql = "SELECT * FROM carts WHERE member_id = :member_id AND complete = :complete";

  $stmt = $db->prepare($sql);
  $stmt->bindValue(":member_id", 1, PDO::PARAM_INT);
  $stmt->bindValue(":complete", 1, PDO::PARAM_INT);
  $result = $stmt->execute();

  while ($row = $stmt->fetch()) {
    $id[] = $row["id"];
    $memberId = $row["member_id"];
    $date[] = $row["date"];
    $status[] = $row["status"];
    $method[] = $row["method"];
    $complete = $row["complete"];
  }

  for ($i = 1; $i <= count($id); $i++) {
    // カートIDから詳細取得
    $sql = "SELECT * FROM cart_stacks WHERE cart_id = :cart_id";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(":cart_id", $id[$i - 1], PDO::PARAM_INT);
    $result = $stmt->execute();

    while ($row = $stmt->fetch()) {
      $cartId[] = $row["cart_id"];
      $shopId[] = $row["shop_id"];
      $productId[] = $row["product_id"];
      $price[] = $row["price"];
      $quantity[] = $row["quantity"];
    }
  }
  for ($j = 1; $j <= count($shopId); $j++) {

    // 商品IDから詳細取得
    $sql = "SELECT * FROM products WHERE id = :product_id";

    $stmtDetail = $db->prepare($sql);
    $stmtDetail->bindValue(":product_id", $productId[$j - 1], PDO::PARAM_STR);
    $result = $stmtDetail->execute();

    while ($row = $stmtDetail->fetch()) {
      $name[] = $row["name"];
      $category[] = $row["category"];
      $size[] = $row["size"];
      $detail[] = $row["detail"];
      $stock[] = $row["stock"];
      $taxId[] = $row["tax_id"];
    }
  }
} catch (PDOException $ex) {
  print("DB接続に失敗しました。");
} finally {
  $db = null;
}

for ($i = 0; $i <= count($price) - 1; $i++) {
  $sumVal = $sumVal + $price[$i];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="./src/components/reset.css" />
  <link rel="stylesheet" href="./src/components/header.css" />
  <link rel="stylesheet" href="./src/components/main.css" />
  <link rel="stylesheet" href="./src/components/footer.css" />
  <title>Document</title>
  <style></style>
</head>

<style>
  .main_coin {
    text-align: center;
    border-bottom: 2px solid rgb(56, 55, 55);
    padding-bottom: 15px;
  }

  .main_cart_box {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px;
    margin-top: 50px;
    padding: 14px;

    border: 1px solid black;
  }

  .main_cart_box_left {
    width: 30%;
  }

  .main_cart_box_right {
    width: 40%;
  }

  .main_cart_box_right>p {
    margin: 15px;
  }

  .cart_title {
    color: rgb(76, 47, 221);
    font-size: 18px;
    font-weight: bold;
  }

  /* history */
  .main_history_title {
    padding-bottom: 20px;
  }

  .main_history_day {
    margin-top: 60px;
  }

  .main_contents_history_en {
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    padding: 40px 0px 10px 0px;
  }
</style>

<body>
  <!-- header -->
  <header>
    <p class="h_title">IDEAL</p>
  </header>

  <!-- main -->
  <main>
    <div class="main_contents">
      <h3 class="page_title main_history_title">購入履歴</h3>
      <?php // 異なる日付の数ループ
        for($d=0;$d<=count($date)-1;$d++){
      ?>
      <p class="main_coin main_history_day"><?php echo $date[$d]; ?></p>
      <p class="main_contents_history_en">小計 ￥<?php echo $sumVal; ?></p>
      <!-- 1個の商品 -->
      <?php for ($i = 0; $i <= count($name) - 1; $i++) { ?>
        <?php // 日付違う時の処理 
          if(){ ?>
        <div class="main_cart_box">
          <div class="main_cart_box_left">
            <img src="./src/img/image.png" class="main_cart_box_img" alt="" width="150 " height="150" />
          </div>
          <div class="main_cart_box_right">
            <p class="cart_title"><?php echo $name[$i]; ?></p>
            <p><?php echo $size[$i]; ?></p>
            <p><?php echo $price[$i]; ?></p>
          </div>
        </div>
        <?php }else{ ?>
        <?php } ?>
      <?php } ?>
      <!-- <div class="main_cart_box">
          <div class="main_cart_box_left">
            <img
              src="./src/img/image.png"
              class="main_cart_box_img"
              alt=""
              width="150 "
              height="150"
            />
          </div>
          <div class="main_cart_box_right">
            <p class="cart_title">sdaa</p>
            <p>PINK</p>
            <p>SIZE:M</p>
          </div>
        </div> -->
        <?php } ?>
      <!-- 2個目 -->
      <!-- <p class="main_coin main_history_day">2022/11/5</p>
      <p class="main_contents_history_en">小計 ￥60,590</p>
      <div class="main_cart_box">
        <div class="main_cart_box_left">
          <img src="./src/img/image.png" class="main_cart_box_img" alt="" width="150 " height="150" />
        </div>
        <div class="main_cart_box_right">
          <p class="cart_title">sdaa</p>
          <p>PINK</p>
          <p>SIZE:M</p>
        </div>
      </div>
      <div class="main_cart_box">
        <div class="main_cart_box_left">
          <img src="./src/img/image.png" class="main_cart_box_img" alt="" width="150 " height="150" />
        </div>
        <div class="main_cart_box_right">
          <p class="cart_title">sdaa</p>
          <p>PINK</p>
          <p>SIZE:M</p>
        </div>
      </div> -->
    </div>
  </main>

  <!-- footer -->
  <footer>
    <ul class="f_contents">
      <div class="f_list_content">
        <li>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 9v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9" />
            <path d="M9 22V12h6v10M2 10.6L12 2l10 8.6" />
          </svg>
        </li>
        <li>ホーム</li>
      </div>

      <div class="f_list_content">
        <li>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="10" r="3" />
            <path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 1 0-16 0c0 3 2.7 6.9 8 11.7z" />
          </svg>
        </li>
        <li>フロアマップ</li>
      </div>

      <div class="f_list_content">
        <li>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="10" cy="20.5" r="1" />
            <circle cx="18" cy="20.5" r="1" />
            <path d="M2.5 2.5h3l2.7 12.4a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.6l1.6-8.4H7.1" />
          </svg>
        </li>
        <li>カート</li>
      </div>

      <div class="f_list_content">
        <li>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
        </li>
        <li>マイページ</li>
      </div>
    </ul>
  </footer>
</body>

</html>