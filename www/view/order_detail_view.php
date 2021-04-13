<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入明細</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'cart.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>購入明細</h1>
  <div class="container">

    <?php include VIEW_PATH . 'templates/messages.php'; ?>
      <table class="table table-bordered">
        <thead class="thead-light">
        <?php foreach($histories as $history) { ?>
        <?php print(h($history['order_id']));?>
        <?php print(h($history['order_date']));?>
        <?php print(h($history['TOTAL']));?>
        <?php } ?>
          <tr>
            <th>商品名</th>
            <th>購入時の商品価格</th>
            <th>購入数</th>
            <th>小計</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($details as $detail) { ?>
          <tr>
            <td><?php print(h($detail['name']));?></td>
            <td><?php print(h($detail['purchase_price']));?></td>
            <td><?php print(h($detail['purchase_amount']));?></td>
            <td><?php print(h($detail['purchase_amount'] * $detail['purchase_price']));?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
  </div>
</body>
</html>