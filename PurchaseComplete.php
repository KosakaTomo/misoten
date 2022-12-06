<?php
$dsn = "mysql:host=localhost;dbname=ideal;charset=utf8";
$username = "root";
$password = "";

try {
  $db = new PDO($dsn, $username, $password);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

  // ユーザ情報
  $sql = "SELECT * FROM members WHERE id = :id";

  $stmt = $db->prepare($sql);
  $stmt->bindValue(":id", 1, PDO::PARAM_INT);
  $result = $stmt->execute();

  while ($row = $stmt->fetch()) {
    $id = $row["id"];
    $mail = $row["mail"];
    $password = $row["password"];
    $name = $row["name"];
    $zip = $row["zip"];
    $address = $row["address"];
    $tel = $row["tel"];
    $entry = $row["entry"];
  }

  // カートから支払い済みの商品を取得(直近のもの)
  $sql = "SELECT * FROM carts WHERE id = (SELECT MAX(id) FROM carts) AND member_id = :member_id AND status = :status";
  $stmt = $db->prepare($sql);
  $stmt->bindValue(":member_id", 1, PDO::PARAM_INT);
  $stmt->bindValue(":status", 1, PDO::PARAM_INT);
  $result = $stmt->execute();

  while ($row = $stmt->fetch()) {
    $id = $row["id"];
    $memberId = $row["member_id"];
    $date = $row["date"];
    $status = $row["status"];
    $method = $row["method"];
    $complete = $row["complete"];
    $boughtArray[] = [$row["id"], $row["member_id"], $row["date"], $row["status"], $row["method"], $row["complete"]];
  }

  // カートIDから詳細取得
  $sql = "SELECT * FROM cart_stacks WHERE cart_id = :cart_id";

  $stmt = $db->prepare($sql);
  $stmt->bindValue(":cart_id", $id, PDO::PARAM_INT);
  $result = $stmt->execute();

  while ($row = $stmt->fetch()) {
    $cartId[] = $row["cart_id"];
    $shopId[] = $row["shop_id"];
    $productId[] = $row["product_id"];
    $price[] = $row["price"];
    $quantity[] = $row["quantity"];
    $cartArray[] = [$row["cart_id"], $row["shop_id"], $row["product_id"], $row["price"], $row["quantity"]];
  }
  for ($j = 1; $j <= count($shopId); $j++) {

    // 商品IDから詳細取得
    $sql = "SELECT * FROM products WHERE id = :product_id";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(":product_id", $productId[$j - 1], PDO::PARAM_STR);
    $result = $stmt->execute();

    while ($row = $stmt->fetch()) {
      $proName[] = $row["name"];
      $category[] = $row["category"];
      $size[] = $row["size"];
      $detail[] = $row["detail"];
      $stock[] = $row["stock"];
      $taxId[] = $row["tax_id"];
      $detailArray[] = [$row["name"], $row["category"], $row["size"], $row["detail"], $row["stock"], $row["tax_id"]];
    }
  }
} catch (PDOException $ex) {
  print("DB接続に失敗しました。");
} finally {
  $db = null;
}

$sumVal = 0;
for ($i = 0; $i < count($price); $i++) {
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
  .comp_list_box {
    padding: 30px 0px;
  }

  /* .comp_list_box_end > dd {
      width: 30%;
    } */
</style>

<body>
  <!-- header -->
  <header>
    <p class="h_title">IDEAL</p>
  </header>

  <!-- main -->
  <main>
    <div class="main_contents">
      <div>
        <h3 class="comp_title">ありがとうございます。</h3>
        <p class="comp_sub"><?php echo $name; ?>様 ご注文を受け付けました。</p>
        <p class="comp_sub">
          以下の情報を控えておくことをお勧めします。<br />
          また、後ほど確認のEメールをお送りします。
        </p>
      </div>

      <hr />
      <dl class="comp_list">
        <div>
          <dt>ご注文番号</dt>
          <dd>000000<?php echo $id; ?></dd>
        </div>
        <div>
          <dt>ご注文日</dt>
          <dd><?php echo $date; ?></dd>
        </div>
        <div>
          <dt>合計金額</dt>
          <dd>¥　<?php echo $sumVal; ?> JPY</dd>
        </div>
        <div>
          <dt>お支払い方法</dt>
          <dd>PAYPAY</dd>
        </div>
        <div>
          <dt>配送方法</dt>
          <dd>HIKARU運送</dd>
        </div>
        <div>
          <dt>お届け先住所</dt>
          <dd>〒<?php echo $zip; ?> <?php echo $address; ?></dd>
        </div>
      </dl>
      <hr />

      <!--  -->
      <dl class="comp_list">
        <!-- 1個目一個目 -->
        <?php for($i=0;$i<count($proName);$i++){ ?>
        <div class="comp_list_box">
          <dt>製品</dt>
          <dd>
            <img src="./src/img/image.png" class="comp_list_img" alt="" />
          </dd>
        </div>
        <div>
          <dt>詳細</dt>
          <dd><?php echo $proName[$i]; ?></dd>
        </div>
        <div>
          <dt>価格</dt>
          <dd>¥ <?php echo $price[$i]; ?></dd>
        </div>
        <?php } ?>
        <!-- 2個目 -->
        <!-- <div class="comp_list_box">
          <dt>製品</dt>
          <dd>
            <img src="./src/img/image.png" class="comp_list_img" alt="" />
          </dd>
        </div>
        <div>
          <dt>詳細</dt>
          <dd>カルビー ポテトチップス うすしお味 1袋</dd>
        </div>
        <div>
          <dt>価格</dt>
          <dd>¥ 200.00</dd>
        </div>
      </dl>
      -->
      <hr />

      <!-- 確認 -->
      <dl class="">
        <!-- 1個目 -->
        <div class="comp_list_box_end">
          <div>
            <dt class="a">ショッピング合計</dt>
            <dd class="a"><?php echo count($proName); ?>製品</dd>
            <dd class="a">¥<?php echo $sumVal; ?></dd>
          </div>
        </div>
      </dl>

      <div class="btn_area">
        <div class="btn">
          <a href="#">ショッピングを続ける</a>
        </div>
      </div>
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