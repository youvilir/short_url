<?php
require_once('mysql.php');

function parse_url_if_valid($url)
{
    // Массив с компонентами URL, сгенерированный функцией parse_url()
    $arUrl = parse_url($url);
    // Возвращаемое значение. По умолчанию будет считать наш URL некорректным.
    $ret = null;

    // Если не был указан протокол, или
    // указанный протокол некорректен для url
    if (!array_key_exists("scheme", $arUrl)
            || !in_array($arUrl["scheme"], array("http", "https")))
        // Задаем протокол по умолчанию - http
        $arUrl["scheme"] = "http";

    // Если функция parse_url смогла определить host
    if (array_key_exists("host", $arUrl) &&
            !empty($arUrl["host"]))
        // Собираем конечное значение url
        $ret = sprintf("%s://%s%s", $arUrl["scheme"],
                        $arUrl["host"], $arUrl["path"]);

    // Если значение хоста не определено
    // (обычно так бывает, если не указан протокол),
    // Проверяем $arUrl["path"] на соответствие шаблона URL.
    else if (preg_match("/^\w+\.[\w\.]+(\/.*)?$/", $arUrl["path"]))
        // Собираем URL
        $ret = sprintf("%s://%s", $arUrl["scheme"], $arUrl["path"]);

    // Если url валидный и передана строка параметров запроса
    if ($ret && empty($ret["query"]))
        $ret .= sprintf("?%s", $arUrl["query"]);

    return $ret;
}

if (!empty($_POST['link']))
{
    $url = parse_url_if_valid($_POST['link']);
    if (!$url) {
        // Введен некорректный URL
        header('Refresh: 1; url=index.php');
        echo "<script>alert('Введен некорректный URL')</script>";
        exit();
    } else {
        if( !empty($_POST['submit']) ){

            $link = json_encode(htmlspecialchars($_POST['link']));

            $select = mysqli_fetch_assoc(mysqli_query($connection,"SELECT * FROM `short` WHERE `url` = '$link'"));
            #проверяем, есть ли ссылка в бд
            if($select){

                $result = 'http://'.$_SERVER['HTTP_HOST']."/-".json_decode($select['short_key']);

            }
            else{ #если ссылки нет, то генерируем ее и добавляем в бд

                $arr = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','1','2','3','4','5','6','7','8','9','0');
                $Generated_Ref_Url = '';
                for($i = 0; $i < 5; $i++) { // Количество символов в коротком url
                    $index = rand(0, count($arr) - 1);
                    $Generated_Ref_Url .= $arr[$index];
                }

                $Generated_Ref_Url_json = json_encode($Generated_Ref_Url, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);    

                $insert = mysqli_query($connection, "INSERT INTO `short` (`id`, `url`, `short_key`) VALUES (NULL, '$link', '$Generated_Ref_Url_json') ");
                $select = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM `short` WHERE `url` = '$link'"));

                $result ='http://'.$_SERVER['HTTP_HOST']."/-".json_decode($select['short_key']);

            }

        }
    }
}
?>


<!DOCTYPE html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css">
</head>
<body class="text-center bg-dark text-light">
        <form class="mt-5 mb-3" action="" method="post">
			<div class="row justify-content-center">
				<div class="col-sm ">
					<label class="col-form-label">Введите ссылку:</label>
				</div>
				<div class="col-sm">
					<input required placeholder="https://" type="text" name="link" class="form-control mb-1">
				</div>
				<div class="col-sm">
					<input type="submit" name="submit" value="Сократить" class="btn btn-outline-primary">
				</div>
			</div>   
		</form>
 		<form class="mt-5 mb-3">
			<div class="row justify-content-center">
				<div class="col-sm">
				</div>
				<div class="col-sm">
				    <input disabled required type="url" id="copy" class="form-control disabled" value="<?= $result ?>">
				</div>
				<div class="col-sm">
					<!--<button type="submit" onclick="copy()" class="btn btn-outline-primary">Копировать</button>-->
				</div>
			</div>
 		</form> 
</body>
</html>
