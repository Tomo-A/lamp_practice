<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入履歴</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'cart.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>購入履歴</h1>
  <div class="container">

    <?php include VIEW_PATH . 'templates/messages.php'; ?>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>注文番号</th>
            <th>注文日時</th>
            <th>合計金額</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($histories as $history) { ?>
          <tr>
            <td><?php print(h($history['order_id']));?></td>
            <td><?php print(h($history['order_date']));?></td>
            <td><?php print(h($history['TOTAL']));?></td>
            <td>
                <form action="order_detail.php" method="get">
                    <input type="hidden" name="order_id" value="<?php print(h($history['order_id']))?>">
                    <input type="hidden" name="token" value="<?php print($token)?>">
                    <input type="submit" name="" value="購入明細">
                </form>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
  </div>
</body>
</html>