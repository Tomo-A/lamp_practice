<?php 
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
  ";
  return fetch_all_query($db, $sql,[$user_id]);
}

function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
    AND
      items.item_id = ?
  ";

  return fetch_query($db, $sql,[$user_id,$item_id]);

}

function get_admin_order_history($db){
  $sql = "
  SELECT
	  order_history.order_id,
    order_date,
    SUM(purchase_price * purchase_amount) AS TOTAL
  FROM
    order_history
  INNER JOIN
    order_detail
  ON
    order_history.order_id = order_detail.order_id
  GROUP BY
    order_history.order_id
  ORDER BY
    order_date DESC;
    ";
  
  return fetch_all_query($db, $sql);
}

function get_order_history($db, $user_id){
  $sql = "
  SELECT
	  order_history.order_id,
    order_date,
    SUM(purchase_price * purchase_amount) AS TOTAL
  FROM
    order_history
  INNER JOIN
    order_detail
  ON
    order_history.order_id = order_detail.order_id
  where
    user_id = ?
  GROUP BY
    order_history.order_id
  ORDER BY
    order_date DESC;
    ";
  
  return fetch_all_query($db, $sql, [$user_id]);
}

function get_admin_order_detail($db, $order_id){
  $sql = "
  SELECT
    order_detail.order_id,
    order_detail.item_id, 
    items.name,
    purchase_price, 
    purchase_amount
  FROM
    order_detail
  INNER JOIN 
    order_history
  ON 
    order_detail.order_id = order_history.order_id 
  INNER JOIN 
    items 
  ON 
    order_detail.item_id = items.item_id
  WHERE
    order_history.order_id = ?
  GROUP BY 
    order_detail.order_id,
    order_detail.item_id;
  ";

  return fetch_all_query($db, $sql, [$order_id]);
}

function get_order_detail($db, $user_id, $order_id){
  $sql = "
  SELECT
    order_detail.order_id,
    order_detail.item_id, 
    purchase_price, 
    purchase_amount,
    items.name
  FROM
    order_detail
  INNER JOIN 
    order_history
  ON 
    order_detail.order_id = order_history.order_id 
  INNER JOIN 
    items 
  ON 
    order_detail.item_id = items.item_id 
  WHERE
    order_history.user_id = ?
  AND
    order_history.order_id = ?
  GROUP BY 
    order_detail.order_id,
    order_detail.item_id;
  ";

  return fetch_all_query($db, $sql, [$user_id, $order_id]);
}

function get_fetch_admin_order_history($db, $order_id){
  $sql = "
  SELECT
	  order_history.order_id,
    order_date,
    SUM(purchase_price * purchase_amount) AS TOTAL
  FROM
    order_history
  INNER JOIN
    order_detail
  ON
    order_history.order_id = order_detail.order_id
  WHERE
    order_history.order_id = ?
  GROUP BY
    order_history.order_id;
    ";
  
  return fetch_query($db, $sql, [$order_id]);
}

function get_fetch_order_history($db, $user_id, $order_id){
  $sql = "
  SELECT
	  order_history.order_id,
    order_date,
    SUM(purchase_price * purchase_amount) AS TOTAL
  FROM
    order_history
  INNER JOIN
    order_detail
  ON
    order_history.order_id = order_detail.order_id
  where
    user_id = ?
  AND
    order_history.order_id = ?
  GROUP BY
    order_history.order_id;
    ";
  
  return fetch_query($db, $sql, [$user_id, $order_id]);
}

function add_cart($db, $user_id, $item_id ) {
  $cart = get_user_cart($db, $user_id, $item_id);
  if($cart === false){
    return insert_cart($db, $user_id, $item_id);
  }
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(?, ?, ?)
  ";

  return execute_query($db, $sql,[$item_id,$user_id,$amount]);
}

function insert_order_history($db, $user_id){
  $sql = "
    INSERT INTO
      order_history(
        user_id,
        order_date
      )
    VALUES(?, now())
  ";
  return execute_query($db, $sql, [$user_id]);

}

function insert_order_detail($db, $item_id, $price, $order_id, $amount){
  $sql = "
    INSERT INTO
     order_detail(
       order_id,
       item_id,
       purchase_price,
       purchase_amount
     )
    VALUES(?, ?, ?, ?)
  ";

  return execute_query($db, $sql, [$order_id, $item_id, $price, $amount]);
}

function make_history_detail($db, $carts){
  if(insert_order_history($db, $carts[0]['user_id']) === false){
    set_error('購入履歴の作成に失敗しました。');
    return false;
  }
  $order_id = $db->lastInsertId();
  foreach($carts as $cart){
    if(insert_order_detail(
      $db,
      $cart['item_id'],
      $cart['price'],
      $order_id,
      $cart['amount']
     ) === false){
       set_error('購入明細の作成に失敗しました。');
       return false;
     }
  }
}

function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = ?
    WHERE
      cart_id = ?
    LIMIT 1
  ";
  return execute_query($db, $sql,[$amount,$cart_id]);
}

function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = ?
    LIMIT 1
  ";

  return execute_query($db, $sql,[$cart_id]);
}

function purchase_carts($db, $carts){
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  $db->beginTransaction();
  make_history_detail($db, $carts);
  foreach($carts as $cart){
    if(update_item_stock(
        $db, 
        $cart['item_id'], 
        $cart['stock'] - $cart['amount']
      ) === false){
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
  }
  
  delete_user_carts($db, $carts[0]['user_id']);

  if(has_error() === true){
    $db->rollback();
  }else{
    $db->commit();
  }
  


}

function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = ?
  ";

  execute_query($db, $sql,[$user_id]);
}


function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

function validate_cart_purchase($carts){
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  foreach($carts as $cart){
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  if(has_error() === true){
    return false;
  }
  return true;
}

