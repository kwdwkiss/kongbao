<?php
$file_path = './Uploads/csv/';
if (!is_dir($file_path)) {
    mkdir($file_path, 0777, true);
}
$file_up = $file_path . date('YmdHis') . mt_rand(11, 999) . '.csv';
header("Content-type:text/html;charset=gbk");
if (move_uploaded_file($_FILES['upload']['tmp_name'], $file_up)) {

    $file = fopen($file_up, 'r');
    while ($data = fgetcsv($file)) { //每次读取CSV里面的一行内容
        $goods_list[] = $data;
    }
    //print_r($goods_list);
    $i = 0;
    foreach ($goods_list as $arr) {
        $i++;
        if ($i == 1) {
            continue;
        }
        echo $arr[12] . ',' . str_replace("'", '', $arr[16]) . ',' . $arr[13] . ",000000&#13;&#10;";

        foreach ($arr as $v) {
            // echo $v.'====';
        }
    }
    fclose($file);
} else {
    echo 'fail';
}
?>